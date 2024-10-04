<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

use App\Models\Site;
use Filament\Facades\Filament;

class SitesOverview extends BaseWidget
{
    protected function getTenantData()
    {
        $sites = Site::query()
            ->where('tenant_id', Filament::getTenant()->id )
            ->count();
    }

    protected function getTotalSitesPerTenant()
    {
        return Site::query()
            ->where('tenant_id', Filament::getTenant()->id )
            ->count();
    }

    protected function getTotalPluginVulnerabilitiesStat()
    {
        $tenantId = Filament::getTenant()->id;

        $totalPluginVulnerabilities=  \DB::table('plugin_site')
            ->join('sites', 'sites.id', '=', 'plugin_site.site_id') // Join sites to filter by tenant_id
            ->where('sites.tenant_id', $tenantId) // Filter by tenant's sites
            ->where('plugin_site.is_vulnerable', true) // Filter by vulnerable plugins
            ->count();

        $stat = Stat::make('', $totalPluginVulnerabilities . ' vulnerabilities')->descriptionIcon('icon-plugins', IconPosition::Before);
        
        if ($totalPluginVulnerabilities === null || $totalPluginVulnerabilities === false) {
            $stat->description('No vulnerabilities found')->color('success');
        } else {
            $stat->description('Total vulnerabilities found across all plugins')->color('danger');
        }

        return $stat;
    }

    protected function getTotalPendingUpdatesStats()
    {
        $tenantId = Filament::getTenant()->id;

        // Get the number of pending plugin updates and the number of sites with updates
        $pendingUpdatesData = \DB::table('plugin_site')
        ->join('sites', 'plugin_site.site_id', '=', 'sites.id') // Join with sites to filter by tenant
        ->where('sites.tenant_id', $tenantId) // Filter by tenant's sites
        ->whereNotNull('plugin_site.update_version') // Only plugins with pending updates
        ->selectRaw('COUNT(plugin_site.plugin_id) as total_updates, COUNT(DISTINCT plugin_site.site_id) as total_sites')
        ->first();

        $pendingUpdatesCount = $pendingUpdatesData->total_updates;
        $sitesWithUpdatesCount = $pendingUpdatesData->total_sites;

        $stat = Stat::make('', $pendingUpdatesCount . ' Pending Updates')->descriptionIcon('heroicon-m-arrow-path', IconPosition::Before);
        
        if ($pendingUpdatesCount === null || $pendingUpdatesCount === false) {
            $stat->description('No pending updates')->color('success');
        } else {
            $stat->description("There are {$pendingUpdatesCount} pending updates in {$sitesWithUpdatesCount} sites")->color('warning');
        }

        return $stat;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('', $this->getTotalSitesPerTenant() . ' Sites' )
                ->description('Sites connected to this workspace')
                ->descriptionIcon('icon-wordpress', IconPosition::Before),
            $this->getTotalPluginVulnerabilitiesStat(),
            $this->getTotalPendingUpdatesStats()
        ];
    }
}
