<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Models\Vulnerability;
use App\Jobs\Site\CheckPluginVulnerabilities;
use App\Jobs\Site\CheckThemeVulnerabilities;
use App\Jobs\Site\CheckCoreVulnerabilities;

use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;

class Security extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.security';

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    /**
     * Get plugin vulnerabilities for this site.
     */
    public function getPluginVulnerabilities(): array
    {
        $results = [];

        foreach ($this->record->plugins as $plugin) {
            if (! $plugin->pivot->is_vulnerable) {
                continue;
            }

            $vulnIds = json_decode($plugin->pivot->vuln_ids, true) ?: [];
            $vulns = Vulnerability::whereIn('id', $vulnIds)->get();

            foreach ($vulns as $vuln) {
                $impact = is_string($vuln->impact) ? json_decode($vuln->impact, true) : $vuln->impact;
                $results[] = [
                    'component'   => $plugin->title ?: $plugin->name,
                    'slug'        => $plugin->name,
                    'type'        => 'plugin',
                    'version'     => $plugin->pivot->version,
                    'vuln_name'   => $vuln->name,
                    'description' => $vuln->description,
                    'max_version' => $vuln->max_version,
                    'severity'    => $impact['cvss']['severity'] ?? null,
                    'cvss_score'  => $impact['cvss']['score'] ?? null,
                ];
            }
        }

        return $results;
    }

    /**
     * Get theme vulnerabilities for this site.
     */
    public function getThemeVulnerabilities(): array
    {
        $results = [];

        foreach ($this->record->themes as $theme) {
            if (! $theme->pivot->is_vulnerable) {
                continue;
            }

            $vulnIds = json_decode($theme->pivot->vuln_ids, true) ?: [];
            $vulns = Vulnerability::whereIn('id', $vulnIds)->get();

            foreach ($vulns as $vuln) {
                $impact = is_string($vuln->impact) ? json_decode($vuln->impact, true) : $vuln->impact;
                $results[] = [
                    'component'   => $theme->title ?: $theme->name,
                    'slug'        => $theme->name,
                    'type'        => 'theme',
                    'version'     => $theme->pivot->version,
                    'vuln_name'   => $vuln->name,
                    'description' => $vuln->description,
                    'max_version' => $vuln->max_version,
                    'severity'    => $impact['cvss']['severity'] ?? null,
                    'cvss_score'  => $impact['cvss']['score'] ?? null,
                ];
            }
        }

        return $results;
    }

    /**
     * Get core vulnerabilities for this site's WP version.
     */
    public function getCoreVulnerabilities(): array
    {
        $wpVer = $this->record->wp_ver;
        if (! $wpVer) {
            return [];
        }

        $results = [];
        $vulns = Vulnerability::where('type', 'core')->where('slug', 'wordpress')->get();

        foreach ($vulns as $vuln) {
            if (\App\Services\VulnerabilityDatabaseMiddleware::isVersionAffected($wpVer, $vuln)) {
                $impact = is_string($vuln->impact) ? json_decode($vuln->impact, true) : $vuln->impact;
                $results[] = [
                    'component'   => 'WordPress Core',
                    'slug'        => 'wordpress',
                    'type'        => 'core',
                    'version'     => $wpVer,
                    'vuln_name'   => $vuln->name,
                    'description' => $vuln->description,
                    'max_version' => $vuln->max_version,
                    'severity'    => $impact['cvss']['severity'] ?? null,
                    'cvss_score'  => $impact['cvss']['score'] ?? null,
                ];
            }
        }

        return $results;
    }

    /**
     * Get all vulnerabilities combined.
     */
    public function getAllVulnerabilities(): array
    {
        return array_merge(
            $this->getPluginVulnerabilities(),
            $this->getThemeVulnerabilities(),
            $this->getCoreVulnerabilities()
        );
    }

    /**
     * Get inactive plugins for this site.
     */
    public function getInactivePlugins(): \Illuminate\Support\Collection
    {
        return $this->record->plugins->filter(fn ($p) => $p->pivot->status === 'inactive');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('scan_now')
                ->label('Run Security Scan')
                ->icon('heroicon-o-shield-check')
                ->color('primary')
                ->action(function () {
                    CheckPluginVulnerabilities::dispatch($this->record);
                    CheckThemeVulnerabilities::dispatch($this->record);
                    CheckCoreVulnerabilities::dispatch($this->record);

                    Notification::make()
                        ->title('Security scan queued')
                        ->body('Checking plugins, themes, and WordPress core against the vulnerability database.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
