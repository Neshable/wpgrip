<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use Filament\Support\RawJs;

use App\Models\PerformanceScore;

class SiteMetricsChart extends ChartWidget
{
    protected static ?string $heading = 'Speed metrics';

    protected static ?string $description = 'Showing stats for the last 7 days';

    protected static ?string $pollingInterval = null;

    public ?string $filter = 'lighthouse';

    public $type;

    public $chart_type;

    public $site;


    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        // $activeFilter = $this->filter;

        // $data = Trend::model(PerformanceScore::class)
        // ->between(
        //     start: now()->startOfYear(),
        //     end: now()->endOfYear(),
        // )
        // ->perMonth()
        // ->average('fcp');
 
        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Blog posts',
        //             'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
        //         ],
        //     ],
        //     'labels' => $data->map(fn (TrendValue $value) => $value->date),
        // ];

        $subdays = 7;
        $goal = 'performance';
        
        $this->chart_type = 'bar';
        $mobile_label = 'Mobile Performance';
        $desktop_label = 'Desktop Performance';
  


        $site = request()->route('record');
     
        
        $fcp = PerformanceScore::where('strategy', 'mobile')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at')
            ->pluck('fcp')->toArray();

        $lcp = PerformanceScore::where('strategy', 'mobile')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at', 'desc')
            ->pluck('lcp')->toArray();

        $fmp = PerformanceScore::where('strategy', 'mobile')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at', 'desc')
            ->pluck('fmp')->toArray();
        
        // Dates for last 7 days
        $labels = collect(range(0, ($subdays - 1)))->map(function($day) {
            return Carbon::now()->subDays($day)->format('d M');
        })->values()->toArray();
  
        return [
            'datasets' => [
                [
                    'label' => 'First Contentful Paint',
                    'data' => $fcp,
                    'borderColor' => array_map(function($value) {
                        return ($value < 5) ? '#25c25d' : '#f6770a'; // Green or Orange
                    }, $fcp),
                    'backgroundColor' => array_map(function($value) {
                        return ($value < 5) ? '#ddfbe7' : '#feeac7'; // Light Green or Light Orange
                    }, $fcp),
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Largest Contentful Paint',
                    'data' => $lcp,
                    'borderColor' => array_map(function($value) {
                        return ($value < 5) ? '#25c25d' : '#f6770a'; // Green or Orange
                    }, $lcp),
                    'backgroundColor' => array_map(function($value) {
                        return ($value < 5) ? '#ddfbe7' : '#feeac7'; // Light Green or Light Orange
                    }, $lcp),
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'First Meaningful Paint',
                    'description' => 'F',
                    'data' => $fmp,
                    'borderColor' => array_map(function($value) {
                        return ($value < 5) ? '#25c25d' : '#f6770a'; // Green or Orange
                    }, $fmp),
                    'backgroundColor' => array_map(function($value) {
                        return ($value < 5) ? '#ddfbe7' : '#feeac7'; // Light Green or Light Orange
                    }, $fmp),
                    'tension' => 0.3,
                    'fill' => true,
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
                        callback: (value) => value,
                    },
                },
                x: {
                    stacked: true,
                    reverse: true,
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
