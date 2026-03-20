<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use App\Models\PerformanceData;

use Filament\Support\RawJs;


class DomSizeChart extends ChartWidget
{
    protected ?string $heading = 'DOM size';

    protected ?string $description = 'Showing DOM size movements for the last 7 days ( in number of elements )';

    protected ?string $pollingInterval = null;

    public ?string $filter = 'lighthouse';

    public $type;

    public $chart_type;

    public $site;


    // protected function getFilters(): ?array
    // {
    //     return [
    //         'server' => 'Response Times',
    //         'lighthouse' => 'Performance Score',
    //     ];
    // }

    protected function getData(): array
    {
        
        $site = request()->route('record');
        $subdays = 7;
    
        $mainQuery = PerformanceData::where('strategy', 'desktop')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at')
            ->pluck('dom_size')->toArray();
        
        // Dates oldest-first to match orderBy('created_at', 'asc') data order
        $labels = collect(range(($subdays - 1), 0))->map(function($day) {
            return Carbon::now()->subDays($day)->format('d M');
        })->values()->toArray();
  
        return [
            'datasets' => [
                [
                    'label' => 'Dom size',
                    'data' => $mainQuery,
                    'borderColor' => '#8e5ea2',
                    'backgroundColor' => '#8e5ea2',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4
                ],
                
        
            ],
            

            'labels' => $labels,
        ];
    }

    /**
     * @return array<string, mixed> | RawJs | null
     */
   
     
protected function getOptions(): RawJs
{
    return RawJs::make(<<<JS
        {
            interaction: {
                mode: 'index',
                intersect: false,
            },
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        callback: (value) => value + ' el',
                    },
                },
                x: {
                },
            },
        }
    JS);
    
}



 
    protected function getType(): string
    {
        return 'bar';
    }

}
