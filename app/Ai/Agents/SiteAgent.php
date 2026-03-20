<?php

namespace App\Ai\Agents;

use App\Ai\Middleware\TrackTokenUsage;
use App\Ai\SshSessionManager;
use App\Ai\Tools\ClearCache;
use App\Ai\Tools\GetBackupHistory;
use App\Ai\Tools\GetPerformanceData;
use App\Ai\Tools\GetSiteInfo;
use App\Ai\Tools\GetUptimeStatus;
use App\Ai\Tools\ListPlugins;
use App\Ai\Tools\ListThemes;
use App\Ai\Tools\RunSshCommand;
use App\Ai\Tools\RunWpCliCommand;
use App\Ai\Tools\TogglePlugin;
use App\Ai\Tools\TriggerBackup;
use App\Ai\Tools\TriggerSync;
use App\Ai\Tools\UpdatePlugin;
use App\Ai\Tools\UpdateTheme;
use App\Models\AgentPromptTemplate;
use App\Models\Site;
use App\Services\SiteMdService;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::Anthropic)]
#[Model('claude-sonnet-4-20250514')]
#[MaxTokens(4096)]
#[MaxSteps(10)]
#[Temperature(0.3)]
#[Timeout(120)]
class SiteAgent implements Agent, Conversational, HasTools, HasMiddleware
{
    use Promptable, RemembersConversations;

    private ?SshSessionManager $sshSession = null;

    public function __construct(
        public Site $site,
        public ?AgentPromptTemplate $promptTemplate = null,
    ) {}

    /**
     * Attach a persistent SSH session to share across tools.
     */
    public function withSshSession(SshSessionManager $session): static
    {
        $this->sshSession = $session;

        return $this;
    }

    /**
     * Get the SSH session (if attached).
     */
    public function sshSession(): ?SshSessionManager
    {
        return $this->sshSession;
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $template = $this->promptTemplate
            ?? AgentPromptTemplate::getDefault();

        $siteMd = app(SiteMdService::class)->read($this->site);

        $content = $template?->content ?? self::defaultInstructions();

        return str_replace(
            ['{{site_name}}', '{{site_url}}', '{{site_snapshot}}'],
            [$this->site->name ?? 'Unknown', $this->site->url ?? 'Unknown', $siteMd ?? 'No snapshot available'],
            $content
        );
    }

    /**
     * Get the tools available to the agent.
     *
     * @return \Laravel\Ai\Contracts\Tool[]
     */
    public function tools(): iterable
    {
        $s = $this->sshSession;

        return [
            // Read-only tools (DB-based, no SSH)
            new GetSiteInfo($this->site),
            new ListPlugins($this->site),
            new ListThemes($this->site),
            new GetUptimeStatus($this->site),
            new GetPerformanceData($this->site),
            new GetBackupHistory($this->site),

            // SSH-based tools (share the persistent session)
            new TogglePlugin($this->site, $s),
            new UpdatePlugin($this->site, $s),
            new UpdateTheme($this->site, $s),
            new ClearCache($this->site, $s),
            new RunWpCliCommand($this->site, $s),
            new TriggerBackup($this->site),
            new TriggerSync($this->site),

            // General SSH command tool — uses persistent session
            ...($s ? [new RunSshCommand($s)] : []),
        ];
    }

    /**
     * Get the agent's prompt middleware.
     */
    public function middleware(): array
    {
        return [
            new TrackTokenUsage,
        ];
    }

    /**
     * Default instructions when no template is configured.
     */
    public static function defaultInstructions(): string
    {
        return <<<'PROMPT'
You are an AI agent built into WPGrip, a WordPress site management platform.
You are managing the site: "{{site_name}}" ({{site_url}}).

## Site Context
{{site_snapshot}}

## Your Capabilities
You have tools to inspect and modify this WordPress site:
- Read site information, plugins, themes, performance data, uptime status
- Activate/deactivate plugins
- Update plugins, themes, and WordPress core
- Clear caches
- Trigger backups and site syncs
- Run safe WP-CLI commands
- **SSH access**: You have a persistent SSH session to the server. You can run
  shell commands for diagnostics, log inspection, disk usage, process checks,
  database queries (read-only via WP-CLI), file inspection, and more.
  The session stays open across tool calls, so you can build on previous results.

## SSH Guidelines
- Use the RunSshCommand tool for any shell-level inspection: `ls`, `cat`, `head`,
  `tail`, `grep`, `find`, `du`, `df`, `ps`, `top -bn1`, `wp db query`, etc.
- Pipes (`|`) are allowed: `wp plugin list --format=csv | grep inactive`
- Stay non-destructive: never delete files, modify configs directly, or install packages.
- If you need to understand a problem, check logs first: `tail -n 50 ~/logs/error.log` or
  `wp log` if the site uses a logging plugin.
- For database inspection, use `wp db query "SELECT ..."` — always read-only.
- When exploring the filesystem, start from the site root and be methodical.

## General Guidelines
- Always explain what you're about to do before using a write/action tool.
- For destructive operations (updates, search-replace), summarize the expected impact first and ask for confirmation.
- Use read tools to gather information before recommending actions.
- If SSH is not connected, inform the user and suggest they check the connection settings.
- Be concise and use markdown formatting.
- When listing plugins or themes, use tables for readability.
- Proactively mention security issues, pending updates, or configuration problems you notice.
PROMPT;
    }
}
