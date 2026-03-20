<?php

namespace App\Ai\Tools;

use App\Models\Site;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetUptimeStatus implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Get the current uptime monitoring status for the site, including recent downtime events and SSL certificate info.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        $this->site->loadMissing(['monitors', 'monitorLogs']);
        $monitors = $this->site->monitors;

        if ($monitors->isEmpty()) {
            return 'No uptime monitors configured for this site.';
        }

        $lines = [];
        foreach ($monitors as $monitor) {
            $lines[] = "## Monitor: {$monitor->url}";
            $lines[] = '- Status: '.($monitor->uptime_status ?? 'unknown');
            $lines[] = '- Last checked: '.($monitor->uptime_last_check_date ?? 'never');
            $lines[] = '- Certificate expiry: '.($monitor->certificate_expiration_date ?? 'unknown');
            $lines[] = '- Certificate status: '.($monitor->certificate_status ?? 'unknown');
            $lines[] = '';
        }

        // Recent logs
        $recentLogs = $this->site->monitorLogs()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        if ($recentLogs->isNotEmpty()) {
            $lines[] = '## Recent Monitor Events';
            $lines[] = '| Date | Event |';
            $lines[] = '|---|---|';
            foreach ($recentLogs as $log) {
                $lines[] = "| {$log->created_at} | {$log->event} |";
            }
        }

        return implode("\n", $lines);
    }
}
