<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Ai\Agents\SiteAgent;
use App\Filament\Dashboard\Resources\SiteResource;
use App\Models\AiTokenUsage;
use App\Services\Plans\SubscriptionLimitChecker;
use App\Services\SiteMdService;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\ConversationStore;

class AgentMode extends ViewRecord
{
    protected static string $resource = SiteResource::class;
    protected string $view = 'site.single.agent-mode';

    // Livewire state
    public array $messages = [];
    public string $userMessage = '';
    public bool $loading = false;
    public string $aiError = '';
    public string $siteMdAge = '';
    public int $tokensUsed = 0;
    public int $tokensLimit = AiTokenUsage::MONTHLY_LIMIT;
    public ?string $conversationId = null;
    public array $conversations = [];

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! SubscriptionLimitChecker::canUseAi()) {
            $this->redirect(static::getResource()::getUrl('view', [
                'record' => $record,
                'tenant' => Filament::getTenant(),
            ]));
            return;
        }

        $this->siteMdAge = $this->getSiteMdAge();

        $tenant = Filament::getTenant();
        $this->tokensUsed = AiTokenUsage::monthlyUsage($tenant->uuid);
        $this->tokensLimit = AiTokenUsage::MONTHLY_LIMIT;

        // Load the last conversation for this site if any
        $this->loadConversationHistory();

        // Try to resume last conversation
        $sessionKey = 'agent_conversation_' . $this->record->id;
        $this->conversationId = session($sessionKey);

        if ($this->conversationId) {
            $this->loadMessages();
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): HtmlString => new HtmlString($this->agentStyles()),
            scopes: [static::class],
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): HtmlString => new HtmlString($this->agentScript()),
            scopes: [static::class],
        );
    }

    /**
     * Called after a streaming response completes to sync state.
     */
    public function syncAfterStream(string $conversationId, int $tokensUsed): array
    {
        $this->conversationId = $conversationId;
        $this->tokensUsed = $tokensUsed;

        $sessionKey = 'agent_conversation_' . $this->record->id;
        session([$sessionKey => $conversationId]);

        $this->loadConversationHistory();

        return [
            'conversations' => $this->conversations,
            'tokensUsed' => $this->tokensUsed,
        ];
    }

    /**
     * Get the SSE stream URL for the frontend.
     */
    public function getStreamUrl(): string
    {
        $tenant = Filament::getTenant();
        return route('agent.stream', [
            'tenant' => $tenant->uuid,
            'site' => $this->record->id,
        ]);
    }

    /**
     * Start a fresh conversation (preserves old ones in history).
     */
    public function startNewConversation(): array
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->aiError = '';

        $sessionKey = 'agent_conversation_' . $this->record->id;
        session()->forget($sessionKey);

        // Refresh the conversation list so the old one appears
        $this->loadConversationHistory();

        return ['ok' => true, 'conversations' => $this->conversations];
    }

    /**
     * Resume a previous conversation.
     */
    public function resumeConversation(string $conversationId): array
    {
        $this->conversationId = $conversationId;
        $this->messages = [];
        $this->aiError = '';

        $sessionKey = 'agent_conversation_' . $this->record->id;
        session([$sessionKey => $conversationId]);

        $this->loadMessages();
        $this->loadConversationHistory();

        return [
            'ok' => true,
            'messages' => $this->messages,
            'conversations' => $this->conversations,
        ];
    }

    /**
     * Refresh SITE.md context.
     */
    public function refreshSiteMd(): array
    {
        try {
            app(SiteMdService::class)->write($this->record);
            $this->siteMdAge = $this->getSiteMdAge();
            return ['ok' => true, 'age' => $this->siteMdAge];
        } catch (\Throwable $e) {
            Log::error('Agent Mode SITE.md refresh failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Load messages from the persisted conversation.
     */
    private function loadMessages(): void
    {
        if (! $this->conversationId) {
            return;
        }

        try {
            $store = app(ConversationStore::class);
            $msgs = $store->getLatestConversationMessages($this->conversationId, 100);

            $this->messages = [];
            foreach ($msgs as $msg) {
                // Skip tool result messages in the display
                if ($msg instanceof \Laravel\Ai\Messages\ToolResultMessage) {
                    continue;
                }

                $role = ($msg instanceof \Laravel\Ai\Messages\AssistantMessage) ? 'assistant' : $msg->role->value;

                $content = $msg->content ?? '';
                if (empty(trim($content))) {
                    continue;
                }

                $html = $role === 'assistant'
                    ? (string) Str::of($content)->markdown(['html_input' => 'escape', 'allow_unsafe_links' => false])
                    : '';

                $this->messages[] = [
                    'role' => $role,
                    'content' => $content,
                    'html' => $html,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to load conversation messages: ' . $e->getMessage());
        }
    }

    /**
     * Delete a conversation.
     */
    public function deleteConversation(string $conversationId): array
    {
        try {
            \DB::table('agent_conversation_messages')
                ->where('conversation_id', $conversationId)
                ->delete();
            \DB::table('agent_conversations')
                ->where('id', $conversationId)
                ->where('user_id', auth()->id())
                ->delete();

            // If we just deleted the active conversation, reset
            if ($this->conversationId === $conversationId) {
                $this->conversationId = null;
                $this->messages = [];
                $sessionKey = 'agent_conversation_' . $this->record->id;
                session()->forget($sessionKey);
            }

            $this->loadConversationHistory();
            return ['ok' => true, 'conversations' => $this->conversations];
        } catch (\Throwable $e) {
            Log::error('Failed to delete conversation: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Load the list of previous conversations for this site.
     * Filters to only show conversations that used the SiteAgent.
     */
    private function loadConversationHistory(): void
    {
        try {
            $userId = auth()->id();
            $agentClass = SiteAgent::class;

            $this->conversations = \DB::table('agent_conversations')
                ->where('agent_conversations.user_id', $userId)
                ->whereExists(function ($query) use ($agentClass) {
                    $query->select(\DB::raw(1))
                        ->from('agent_conversation_messages')
                        ->whereColumn('agent_conversation_messages.conversation_id', 'agent_conversations.id')
                        ->where('agent_conversation_messages.agent', $agentClass);
                })
                ->orderByDesc('agent_conversations.updated_at')
                ->limit(30)
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'title' => Str::limit($c->title ?? 'Conversation', 40),
                    'updated_at' => \Carbon\Carbon::parse($c->updated_at)->diffForHumans(),
                    'active' => $c->id === $this->conversationId,
                ])
                ->toArray();
        } catch (\Throwable $e) {
            $this->conversations = [];
        }
    }

    private function getSiteMdAge(): string
    {
        $path = storage_path('app/sites/' . $this->record->id . '/SITE.md');
        if (! file_exists($path)) {
            return 'never generated';
        }
        return \Carbon\Carbon::createFromTimestamp(filemtime($path))->diffForHumans();
    }

    private function agentScript(): string
    {
        return <<<'JS'
        <script>
        function agentChat() {
            return {
                messages: [],
                draft: '',
                loading: false,
                refreshing: false,
                limitReached: false,
                siteMdAge: '',
                tokensUsed: 0,
                tokensLimit: 5000000,
                conversationId: null,
                conversations: [],
                showHistory: false,
                currentActivity: '',
                streamUrl: '',
                suggestions: [
                    'Which plugins have updates available?',
                    'Are there any security vulnerabilities?',
                    'Clear all caches on this site',
                    'Show me the uptime status',
                    'List all inactive plugins',
                    'Run a full site sync',
                ],

                get usagePercent() {
                    return this.tokensLimit > 0 ? (this.tokensUsed / this.tokensLimit) * 100 : 0;
                },
                get usageLabel() {
                    const fmt = (n) => n >= 1000000 ? (n / 1000000).toFixed(1) + 'M' : n >= 1000 ? (n / 1000).toFixed(0) + 'K' : String(n);
                    return fmt(this.tokensUsed) + ' / ' + fmt(this.tokensLimit) + ' tokens';
                },

                init(rootEl) {
                    const seed = document.getElementById('agent-chat-seed');
                    if (seed) {
                        try { this.messages = JSON.parse(seed.dataset.messages || '[]'); } catch(e) { this.messages = []; }
                        this.siteMdAge = seed.dataset.age || '';
                        this.tokensUsed = parseInt(seed.dataset.tokensUsed || '0', 10);
                        this.tokensLimit = parseInt(seed.dataset.tokensLimit || '5000000', 10);
                        this.conversationId = seed.dataset.conversationId || null;
                        try { this.conversations = JSON.parse(seed.dataset.conversations || '[]'); } catch(e) { this.conversations = []; }
                        this.limitReached = this.tokensUsed >= this.tokensLimit;
                        this.streamUrl = seed.dataset.streamUrl || '';
                    }
                    this.$nextTick(() => this.scrollToBottom());
                },

                handleKeydown(e) {
                    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); this.submit(); }
                },

                async submit() {
                    const text = this.draft.trim();
                    if (!text || this.loading) return;
                    this.messages.push({ role: 'user', content: text, html: '' });
                    this.draft = '';
                    this.loading = true;
                    this.currentActivity = '';
                    this.$nextTick(() => {
                        if (this.$refs.input) this.$refs.input.style.height = 'auto';
                        this.scrollToBottom();
                    });

                    // Build the assistant message placeholder
                    const assistantMsg = {
                        role: 'assistant',
                        content: '',
                        html: '',
                        tools: '',
                        toolsList: [],
                        steps: 0,
                        reasoning: '',
                        isStreaming: true,
                    };
                    this.messages.push(assistantMsg);
                    const msgIdx = this.messages.length - 1;

                    try {
                        const response = await fetch(this.streamUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                                'Accept': 'text/event-stream',
                            },
                            body: JSON.stringify({
                                message: text,
                                conversation_id: this.conversationId,
                            }),
                        });

                        if (!response.ok) {
                            const errText = await response.text();
                            throw new Error(errText || `HTTP ${response.status}`);
                        }

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();
                        let buffer = '';

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            const lines = buffer.split('\n');
                            buffer = lines.pop(); // keep incomplete line

                            for (const line of lines) {
                                if (!line.startsWith('data: ')) continue;
                                const payload = line.slice(6).trim();
                                if (payload === '[DONE]') continue;

                                let evt;
                                try { evt = JSON.parse(payload); } catch { continue; }

                                this.handleStreamEvent(evt, msgIdx);
                            }
                        }
                    } catch (err) {
                        this.messages[msgIdx].html = '<p class="text-red-400">' + this.escapeHtml(err.message) + '</p>';
                        this.messages[msgIdx].content = err.message;
                    } finally {
                        this.loading = false;
                        this.currentActivity = '';
                        this.messages[msgIdx].isStreaming = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                handleStreamEvent(evt, msgIdx) {
                    const msg = this.messages[msgIdx];
                    switch (evt.type) {
                        case 'stream_start':
                            this.currentActivity = 'Connecting to ' + (evt.model || 'AI') + '...';
                            break;

                        case 'reasoning_start':
                            this.currentActivity = 'Thinking...';
                            msg.reasoning = '';
                            break;

                        case 'reasoning_delta':
                            msg.reasoning += evt.delta;
                            this.currentActivity = 'Thinking: ' + msg.reasoning.slice(-60);
                            break;

                        case 'reasoning_end':
                            this.currentActivity = '';
                            break;

                        case 'tool_call':
                            msg.toolsList.push({ name: evt.tool_name, status: 'running', result: null });
                            msg.tools = msg.toolsList.map(t => (t.status === 'running' ? '\u23F3' : '\u2705') + ' ' + t.name).join(', ');
                            msg.steps++;
                            this.currentActivity = 'Running ' + this.formatToolName(evt.tool_name) + '...';
                            this.$nextTick(() => this.scrollToBottom());
                            break;

                        case 'tool_result':
                            const tool = msg.toolsList.find(t => t.name === evt.tool_name && t.status === 'running');
                            if (tool) {
                                tool.status = evt.successful ? 'done' : 'error';
                                tool.result = evt.result;
                            }
                            msg.tools = msg.toolsList.map(t => (t.status === 'running' ? '\u23F3' : t.status === 'done' ? '\u2705' : '\u274C') + ' ' + t.name).join(', ');
                            this.currentActivity = evt.successful
                                ? this.formatToolName(evt.tool_name) + ' complete'
                                : this.formatToolName(evt.tool_name) + ' failed';
                            break;

                        case 'text_start':
                            this.currentActivity = 'Writing response...';
                            break;

                        case 'text_delta':
                            msg.content += evt.delta;
                            // Render markdown incrementally
                            msg.html = this.renderMarkdown(msg.content);
                            this.currentActivity = '';
                            this.$nextTick(() => this.scrollToBottom());
                            break;

                        case 'text_end':
                            msg.html = this.renderMarkdown(msg.content);
                            break;

                        case 'stream_end':
                            // Final render
                            msg.html = this.renderMarkdown(msg.content);
                            break;

                        case 'done':
                            if (evt.conversation_id) this.conversationId = evt.conversation_id;
                            if (evt.tokens_used !== undefined) {
                                this.tokensUsed = evt.tokens_used;
                                this.limitReached = this.tokensUsed >= this.tokensLimit;
                            }
                            // Sync with Livewire to update conversation list
                            if (evt.conversation_id) {
                                this.$wire.syncAfterStream(evt.conversation_id, evt.tokens_used || 0)
                                    .then(r => {
                                        if (r && r.conversations) this.conversations = r.conversations;
                                    });
                            }
                            break;

                        case 'error':
                            msg.html = '<p class="text-red-400">' + this.escapeHtml(evt.message) + '</p>';
                            msg.content = evt.message;
                            break;
                    }
                },

                formatToolName(name) {
                    // Convert PascalCase/snake_case to readable
                    return name.replace(/([A-Z])/g, ' $1').replace(/_/g, ' ').trim().replace(/^ /, '');
                },

                renderMarkdown(text) {
                    if (!text) return '';

                    // Extract fenced code blocks to protect them from processing
                    const codeBlocks = [];
                    let src = text.replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) => {
                        codeBlocks.push('<pre class="language-' + (lang||'text') + '"><code>' + this.escapeHtml(code.replace(/\n$/, '')) + '</code></pre>');
                        return '\x00CB' + (codeBlocks.length - 1) + '\x00';
                    });

                    // Pre-split: ensure headers always get their own block.
                    // Insert a double-newline before any line starting with # headers
                    // so that headers don't merge with the paragraph above them.
                    src = src.replace(/\n(#{1,6} )/g, '\n\n$1');
                    // Also ensure a double-newline AFTER a header line
                    src = src.replace(/^(#{1,6} .+)$/gm, (match) => match + '\n');

                    // Ensure table blocks start after a blank line when preceded by non-table text.
                    src = src.replace(/^(.+)\n(\|)/gm, (m, prev, pipe) => {
                        if (prev.trim().startsWith('|')) return m; // already in table
                        return prev + '\n\n' + pipe;
                    });

                    // Ensure list blocks start after a blank line when preceded by non-list text.
                    // Only insert break when the preceding line is NOT a list item itself.
                    src = src.replace(/^(.+)\n(- )/gm, (m, prev, dash) => {
                        if (/^\s*- /.test(prev) || /^\s*\d+\. /.test(prev) || /^#{1,4} /.test(prev)) return m;
                        return prev + '\n\n' + dash;
                    });
                    src = src.replace(/^(.+)\n(\d+\. )/gm, (m, prev, num) => {
                        if (/^\s*- /.test(prev) || /^\s*\d+\. /.test(prev) || /^#{1,4} /.test(prev)) return m;
                        return prev + '\n\n' + num;
                    });

                    // Split into blocks on double-newline
                    const blocks = src.split(/\n{2,}/);

                    const rendered = blocks.map(block => {
                        block = block.trim();
                        if (!block) return '';

                        // Restore code blocks that are alone in a block
                        if (/^\x00CB\d+\x00$/.test(block)) {
                            return block.replace(/\x00CB(\d+)\x00/, (_, i) => codeBlocks[+i]);
                        }

                        // Apply inline formatting
                        let h = this.escapeHtml(block);

                        // Restore inline code blocks within text
                        h = h.replace(/\x00CB(\d+)\x00/g, (_, i) => codeBlocks[+i]);

                        // Inline code
                        h = h.replace(/`([^`]+)`/g, '<code>$1</code>');
                        // Bold
                        h = h.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
                        // Italic
                        h = h.replace(/(?<![*])\*([^*]+)\*(?![*])/g, '<em>$1</em>');

                        // --- Block-type detection ---

                        // Headers (any level # through ######)
                        if (/^#{1,6} /.test(block)) {
                            h = h.replace(/^###### (.+)$/gm, '<h6 class="font-medium text-xs mt-2 mb-1">$1</h6>');
                            h = h.replace(/^##### (.+)$/gm, '<h5 class="font-medium text-sm mt-2 mb-1">$1</h5>');
                            h = h.replace(/^#### (.+)$/gm, '<h4 class="font-semibold mt-3 mb-1">$1</h4>');
                            h = h.replace(/^### (.+)$/gm, '<h4 class="font-semibold mt-3 mb-1">$1</h4>');
                            h = h.replace(/^## (.+)$/gm, '<h3 class="font-semibold text-base mt-4 mb-1">$1</h3>');
                            h = h.replace(/^# (.+)$/gm, '<h2 class="font-bold text-lg mt-4 mb-2">$1</h2>');
                            return h;
                        }

                        // Table block: first non-empty line starts with |
                        const firstLine = block.split('\n').find(l => l.trim());
                        if (firstLine && firstLine.trim().startsWith('|')) {
                            const rows = h.split('\n').filter(r => r.trim());
                            let isFirst = true;
                            const tableRows = rows.map(row => {
                                const cells = row.split('|').filter(c => c.trim() !== '').map(c => c.trim());
                                if (cells.every(c => /^[-:]+$/.test(c))) { isFirst = false; return ''; }
                                const tag = isFirst ? 'th' : 'td';
                                isFirst = false;
                                return '<tr>' + cells.map(c => '<'+tag+' class="border border-gray-200 dark:border-white/10 px-2 py-1">'+c+'</'+tag+'>').join('') + '</tr>';
                            }).filter(Boolean).join('');
                            return '<table class="w-full border-collapse text-xs my-2">' + tableRows + '</table>';
                        }

                        // Bullet list: lines start with - (allow indented sub-items)
                        if (/^- /m.test(block) && block.split('\n').every(l => !l.trim() || /^\s*- /.test(l))) {
                            const items = h.split('\n').filter(l => l.trim()).map(l => {
                                const indent = /^\s+- /.test(l);
                                const text = l.replace(/^\s*- /, '');
                                return indent
                                    ? '<li class="ml-4 list-[circle]">' + text + '</li>'
                                    : '<li>' + text + '</li>';
                            }).join('');
                            return '<ul class="list-disc ml-4 space-y-0.5 my-1">' + items + '</ul>';
                        }

                        // Numbered list: lines start with digit. (allow indented sub-items with -)
                        if (/^\d+\. /m.test(block) && block.split('\n').every(l => !l.trim() || /^\s*\d+\. /.test(l) || /^\s+- /.test(l))) {
                            const items = h.split('\n').filter(l => l.trim()).map(l => {
                                if (/^\s+- /.test(l)) {
                                    return '<li class="ml-4 list-disc">' + l.replace(/^\s*- /, '') + '</li>';
                                }
                                return '<li>' + l.replace(/^\s*\d+\.\s*/, '') + '</li>';
                            }).join('');
                            return '<ol class="list-decimal ml-4 space-y-0.5 my-1">' + items + '</ol>';
                        }

                        // Regular paragraph: single newlines become <br>
                        h = h.replace(/\n/g, '<br>');
                        return '<p class="mb-2 last:mb-0">' + h + '</p>';
                    }).filter(Boolean);

                    return rendered.join('\n');
                },

                newConversation() {
                    this.$wire.startNewConversation().then((r) => {
                        if (r && r.ok) {
                            this.messages = [];
                            this.conversationId = null;
                            if (r.conversations) this.conversations = r.conversations;
                        }
                    });
                },

                resumeConversation(id) {
                    if (this.conversationId === id) return;
                    this.loading = true;
                    this.$wire.resumeConversation(id).then((r) => {
                        this.loading = false;
                        if (r && r.ok) {
                            this.messages = r.messages || [];
                            this.conversationId = id;
                            if (r.conversations) this.conversations = r.conversations;
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }).catch(() => { this.loading = false; });
                },

                deleteConversation(id, e) {
                    if (e) e.stopPropagation();
                    if (!confirm('Delete this conversation?')) return;
                    this.$wire.deleteConversation(id).then((r) => {
                        if (r && r.ok) {
                            if (r.conversations) this.conversations = r.conversations;
                            if (this.conversationId === id) {
                                this.messages = [];
                                this.conversationId = null;
                            }
                        }
                    });
                },

                toggleHistory() {
                    this.showHistory = !this.showHistory;
                },

                refreshContext() {
                    if (this.refreshing) return;
                    this.refreshing = true;
                    this.$wire.refreshSiteMd()
                        .then((r) => {
                            this.refreshing = false;
                            if (r && r.ok) { this.siteMdAge = r.age; }
                        })
                        .catch(() => { this.refreshing = false; });
                },

                fillSuggestion(s) {
                    this.draft = s;
                    this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
                },

                escapeHtml(str) {
                    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                },

                autoResize(el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 140) + 'px';
                },

                scrollToBottom() {
                    const el = this.$refs.msgList;
                    if (el) el.scrollTop = el.scrollHeight;
                },
            };
        }
        </script>
        JS;
    }

    private static function agentStyles(): string
    {
        return <<<'CSS'
        <style>
        @keyframes agent-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: .4; }
            40%           { transform: translateY(-6px); opacity: 1; }
        }
        @keyframes agent-fadein {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .agent-dot { animation: agent-bounce 1.2s infinite ease-in-out; }
        .agent-dot:nth-child(1) { animation-delay: 0s; }
        .agent-dot:nth-child(2) { animation-delay: .2s; }
        .agent-dot:nth-child(3) { animation-delay: .4s; }
        .agent-msg { animation: agent-fadein .22s ease-out both; }
        .agent-bubble                { display: block !important; }
        .agent-bubble p             { display: block; margin: 0 0 .5rem; }
        .agent-bubble p:last-child  { margin-bottom: 0; }
        .agent-bubble h2, .agent-bubble h3, .agent-bubble h4, .agent-bubble h5, .agent-bubble h6 { display: block; }
        .agent-bubble pre           { background: #1e1e2e; color: #cdd6f4; border-radius: .5rem; padding: .75rem 1rem; overflow-x: auto; font-size: .78rem; margin: .4rem 0; }
        .agent-bubble code          { font-size: .78rem; }
        .agent-bubble ul, .agent-bubble ol { display: block !important; margin: .25rem 0 .5rem 1.2rem; padding-left: 1rem; }
        .agent-bubble ul             { list-style-type: disc !important; }
        .agent-bubble ol             { list-style-type: decimal !important; }
        .agent-bubble li             { display: list-item !important; margin-bottom: .15rem; }
        .agent-bubble table         { display: table !important; width: 100%; border-collapse: collapse; font-size: .78rem; margin: .4rem 0; }
        .agent-bubble tr            { display: table-row !important; }
        .agent-bubble th, .agent-bubble td { display: table-cell !important; border: 1px solid #e5e7eb; padding: .3rem .6rem; text-align: left; }
        .dark .agent-bubble th, .dark .agent-bubble td { border-color: rgba(255,255,255,.1); }
        .agent-bubble th            { background: rgba(0,0,0,.03); font-weight: 600; }
        .dark .agent-bubble th      { background: rgba(255,255,255,.05); }
        .agent-tool-badge { display: inline-flex; align-items: center; gap: 4px; background: rgba(139,92,246,.15); color: #a78bfa; border-radius: 6px; padding: 2px 8px; font-size: 11px; font-weight: 500; margin-bottom: 6px; }

        /* History sidebar */
        .agent-history-sidebar {
            width: 240px;
            min-width: 240px;
            border-right: 1px solid rgba(229,231,235,.6);
            display: flex;
            flex-direction: column;
            background: #fafafa;
            border-radius: 0.75rem 0 0 0.75rem;
        }
        .dark .agent-history-sidebar {
            background: rgba(17,24,39,.6);
            border-color: rgba(255,255,255,.06);
        }
        .agent-history-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            font-size: 13px;
            color: #6b7280;
            border-radius: 8px;
            margin: 0 6px;
            cursor: pointer;
            transition: background .12s, color .12s;
            text-decoration: none;
        }
        .agent-history-item:hover {
            background: rgba(139,92,246,.08);
            color: #4b5563;
        }
        .dark .agent-history-item { color: #9ca3af; }
        .dark .agent-history-item:hover { background: rgba(139,92,246,.15); color: #e5e7eb; }
        .agent-history-item.active {
            background: rgba(139,92,246,.12);
            color: #7c3aed;
            font-weight: 500;
        }
        .dark .agent-history-item.active {
            background: rgba(139,92,246,.2);
            color: #a78bfa;
        }
        .agent-history-item .delete-btn {
            opacity: 0;
            margin-left: auto;
            flex-shrink: 0;
            padding: 2px;
            border-radius: 4px;
            transition: opacity .12s, background .12s;
        }
        .agent-history-item:hover .delete-btn { opacity: .6; }
        .agent-history-item .delete-btn:hover { opacity: 1; background: rgba(239,68,68,.15); color: #ef4444; }
        </style>
        CSS;
    }
}
