<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\ChartWidget;

use Filament\Support\RawJs;
use Carbon\Carbon;

use App\Models\PerformanceData;

class SitePerformanceHistory extends ChartWidget
{
    protected static ?string $heading = 'Site performance history';

    protected static ?string $description = 'Showing stats for the last 7 days';

    protected static ?string $pollingInterval = null;

    public $type;

    protected function getData(): array
    {

        $site = request()->route('record');
        $subdays = 7;


        if ( $this->type && $this->type == 'mobile' )
        {
            self::$heading = 'Mobile Performance';
            $mainQuery = PerformanceData::where('strategy', 'mobile')
                ->where('site_id', $site)
                ->where('created_at', '>=', Carbon::now()->subDays($subdays))
                ->orderBy('created_at')
                ->get()->toArray();
        }
        else 
        {
            self::$heading = 'Desktop Performance';
            $mainQuery = PerformanceData::where('strategy', 'desktop')
                ->where('site_id', $site)
                ->where('created_at', '>=', Carbon::now()->subDays($subdays))
                ->orderBy('created_at', 'desc')
                ->get()->toArray();
        }


        // Dates for last 7 days
          $labels = collect(range(0, ($subdays - 1)))->map(function($day) {
            return Carbon::now()->subDays($day)->format('d M');
        })->values()->toArray();

         return [
            'datasets' => [
                [
                    'label' => 'FCP',
                    'data' => array_map(function($value) {
                         return round($value / 1000, 1);
                    }, array_column($mainQuery, 'fcp')),
                    'tension' => 0.6,
                    'borderColor' => '#e8c3b9',
                    'backgroundColor' => '#e8c3b9',
                    'fill' => true
                ],
                [
                    'label' => 'Speed Index',
                    'data' => array_map(function($value) {
                         return round($value / 1000, 1);
                    }, array_column($mainQuery, 'speed_index')),
                    'tension' => 1,
                    'borderColor' => '#3e95cd',
                    'backgroundColor' => '#3e95cd',
                    'fill' => true
                ],
                [
                    'label' => 'LCP',
                    'data' => array_map(function($value) {
                         return round($value / 1000, 1);
                    }, array_column($mainQuery, 'lcp')),
                    'tension' => 0.6,
                    'borderColor' => '#3cba9f',
                    'backgroundColor' => '#3cba9f',
                    'fill' => true
                ],
                [
                    'label' => 'Time to Interactive',
                    'data' => array_map(function($value) {
                         return round($value / 1000, 1);
                    }, array_column($mainQuery, 'time_interactive')),
                    'tension' => 0.6,
                    'borderColor' => '#3e95cd',
                    'backgroundColor' => '#3e95cd',
                    'fill' => true
                ],
                // [
                //     'label' => 'DOM Size',
                //     'data' => array_column($mainQuery, 'dom_size'),
                //     'tension' => 0.6,
                //     'borderColor' => '#3e95cd',
                //     'fill' => false
                // ],
                [
                    'label' => 'Server Response Time',
                    'data' => array_map(function($value) {
                         return round($value / 1000, 1);
                    }, array_column($mainQuery, 'server_response_time')),
                    'tension' => 0.6,
                    'borderColor' => '#8e5ea2',
                    'backgroundColor' => '#8e5ea2',
                    'fill' => true
                ],
                // [
                //     'label' => $mobile_label,
                //     'data' => $mobilePerformance,
                //     'backgroundColor' => array_map(function($value) {
                //         if ($value < 50) {
                //             return '#FFF2F1';
                //         } elseif ($value >= 50 && $value <= 70) {
                //             return '#FFF7EB';
                //         } else {
                //             return '#ECFAF0';
                //         }
                //     }, $mobilePerformance),
                //     'borderColor' => array_map(function($value) {
                //         if ($value < 50) {
                //             return '#FF3232';
                //         } elseif ($value >= 50 && $value <= 69) {
                //             return '#FFAB33';
                //         } else {
                //             return '#00CC66';
                //         }
                //     }, $mobilePerformance),
             
                //     'tension' => 0.3
                // ],
                // [
                //     'label' => $desktop_label,
                //     'data' => $mainQuery,
                //     'backgroundColor' => array_map(function($value) {
                //         if ($value < 50) {
                //             return '#FFF2F1';
                //         } elseif ($value >= 50 && $value <= 70) {
                //             return '#FFF7EB';
                //         } else {
                //             return '#ECFAF0';
                //         }
                //     }, $mainQuery),
                //     'borderColor' => array_map(function($value) {
                //         if ($value < 50) {
                //             return '#FF3232';
                //         } elseif ($value >= 50 && $value <= 69) {
                //             return '#FFAB33';
                //         } else {
                //             return '#00CC66';
                //         }
                //     }, $mainQuery),
            
                //     'tension' => 0.3
                // ],
        
            ],
            

            'labels' => $labels,
        ];
    }

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
                            callback: (value) => (Number(value).toFixed(1)) + 's',
                        },
                    },
                    x: {
                        reverse: true,
                        stacked: true,
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
