<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use Filament\Support\RawJs;

use App\Models\Plugin;
use App\Models\Site;
use Filament\Facades\Filament;

class PluginChart extends ChartWidget
{
    protected ?string $heading = 'All Plugins';

    protected ?string $description = 'Plugin data across all sites';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $pluginStatusData = \DB::table('plugin_site')
            ->join('sites', 'plugin_site.site_id', '=', 'sites.id') // Join with sites
            ->where('sites.tenant_id', Filament::getTenant()->id) // Filter by tenant's sites
            ->selectRaw("
                COUNT(CASE WHEN plugin_site.update_version IS NULL THEN 1 END) as total_up_to_date,
                COUNT(CASE WHEN plugin_site.update_version IS NOT NULL THEN 1 END) as total_outdated
            ")
            ->first();

        // Extract the values
        $upToDatePluginsCount = $pluginStatusData->total_up_to_date;
        $outdatedPluginsCount = $pluginStatusData->total_outdated;
 
        return [
            'datasets' => [
                [
                    'label' => 'Up to date',
                    'data' =>  [ $upToDatePluginsCount,  $outdatedPluginsCount],
                    'backgroundColor' => [ '#008060', '#ffab3b' ],
                ],
                // [
                //     'label' => 'WordPress versions',
                //     'data' =>  $data->values()->toArray(),
                //     'backgroundColor' => $this->assignColors( $data->keys()->toArray() ),
                // ],
            ],
            'labels' => ['Up to date', 'Pending Updates'],
            
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function assignColors( $labels )
    {
        // Custom colors
        $green = [0x6B, 0xC3, 0x74];
        $red = [0xE3, 0x5B, 0x5B];

        // Prepare colors
        $colors = [];
        $versions = count($labels);

        if ( $versions >= 1 ) {
            foreach (range(0, $versions - 1) as $i) {
                if ( $versions = 1 )
                {
                    $ratio = $i;
                } else 
                {
                    $ratio = $i / ($versions - 1);
                }
                $r = (int)($red[0] * $ratio + $green[0] * (1 - $ratio));
                $g = (int)($red[1] * $ratio + $green[1] * (1 - $ratio));
                $b = (int)($red[2] * $ratio + $green[2] * (1 - $ratio));
                $colors[] = sprintf("#%02x%02x%02x", $r, $g, $b);
            }
        }
        

        
        return $colors;
    }

    /**
     * @return array<string, mixed> | RawJs | null
     */
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                scales: {
                    y: {
                        display: false,
                    },
                    x: {
                        display: false,
                        reverse: true,
                    },
                },
            }
        JS);
    }
}
