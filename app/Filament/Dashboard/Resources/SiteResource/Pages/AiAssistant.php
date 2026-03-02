<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Jobs\Site\GenerateSiteMd;
use App\Services\SiteMdService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Anthropic\Laravel\Facades\Anthropic;
use App\Services\Plans\SubscriptionLimitChecker;

class AiAssistant extends ViewRecord
{
    protected static string $resource = SiteResource::class;
    protected static string $view     = 'site.single.ai-assistant';

    // -------------------------------------------------------------------------
    // Livewire state
    // -------------------------------------------------------------------------

    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    public string $userMessage = '';
    public bool   $loading     = false;
    public string $aiError     = '';
    public string $siteMdAge   = '';   // shown in UI so user knows how fresh the context is

    // -------------------------------------------------------------------------

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! SubscriptionLimitChecker::canUseAi()) {
            $this->redirect(static::getResource()::getUrl('view', ['record' => $record, 'tenant' => \Filament\Facades\Filament::getTenant()]));
            return;
        }

        // Pre-compute the age of SITE.md so the view can show it
        $this->siteMdAge = $this->getSiteMdAge();

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): HtmlString => new HtmlString($this->aiStyles()),
            scopes: [static::class],
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): HtmlString => new HtmlString($this->aiScript()),
            scopes: [static::class],
        );
    }

    // -------------------------------------------------------------------------
    // Chat
    // -------------------------------------------------------------------------

    public function sendMessage(string $text): array
    {
        $text = trim($text);
        if (empty($text)) {
            return ['error' => 'Empty message.'];
        }

        $this->aiError = '';
        $this->messages[] = ['role' => 'user', 'content' => $text];

        try {
            $reply = $this->callAnthropic();
        } catch (\Throwable $e) {
            Log::error('AI Assistant error: ' . $e->getMessage());
            $this->aiError = 'AI request failed: ' . $e->getMessage();
            return ['error' => $e->getMessage()];
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $reply];

        $html = (string) Str::of($reply)
            ->markdown(['html_input' => 'escape', 'allow_unsafe_links' => false]);

        return ['content' => $reply, 'html' => $html];
    }

    /**
     * Manually trigger a SITE.md regeneration.
     * Returns the new age string so Alpine can update the badge.
     */
    public function refreshSiteMd(): array
    {
        try {
            app(SiteMdService::class)->write($this->record);
            $this->siteMdAge = $this->getSiteMdAge();
            return ['ok' => true, 'age' => $this->siteMdAge];
        } catch (\Throwable $e) {
            Log::error('Manual SITE.md refresh failed: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->aiError  = '';
    }

    // -------------------------------------------------------------------------
    // Anthropic — DB-only, no SSH tools
    // -------------------------------------------------------------------------

    private function callAnthropic(): string
    {
        $site    = $this->record;
        $siteMd  = app(SiteMdService::class)->read($site);

        $system = <<<SYSTEM
You are an AI assistant built into WPGrip, a WordPress site management platform.
You help users understand and manage the WordPress site: "{$site->name}" ({$site->url}).

Below is a full snapshot of the site data from the WPGrip database (SITE.md):

{$siteMd}

---
Guidelines:
- Answer questions based ONLY on the data above.
- If information is not in the snapshot, say so clearly and suggest the user trigger a sync.
- Format answers in markdown. Use tables and bullet lists where appropriate.
- Be concise and direct.
- Never suggest running SSH commands — you do not have shell access.
- When you spot pending plugin/theme updates, proactively mention them.
SYSTEM;

        $anthropicMessages = array_map(
            fn ($m) => ['role' => $m['role'], 'content' => $m['content']],
            $this->messages,
        );

        $response = Anthropic::messages()->create([
            'model'      => 'claude-opus-4-5',
            'max_tokens' => 2048,
            'system'     => $system,
            'messages'   => $anthropicMessages,
        ]);

        $text = '';
        foreach ($response->content as $block) {
            if ($block->type === 'text') {
                $text .= $block->text;
            }
        }

        return $text ?: '(No response)';
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getSiteMdAge(): string
    {
        $path = storage_path('app/sites/' . $this->record->id . '/SITE.md');
        if (!file_exists($path)) {
            return 'never generated';
        }
        return \Carbon\Carbon::createFromTimestamp(filemtime($path))->diffForHumans();
    }

    // -------------------------------------------------------------------------
    // Inline JS
    // -------------------------------------------------------------------------

    private function aiScript(): string
    {
        return <<<'JS'
        <script>
        function aiChat() {
            return {
                messages: [],
                draft: '',
                loading: false,
                refreshing: false,
                siteMdAge: '',
                suggestions: [
                    'Which plugins have updates available?',
                    'What WordPress and PHP version is this site running?',
                    'Show me the database tables and sizes',
                    'Is anything flagged as vulnerable or has issues?',
                    'When was this site last synced?',
                    'List all inactive plugins',
                ],

                init(rootEl) {
                    const seed = document.getElementById('ai-chat-seed');
                    if (seed) {
                        try { this.messages = JSON.parse(seed.dataset.messages || '[]'); } catch(e) { this.messages = []; }
                        this.siteMdAge = seed.dataset.age || '';
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
                    this.draft   = '';
                    this.loading = true;
                    this.$nextTick(() => {
                        if (this.$refs.input) this.$refs.input.style.height = 'auto';
                        this.scrollToBottom();
                    });
                    this.$wire.sendMessage(text)
                        .then((result) => {
                            this.loading = false;
                            if (result && result.html) {
                                this.messages.push({ role: 'assistant', content: result.content, html: result.html });
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        })
                        .catch(() => { this.loading = false; });
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

                clearChat() {
                    this.$wire.clearChat();
                    this.messages = [];
                    this.loading  = false;
                },

                nl2br(str) {
                    return String(str)
                        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                        .replace(/\n/g, '<br>');
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

    private static function aiStyles(): string
    {
        return <<<'CSS'
        <style>
        @keyframes ai-bounce {
            0%, 80%, 100% { transform: translateY(0); opacity: .4; }
            40%           { transform: translateY(-6px); opacity: 1; }
        }
        @keyframes ai-fadein {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .ai-dot { animation: ai-bounce 1.2s infinite ease-in-out; }
        .ai-dot:nth-child(1) { animation-delay: 0s; }
        .ai-dot:nth-child(2) { animation-delay: .2s; }
        .ai-dot:nth-child(3) { animation-delay: .4s; }
        .ai-msg { animation: ai-fadein .22s ease-out both; }
        .ai-bubble p             { margin: 0 0 .5rem; }
        .ai-bubble p:last-child  { margin-bottom: 0; }
        .ai-bubble pre           { background: #1e1e2e; color: #cdd6f4; border-radius: .5rem; padding: .75rem 1rem; overflow-x: auto; font-size: .78rem; margin: .4rem 0; }
        .ai-bubble code          { font-size: .78rem; }
        .ai-bubble ul, .ai-bubble ol { margin: .25rem 0 .5rem 1.2rem; }
        .ai-bubble li            { margin-bottom: .15rem; }
        .ai-bubble table         { width: 100%; border-collapse: collapse; font-size: .78rem; margin: .4rem 0; }
        .ai-bubble th, .ai-bubble td { border: 1px solid #e5e7eb; padding: .3rem .6rem; text-align: left; }
        .ai-bubble th            { background: #f3f4f6; font-weight: 600; }
        </style>
        CSS;
    }
}
