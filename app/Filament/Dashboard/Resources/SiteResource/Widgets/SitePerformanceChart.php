<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use Filament\Support\RawJs;

use App\Models\PerformanceScore;

class SitePerformanceChart extends ChartWidget
{
    protected static ?string $heading = 'Site performance';

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
            ->pluck($goal)->toArray();

    
        $desktopPerformance = PerformanceScore::where('strategy', 'desktop')
            ->where('site_id', $site)
            ->where('created_at', '>=', Carbon::now()->subDays($subdays))
            ->orderBy('created_at', 'desc')
            ->pluck($goal)->toArray();
        
        // Dates for last 7 days
        $labels = collect(range(0, ($subdays - 1)))->map(function($day) {
            return Carbon::now()->subDays($day)->format('d M');
        })->values()->toArray();
  
        return [
            'datasets' => [
                [
                    'label' => $mobile_label,
                    'data' => $mobilePerformance,
                    'backgroundColor' => array_map(function($value) {
                        if ($value < 50) {
                            return '#FFF2F1';
                        } elseif ($value >= 50 && $value <= 70) {
                            return '#FFF7EB';
                        } else {
                            return '#ECFAF0';
                        }
                    }, $mobilePerformance),
                    'borderColor' => array_map(function($value) {
                        if ($value < 50) {
                            return '#FF3232';
                        } elseif ($value >= 50 && $value <= 69) {
                            return '#FFAB33';
                        } else {
                            return '#00CC66';
                        }
                    }, $mobilePerformance),
             
                    'tension' => 0.3
                ],
                [
                    'label' => $desktop_label,
                    'data' => $desktopPerformance,
                    'backgroundColor' => array_map(function($value) {
                        if ($value < 50) {
                            return '#FFF2F1';
                        } elseif ($value >= 50 && $value <= 70) {
                            return '#FFF7EB';
                        } else {
                            return '#ECFAF0';
                        }
                    }, $desktopPerformance),
                    'borderColor' => array_map(function($value) {
                        if ($value < 50) {
                            return '#FF3232';
                        } elseif ($value >= 50 && $value <= 69) {
                            return '#FFAB33';
                        } else {
                            return '#00CC66';
                        }
                    }, $desktopPerformance),
            
                    'tension' => 0.3
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
        return $this->chart_type;
    }

}
