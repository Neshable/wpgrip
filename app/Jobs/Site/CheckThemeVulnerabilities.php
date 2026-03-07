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

class CheckThemeVulnerabilities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function handle(): void
    {
        if (! $this->site) {
            return;
        }

        $themes = $this->site->themes;

        foreach ($themes as $theme) {
            $currentVersion = $theme->pivot->version;

            if (! $currentVersion) {
                continue;
            }

            // Find all vulnerabilities for this theme slug.
            $vulnerabilities = Vulnerability::where('type', 'theme')
                ->where('slug', $theme->name)
                ->get();

            $isVulnerable = false;
            $vulnIds = [];

            foreach ($vulnerabilities as $vuln) {
                if (VulnerabilityDatabaseMiddleware::isVersionAffected($currentVersion, $vuln)) {
                    $isVulnerable = true;
                    $vulnIds[] = $vuln->id;
                }
            }

            $theme->pivot->update([
                'is_vulnerable' => $isVulnerable,
                'vuln_ids'      => json_encode($vulnIds),
            ]);
        }
    }
}
