<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Services\SiteMdService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Regenerates the SITE.md snapshot for a site.
 *
 * Dispatched automatically after any sync job completes
 * (SyncSiteStats, GetAllPlugins, GetAllThemes, CheckDomainStats).
 * Can also be dispatched manually from the AI Agent Mode UI.
 */
class GenerateSiteMd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Site $site) {}

    public function handle(SiteMdService $service): void
    {
        try {
            $path = $service->write($this->site);
            Log::info('SITE.md regenerated', ['site' => $this->site->id, 'path' => $path]);
        } catch (\Throwable $e) {
            Log::error('GenerateSiteMd failed', ['site' => $this->site->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
