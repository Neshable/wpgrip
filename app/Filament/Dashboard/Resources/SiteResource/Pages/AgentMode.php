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
     * Send a message to the AI agent.
     */
    public function sendMessage(string $text): array
    {
        $text = trim($text);
        if (empty($text)) {
            return ['error' => 'Empty message.'];
        }

        $tenant = Filament::getTenant();
        if (AiTokenUsage::hasExceededLimit($tenant->uuid)) {
            return [
                'error' => 'Monthly AI token limit reached (5M tokens). Resets on the 1st of next month.',
                'limitReached' => true,
            ];
        }

        $this->aiError = '';

        try {
            $agent = new SiteAgent($this->record);
            $user = auth()->user();

            if ($this->conversationId) {
                $agent->continue($this->conversationId, as: $user);
            } else {
                $agent->forUser($user);
            }

            $response = $agent->prompt($text);

            // Store conversation ID
            if ($response->conversationId) {
                $this->conversationId = $response->conversationId;
                $sessionKey = 'agent_conversation_' . $this->record->id;
                session([$sessionKey => $this->conversationId]);
            }

            $reply = $response->text;

            // Update token usage display
            $this->tokensUsed = AiTokenUsage::monthlyUsage($tenant->uuid);

            $html = (string) Str::of($reply)
                ->markdown(['html_input' => 'escape', 'allow_unsafe_links' => false]);

            // Collect tool usage info
            $toolsUsed = [];
            foreach ($response->toolCalls as $toolCall) {
                $toolsUsed[] = [
                    'name' => $toolCall->name,
                    'result' => null,
                ];
            }
            foreach ($response->toolResults as $i => $result) {
                if (isset($toolsUsed[$i])) {
                    $toolsUsed[$i]['result'] = Str::limit((string) ($result->content ?? ''), 200);
                }
            }

            // Refresh conversations list
            $this->loadConversationHistory();

            return [
                'content' => $reply,
                'html' => $html,
                'tokensUsed' => $this->tokensUsed,
                'conversationId' => $this->conversationId,
                'toolsUsed' => $toolsUsed,
                'steps' => $response->steps->count(),
                'conversations' => $this->conversations,
            ];
        } catch (\Throwable $e) {
            Log::error('Agent Mode error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->aiError = 'Agent error: ' . $e->getMessage();
            return ['error' => $e->getMessage()];
        }
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
                    }
                    this.$nextTick(() => this.scrollToBottom());
                },

                handleKeydown(e) {
                    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); this.submit(); }
                },

                submit() {
                    const text = this.draft.trim();
                    if (!text || this.loading) return;
                    this.messages.push({ role: 'user', content: text, html: '' });
                    this.draft = '';
                    this.loading = true;
                    this.$nextTick(() => {
                        if (this.$refs.input) this.$refs.input.style.height = 'auto';
                        this.scrollToBottom();
                    });
                    this.$wire.sendMessage(text)
                        .then((result) => {
                            this.loading = false;
                            if (result && result.limitReached) {
                                this.limitReached = true;
                                this.messages.pop();
                                return;
                            }
                            if (result && result.error) {
                                this.messages.push({ role: 'assistant', content: result.error, html: '<p class="text-red-400">' + this.escapeHtml(result.error) + '</p>' });
                                this.$nextTick(() => this.scrollToBottom());
                                return;
                            }
                            if (result && result.html) {
                                let toolInfo = '';
                                if (result.toolsUsed && result.toolsUsed.length > 0) {
                                    toolInfo = result.toolsUsed.map(t => '\uD83D\uDD27 ' + t.name).join(', ');
                                }
                                this.messages.push({
                                    role: 'assistant',
                                    content: result.content,
                                    html: result.html,
                                    tools: toolInfo,
                                    steps: result.steps || 0,
                                });
                                if (result.conversationId) this.conversationId = result.conversationId;
                                if (result.conversations) this.conversations = result.conversations;
                                if (result.tokensUsed !== undefined) {
                                    this.tokensUsed = result.tokensUsed;
                                    this.limitReached = this.tokensUsed >= this.tokensLimit;
                                }
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        })
                        .catch(() => { this.loading = false; });
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
        .agent-bubble p             { margin: 0 0 .5rem; }
        .agent-bubble p:last-child  { margin-bottom: 0; }
        .agent-bubble pre           { background: #1e1e2e; color: #cdd6f4; border-radius: .5rem; padding: .75rem 1rem; overflow-x: auto; font-size: .78rem; margin: .4rem 0; }
        .agent-bubble code          { font-size: .78rem; }
        .agent-bubble ul, .agent-bubble ol { margin: .25rem 0 .5rem 1.2rem; }
        .agent-bubble li            { margin-bottom: .15rem; }
        .agent-bubble table         { width: 100%; border-collapse: collapse; font-size: .78rem; margin: .4rem 0; }
        .agent-bubble th, .agent-bubble td { border: 1px solid rgba(255,255,255,.1); padding: .3rem .6rem; text-align: left; }
        .agent-bubble th            { background: rgba(255,255,255,.05); font-weight: 600; }
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
