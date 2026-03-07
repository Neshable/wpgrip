<?php

namespace App\Jobs\Global;

use App\Models\Site;
use App\Jobs\Site\CheckPluginVulnerabilities;
use App\Jobs\Site\CheckThemeVulnerabilities;
use App\Jobs\Site\CheckCoreVulnerabilities;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class CheckAllVulnerabilities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $sites = Site::where('is_staging', false)
            ->where('ssh_connection', true)
            ->where('enabled', true)
            ->get();

        foreach ($sites as $site) {
            Bus::chain([
                new CheckPluginVulnerabilities($site),
                new CheckThemeVulnerabilities($site),
                new CheckCoreVulnerabilities($site),
            ])->catch(function (\Throwable $e) use ($site) {
                Log::warning('Vulnerability check chain failed for site #' . $site->id, [
                    'error' => $e->getMessage(),
                ]);
            })->onQueue('longrunning')->dispatch();
        }
    }
}
