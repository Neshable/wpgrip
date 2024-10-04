<?php

namespace App\Filament\Dashboard\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use Filament\Support\RawJs;

use App\Models\PerformanceScore;
use App\Models\Site;
use Filament\Facades\Filament;

class WPVersionChart extends ChartWidget
{
    protected static ?string $heading = 'WordPress Core Version';

    protected static ?string $description = 'WP versions used across all sites';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $data = Site::where('tenant_id', Filament::getTenant()->id)
        ->pluck('wp_ver')
        ->filter()
        ->map(function ($value) {
            return (string)$value;
        })
        ->countBy();
 
        return [
            'datasets' => [
                [
                    'label' => 'WordPress versions',
                    'data' =>  $data->values()->toArray(),
                    'backgroundColor' => $this->assignColors( $data->keys()->toArray() ),
                ],
            ],
            'labels' => array_map(function($label) {
                return 'WP Ver: ' . $label;
            }, $data->keys()->toArray()),
            
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
