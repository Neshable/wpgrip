<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Services\SSHSiteConnect;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Anthropic\Laravel\Facades\Anthropic;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AiAssistant extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.ai-assistant';

    // -------------------------------------------------------------------------
    // Livewire state
    // -------------------------------------------------------------------------

    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    /** The current user input */
    public string $userMessage = '';

    /** Whether we are waiting for a reply */
    public bool $loading = false;

    /** Error string shown in UI */
    public string $aiError = '';

    // -------------------------------------------------------------------------

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

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

    private function aiScript(): string
    {
        return <<<'JS'
        <script>
        function aiChat() {
            return {
                messages: [],
                draft: '',
                loading: false,
                suggestions: [
                    'List all plugins with pending updates',
                    'What PHP version is running?',
                    'Show the last 30 lines of the error log',
                    'Check wp-config.php for debug or security issues',
                    'How big is the database?',
                ],

                init(rootEl) {
                    // Load seed data from the hidden div
                    const seed = document.getElementById('ai-chat-seed');
                    if (seed) {
                        try { this.messages = JSON.parse(seed.dataset.messages || '[]'); }
                        catch(e) { this.messages = []; }
                    }

                    this.$nextTick(() => this.scrollToBottom());

                    this.$wire.$on('messageAdded', ({ role, content, html }) => {
                        this.loading = false;
                        this.messages.push({ role, content, html });
                        this.$nextTick(() => this.scrollToBottom());
                    });

                    this.$watch('$wire.messages', (val) => {
                        if (Array.isArray(val) && val.length === 0) {
                            this.messages = [];
                            this.loading  = false;
                        }
                    });
                },

                handleKeydown(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.submit();
                    }
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
                    this.$wire.sendMessage(text).catch(() => { this.loading = false; });
                },

                fillSuggestion(s) {
                    this.draft = s;
                    this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
                },

                clearChat() {
                    this.messages = [];
                    this.loading  = false;
                    this.$wire.clearChat();
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
        </style>
        CSS;
    }

    // -------------------------------------------------------------------------
    // Chat
    // -------------------------------------------------------------------------

    /**
     * Called by Alpine with the message text already trimmed.
     * Appends to server-side history, calls Anthropic, dispatches event back to Alpine.
     */
    public function sendMessage(string $text): void
    {
        $text = trim($text);
        if (empty($text)) {
            return;
        }

        $this->aiError = '';

        // Keep server-side history in sync (Alpine already showed the user bubble)
        $this->messages[] = ['role' => 'user', 'content' => $text];

        try {
            $reply = $this->callAnthropic();
        } catch (\Throwable $e) {
            Log::error('AI Assistant error: ' . $e->getMessage());
            $this->aiError = 'AI request failed: ' . $e->getMessage();
            $this->dispatch('aiError', message: $e->getMessage());
            return;
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $reply];

        // Render markdown server-side so Alpine just injects HTML
        $html = (string) \Illuminate\Support\Str::of($reply)
            ->markdown(['html_input' => 'escape', 'allow_unsafe_links' => false]);

        // Tell Alpine to append the bubble and stop the loading indicator
        $this->dispatch('messageAdded', role: 'assistant', content: $reply, html: $html);
    }

    // -------------------------------------------------------------------------
    // Anthropic call with tool use
    // -------------------------------------------------------------------------

    private function callAnthropic(): string
    {
        $site    = $this->record;
        $siteCtx = $this->buildSiteContext();

        $systemPrompt = <<<SYSTEM
You are an AI assistant integrated into WPGrip, a WordPress site management platform.
You are helping the user manage and understand the WordPress site: "{$site->name}" ({$site->url}).

Here is the current data we have in our database for this site:

{$siteCtx}

You have a tool called `run_ssh_command` that lets you run read-only shell commands on the remote server via SSH.
Use it when the user asks about files, logs, server details, or anything not already in the database context.
Only use safe, non-destructive commands (cat, ls, tail, grep, wp, etc.).
Never run commands that modify, delete, or write files.

Be concise, helpful, and format code/output in markdown code blocks.
SYSTEM;

        // Build conversation for Anthropic
        $anthropicMessages = [];
        foreach ($this->messages as $msg) {
            $anthropicMessages[] = [
                'role'    => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        $tools = [
            [
                'name'        => 'run_ssh_command',
                'description' => 'Run a read-only shell command on the remote WordPress server via SSH and return its output.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'command' => [
                            'type'        => 'string',
                            'description' => 'The shell command to execute. Must be read-only (cat, ls, tail, grep, wp option get, etc.).',
                        ],
                    ],
                    'required' => ['command'],
                ],
            ],
        ];

        // Agentic loop — allow up to 5 tool rounds
        $maxRounds = 5;
        $round     = 0;

        while ($round < $maxRounds) {
            $round++;

            $response = Anthropic::messages()->create([
                'model'      => 'claude-opus-4-5',
                'max_tokens' => 2048,
                'system'     => $systemPrompt,
                'messages'   => $anthropicMessages,
                'tools'      => $tools,
            ]);

            $textParts     = [];
            $toolUseBlocks = [];

            foreach ($response->content as $block) {
                if ($block->type === 'text') {
                    $textParts[] = $block->text;
                } elseif ($block->type === 'tool_use') {
                    $toolUseBlocks[] = $block;
                }
            }

            // No tool calls -> final answer
            if (empty($toolUseBlocks)) {
                return implode('\n', $textParts) ?: '(No response)';
            }

            // Append assistant message with raw content blocks
            $rawContent = [];
            foreach ($textParts as $txt) {
                $rawContent[] = ['type' => 'text', 'text' => $txt];
            }
            foreach ($toolUseBlocks as $tb) {
                $rawContent[] = [
                    'type'  => 'tool_use',
                    'id'    => $tb->id,
                    'name'  => $tb->name,
                    'input' => $tb->input,
                ];
            }
            $anthropicMessages[] = ['role' => 'assistant', 'content' => $rawContent];

            // Process tool calls
            $toolResults = [];
            foreach ($toolUseBlocks as $toolBlock) {
                if ($toolBlock->name === 'run_ssh_command') {
                    $cmd    = $toolBlock->input['command'] ?? '';
                    $output = $this->runSshCommand($cmd);

                    $toolResults[] = [
                        'type'        => 'tool_result',
                        'tool_use_id' => $toolBlock->id,
                        'content'     => $output,
                    ];
                }
            }

            $anthropicMessages[] = ['role' => 'user', 'content' => $toolResults];
        }

        return '(Max tool-use rounds reached without a final text response.)';
    }

    // -------------------------------------------------------------------------
    // SSH tool
    // -------------------------------------------------------------------------

    private function runSshCommand(string $command): string
    {
        // Block destructive patterns
        $blocked = ['rm ', 'rmdir', 'mkfs', 'dd ', '> /', 'wget ', 'curl ', 'chmod ', 'chown ', 'mv ', 'cp '];
        foreach ($blocked as $b) {
            if (stripos($command, $b) !== false) {
                return '[BLOCKED] The command "' . $command . '" is not permitted for safety reasons.';
            }
        }

        try {
            $conn = new SSHSiteConnect($this->record);
            if (! $conn->active) {
                return '[SSH] Could not establish SSH connection to the server.';
            }
            $output = $conn->exec($command);
            $conn->close();
            return $output ?: '(empty output)';
        } catch (\Throwable $e) {
            Log::error('AI Assistant SSH error: ' . $e->getMessage());
            return '[SSH Error] ' . $e->getMessage();
        }
    }

    // -------------------------------------------------------------------------
    // Build a rich context string from the DB
    // -------------------------------------------------------------------------

    private function buildSiteContext(): string
    {
        $site = $this->record;
        $site->load(['server', 'client', 'sitemeta', 'plugins', 'themes']);

        $lines = [];

        $lines[] = '## Site Info';
        $lines[] = 'Name: ' . ($site->name ?? 'n/a');
        $lines[] = 'URL: ' . ($site->url ?? 'n/a');
        $lines[] = 'SSH User: ' . ($site->ssh_user ?? 'n/a');
        $lines[] = 'Dir Path: ' . ($site->dir_path ?? 'n/a');
        $lines[] = 'WordPress Version: ' . ($site->wp_ver ?? 'n/a');
        $lines[] = 'PHP Version: ' . ($site->php_ver ?? 'n/a');
        $lines[] = 'WP-CLI Version: ' . ($site->cli_ver ?? 'n/a');
        $lines[] = 'DB Prefix: ' . ($site->db_prefix ?? 'n/a');
        $lines[] = 'Is Staging: ' . ($site->is_staging ? 'yes' : 'no');
        $lines[] = 'SSH Connected: ' . ($site->ssh_connection ? 'yes' : 'no');
        $lines[] = 'Last Synced: ' . ($site->updated_at?->toDateTimeString() ?? 'n/a');

        if ($site->server) {
            $lines[] = '';
            $lines[] = '## Server';
            $lines[] = 'Name: ' . $site->server->name;
            $lines[] = 'IP: ' . $site->server->ip;
            $lines[] = 'Port: ' . ($site->server->port ?? 22);
            $provider = $site->server->provider;
            $lines[] = 'Provider: ' . ($provider instanceof \BackedEnum ? $provider->value : ($provider ?? 'n/a'));
        }

        if ($site->client) {
            $lines[] = '';
            $lines[] = '## Client';
            $lines[] = 'Name: ' . $site->client->name;
        }

        if ($site->sitemeta) {
            $meta    = $site->sitemeta;
            $lines[] = '';
            $lines[] = '## Site Meta';
            $lines[] = 'DB Size: ' . ($meta->db_size ?? 'n/a') . ' MB';
            $lines[] = 'Domain Expiry: ' . ($meta->domain_expiry_date ?? 'n/a');
            $lines[] = 'Admin Email: ' . ($meta->admin_email ?? 'n/a');
            $lines[] = 'Active Theme: ' . ($meta->active_theme ?? 'n/a');
        }

        if ($site->plugins && $site->plugins->count()) {
            $lines[] = '';
            $lines[] = '## Plugins (' . $site->plugins->count() . ' total)';
            foreach ($site->plugins->take(40) as $plugin) {
                $status = $plugin->pivot->status ?? 'unknown';
                $ver    = $plugin->pivot->version ?? 'n/a';
                $upd    = $plugin->pivot->update_version ?? null;
                $line   = '- ' . $plugin->name . ' v' . $ver . ' [' . $status . ']';
                if ($upd) {
                    $line .= ' (update available: v' . $upd . ')';
                }
                $lines[] = $line;
            }
            if ($site->plugins->count() > 40) {
                $lines[] = '... and ' . ($site->plugins->count() - 40) . ' more plugins.';
            }
        }

        if ($site->themes && $site->themes->count()) {
            $lines[] = '';
            $lines[] = '## Themes (' . $site->themes->count() . ' total)';
            foreach ($site->themes as $theme) {
                $ver  = $theme->pivot->version ?? 'n/a';
                $upd  = $theme->pivot->update_version ?? null;
                $line = '- ' . $theme->name . ' v' . $ver;
                if ($upd) {
                    $line .= ' (update available: v' . $upd . ')';
                }
                $lines[] = $line;
            }
        }

        return implode("\n", $lines);
    }

    // -------------------------------------------------------------------------
    // Clear chat
    // -------------------------------------------------------------------------

    public function clearChat(): void
    {
        $this->messages = [];
        $this->aiError  = '';
    }
}
