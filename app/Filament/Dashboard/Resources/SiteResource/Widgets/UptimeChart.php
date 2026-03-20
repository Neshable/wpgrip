<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\MonitorLog;
use Carbon\Carbon;
use Filament\Support\RawJs;

class UptimeChart extends ChartWidget
{
	protected ?string $heading = 'Uptime Monitor Logs - Last 28 Days';

	protected ?string $description = 'Green shows uptime, red shows downtime (any downtime is made visible)';

	protected ?string $pollingInterval = null;

	/**
	 * Get the site ID from the current route record
	 *
	 * @return int|null
	 */
	protected function getSiteId(): ?int
	{
		return request()->route('record');
	}

	/**
	 * Get uptime chart data for the last 28 days
	 *
	 * @return array
	 */
	protected function getData(): array
	{
		$siteId = $this->getSiteId();
		
		if (!$siteId) {
			return [
				'datasets' => [],
				'labels' => [],
			];
		}

		// Get the last 28 days
		$days = collect(range(0, 27))->map(function ($day) {
			return Carbon::now()->subDays($day);
		})->reverse()->values();

		$labels = $days->map(function ($date) {
			return $date->format('M j');
		})->toArray();

		// Get all downtime logs for this site in the last 28 days
		$downtimeLogs = MonitorLog::where('site_id', $siteId)
			->where('uptime_status', 'down')
			->where('created_at', '>=', Carbon::now()->subDays(28))
			->get()
			->groupBy(function ($log) {
				return Carbon::parse($log->created_at)->format('Y-m-d');
			});

		// Calculate minutes per day (24 hours * 60 minutes = 1440 minutes)
		$minutesPerDay = 1440;

		// Prepare data for each day
		$uptimeData = [];
		$downtimeData = [];

		foreach ($days as $date) {
			$dateKey = $date->format('Y-m-d');
			
			// Get downtime logs for this specific day
			$dayDowntimeLogs = $downtimeLogs->get($dateKey, collect());
			
			// Calculate total downtime minutes for the day
			// Since logs are created only when site is down, and checks are every 5 minutes,
			// each log represents approximately 5 minutes of downtime
			$downtimeMinutes = $dayDowntimeLogs->count() * 5;
			
			// Cap downtime at maximum minutes per day
			$downtimeMinutes = min($downtimeMinutes, $minutesPerDay);
			
			// Calculate uptime minutes
			$uptimeMinutes = $minutesPerDay - $downtimeMinutes;
			
			// Convert to actual percentages
			$uptimePercentage = ($uptimeMinutes / $minutesPerDay) * 100;
			$downtimePercentage = ($downtimeMinutes / $minutesPerDay) * 100;
			
			if ($downtimePercentage > 0) {
				// If there's any downtime, make it visible with minimum 3% height
				$visibleDowntimePercentage = max($downtimePercentage, 3);
				$adjustedUptimePercentage = 100 - $visibleDowntimePercentage;
				
				$uptimeData[] = round($adjustedUptimePercentage, 2);
				$downtimeData[] = round($visibleDowntimePercentage, 2);
			} else {
				// Perfect uptime day
				$uptimeData[] = 100;
				$downtimeData[] = 0;
			}
		}

		return [
			'datasets' => [
				[
					'label' => 'Uptime',
					'data' => $uptimeData,
					'backgroundColor' => '#10b981',
					'borderColor' => '#059669',
					'borderWidth' => 1,
				],
				[
					'label' => 'Downtime',
					'data' => $downtimeData,
					'backgroundColor' => '#ef4444',
					'borderColor' => '#dc2626',
					'borderWidth' => 1,
				],
			],
			'labels' => $labels,
		];
	}

	/**
	 * Get the chart type
	 *
	 * @return string
	 */
	protected function getType(): string
	{
		return 'bar';
	}

	/**
	 * Get chart options for stacked bar chart
	 *
	 * @return RawJs
	 */

     protected function getOptions(): RawJs
     {
         return RawJs::make(<<<JS
             {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false,
                        text: 'Daily Uptime Status - Last 28 Days'
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return 'Date: ' + context[0].label;
                                },
                                label: function(context) {
                                    const label = context.dataset.label;
                                    const value = context.parsed.y;

                                    return label + ': ' + value.toFixed(1) + '%';
                                }
                            }
                    }
                },
                 scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: true,
                            color: '#e5e7eb',
                            gap: 2,

                        }
                    },
                     y: {
                         stacked: true,
                         beginAtZero: true,
                         display: false,
                         ticks: {
                             callback: (value) => value + '%',
                         },
                     },
                 },
    
             }
         JS);
     }

    // protected function getOptions(): RawJs
    // {
    //     return RawJs::make(<<<JS
    //         {
    //             responsive: true,
    //             maintainAspectRatio: false,
    //             plugins: {
    //                 title: {
    //                     display: true,
    //                     text: "Daily Uptime Status - Last 28 Days"
    //                 },
    //                 legend: {
    //                     display: true,
    //                     position: "top"
    //                 },
    //                 tooltip: {
    //                         callbacks: {
    //                             title: function(context) {
    //                                 return "Date: " + context[0].label;
    //                             },
    //                             label: function(context) {
    //                                 const label = context.dataset.label;
    //                                 const value = context.parsed.y;
                                    
    //                                 if (label === "Uptime") {
    //                                     return "🟢 " + label + ": " + value.toFixed(1) + %';
    //                                 } else if (label === "Downtime") {
    //                                     return "🔴 " + label + ": " + value.toFixed(1) + %';
    //                                 }
    //                                 return label + ": " + value.toFixed(1) + %';
    //                             }
    //                         }
    //                 }
    //             },
    //             scales: {
    //                 x: {
    //                     stacked: true,
    //                     grid: {
    //                         display: false
    //                     }
    //                 },
    //                 y: {
    //                     stacked: true,
    //                     beginAtZero: true,
    //                     max: 100,
    //                     ticks: {
    //                         callback: function(value) {
    //                             return value + %';
    //                         }
    //                     },
    //                     title: {
    //                         display: true,
    //                         text: "Uptime Percentage"
    //                     }
    //                 }
    //             },
    //             interaction: {
    //                 intersect: false,
    //                 mode: "index"
    //             }
    //         }
    //     JS);
    // }

}
