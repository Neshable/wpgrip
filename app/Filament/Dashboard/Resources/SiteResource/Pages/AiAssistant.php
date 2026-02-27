<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Services\SSHSiteConnect;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Anthropic\Laravel\Facades\Anthropic;

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

    // -------------------------------------------------------------------------
    // Chat
    // -------------------------------------------------------------------------

    public function sendMessage(): void
    {
        $text = trim($this->userMessage);
        if (empty($text)) {
            return;
        }

        $this->aiError     = '';
        $this->loading     = true;
        $this->userMessage = '';

        // Append user message
        $this->messages[] = ['role' => 'user', 'content' => $text];

        try {
            $reply = $this->callAnthropic();
        } catch (\Throwable $e) {
            Log::error('AI Assistant error: ' . $e->getMessage());
            $this->aiError = 'AI request failed: ' . $e->getMessage();
            $this->loading = false;
            return;
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $reply];
        $this->loading    = false;
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
