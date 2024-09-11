<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use Filament\Support\RawJs;

use App\Models\PerformanceScore;

class ResponseTimeChart extends ChartWidget
{
    protected static ?string $heading = 'Response time';

    protected static ?string $description = 'Showing stats for the last 7 days';

    protected static ?string $pollingInterval = null;

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
        
        $activeFilter = $this->filter;
        $subdays = 7;
        $goal = 'performance';
        
        $this->chart_type = 'bar';
        $mobile_label = 'Mobile Performance';
        $desktop_label = 'Desktop Performance';
  
        if ( $this->type )
        {
            switch( $this->type ) 
            {
                case 'server_response_time':
                    $goal = 'server_response_time';
                    self::$heading = 'Server response times';
                    $mobile_label = 'Mobile Response';
                    $desktop_label = 'Desktop Response';
                    $this->chart_type = 'line';
                    break;
            }
        }

        $site = request()->route('record');
       
        $mobilePerformance = PerformanceScore::where('strategy', 'mobile')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at')
            ->pluck('server_response_time')->toArray();

    
        $desktopPerformance = PerformanceScore::where('strategy', 'desktop')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at', 'desc')
            ->pluck('server_response_time')->toArray();
        
        // Dates for last 7 days
        $labels = collect(range(0, ($subdays - 1)))->map(function($day) {
            return Carbon::now()->subDays($day)->format('d M');
        })->values()->toArray();
  
        return [
            'datasets' => [
                [
                    'label' => 'Mobile',
                    'data' => $mobilePerformance,
                    'backgroundColor' => '',
                    'borderColor' => '#3BD671',
                    'barPercentage' => 5,
                    'barThickness' => 30,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Desktop',
                    'data' => $desktopPerformance,
                    'backgroundColor' => '',
                    'borderColor' => '#3BD671',
                    'barPercentage' => 5,
                    'barThickness' => 30,
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
                        callback: (value) => value,
                    },
                },
                x: {
                    reverse: true,
                },
            },
        }
    JS);
}


 
    protected function getType(): string
    {
        return 'line';
    }

}
