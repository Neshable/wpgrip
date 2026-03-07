<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Vulnerability;
use App\Services\VulnerabilityDatabaseMiddleware;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckCoreVulnerabilities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function handle(): void
    {
        if (! $this->site || ! $this->site->wp_ver) {
            return;
        }

        $coreVersion = $this->site->wp_ver;

        // Find all core vulnerabilities.
        $vulnerabilities = Vulnerability::where('type', 'core')
            ->where('slug', 'wordpress')
            ->get();

        $isVulnerable = false;
        $vulnIds = [];

        foreach ($vulnerabilities as $vuln) {
            if (VulnerabilityDatabaseMiddleware::isVersionAffected($coreVersion, $vuln)) {
                $isVulnerable = true;
                $vulnIds[] = $vuln->id;
            }
        }

        // Store in sites_meta.
        if ($this->site->sitemeta) {
            $this->site->sitemeta->update([
                'is_vulnerable' => $isVulnerable,
            ]);
        }
    }
}
