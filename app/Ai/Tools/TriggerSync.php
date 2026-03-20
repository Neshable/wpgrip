<?php

namespace App\Ai\Tools;

use App\Models\Site;
use App\Jobs\Site\SyncSiteStats;
use App\Services\SiteMdService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class TriggerSync implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Trigger a full site data sync from the remote server. This refreshes all plugin, theme, and configuration data. Runs in the background.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            SyncSiteStats::dispatch($this->site);

            // Also regenerate SITE.md for fresh context
            app(SiteMdService::class)->write($this->site);

            return 'Site sync job has been dispatched. Data will be refreshed shortly. The site snapshot has been regenerated with current DB data.';
        } catch (\Throwable $e) {
            return 'Failed to dispatch sync job: ' . $e->getMessage();
        }
    }
}
