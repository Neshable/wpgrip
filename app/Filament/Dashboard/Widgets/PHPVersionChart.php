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

class PHPVersionChart extends ChartWidget
{
    protected ?string $heading = 'PHP Versions';

    // protected int | string | array $columnSpan = 2;

    protected static ?int $sort = 3;

    protected ?string $description = 'All PHP versions used across the sites';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $data = Site::where('tenant_id', Filament::getTenant()->id)
        ->pluck('php_ver')
        ->filter()
        ->map(function ($value) {
            return (string)$value;
        })
        ->countBy();
 
        return [
            'datasets' => [
                [
                    'label' => 'PHP versions',
                    'data' =>  $data->values()->toArray(),
                    'backgroundColor' => $this->assignColors( $data->keys()->toArray() ),
                ],
            ],
            'labels' => array_map(function($label) {
                return 'PHP ' . $label;
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
                
                $r = (int)($green[0] * $ratio + $red[0] * (1 - $ratio));
                $g = (int)($green[1] * $ratio + $red[1] * (1 - $ratio));
                $b = (int)($green[2] * $ratio + $red[2] * (1 - $ratio));
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
