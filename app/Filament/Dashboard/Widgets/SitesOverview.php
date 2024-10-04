<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Site;
use App\Models\Server;
use App\Models\Clients;
use App\Models\Plugins;
use App\Models\Repository;

use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class SitesOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    

    protected function getStats(): array
    {
        // $startDate = $this->filters['startDate'] ?? null;
        // $endDate = $this->filters['endDate'] ?? null;

        return [
            StatsOverviewWidget\Stat::make(
                label: 'Sites connected',
                value: Site::query()
                    ->where('tenant_id', Filament::getTenant()->id )
                    ->count(),
            )
            // ->description('Your network growth')
            // ->descriptionIcon('icon-wordpress')
            // ->color('info')
            // ->chart([2, 4, 6, 8, 10, 12, 14])
            ->extraAttributes([
                'class' => 'wpg-stat-box',
            ]),
            // Total servers
            StatsOverviewWidget\Stat::make(
                label: 'Servers connected',
                value: Server::query()
                    ->where('tenant_id', Filament::getTenant()->id )
                    ->count(),
            )->extraAttributes([
                'class' => 'wpg-stat-box',
            ]),
            // ->description('SSH connection')
            // ->descriptionIcon('icon-wordpress')
            // ->color('info')
            // ->chart([2, 4, 6, 8, 10, 12, 14]),
             // Total repositories
             StatsOverviewWidget\Stat::make(
                label: 'Repositories connected',
                value: Repository::query()
                    ->where('tenant_id', Filament::getTenant()->id )
                    ->count(),
                )->extraAttributes([
                    'class' => 'wpg-stat-box',
                ]),
                // ->description('Themes, Plugins, Apps')
                // ->descriptionIcon('icon-wordpress')
                // ->color('info')
                // ->chart([2, 4, 6, 8, 10, 12, 14]),


            // Stat::make('Unique views', '192.1k')
            //     ->description('32k increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->chart([2, 4, 6, 8, 10, 12, 14])
            //     ->color('info'),

        ];
    }
}
