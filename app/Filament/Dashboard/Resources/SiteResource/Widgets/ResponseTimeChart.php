<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Widgets;

use App\Models\MonitorLog;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ResponseTimeChart extends ChartWidget
{
    protected static ?string $heading = 'Response Time - Last 24 Hours';

    protected static ?string $description = 'Breakdown: DNS, Connect, TLS, TTFB, Transfer (in ms)';

    protected static ?string $pollingInterval = null;

    protected static ?string $maxHeight = '300px';

    protected function getSiteId(): ?int
    {
        return request()->route('record');
    }

    protected function getData(): array
    {
        $siteId = $this->getSiteId();

        if (! $siteId) {
            return ['datasets' => [], 'labels' => []];
        }

        $logs = MonitorLog::where('site_id', $siteId)
            ->whereNotNull('response_time_ms')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        if ($logs->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $labels = $logs->map(fn ($log) => Carbon::parse($log->created_at)->format('H:i'))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $logs->pluck('response_time_ms')->toArray(),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 0,
                    'pointHitRadius' => 10,
                ],
                [
                    'label' => 'TTFB',
                    'data' => $logs->pluck('ttfb_ms')->toArray(),
                    'borderColor' => '#10b981',
                    'borderWidth' => 1.5,
                    'tension' => 0.3,
                    'pointRadius' => 0,
                    'pointHitRadius' => 10,
                ],
                [
                    'label' => 'TLS',
                    'data' => $logs->pluck('tls_time_ms')->toArray(),
                    'borderColor' => '#f59e0b',
                    'borderWidth' => 1.5,
                    'tension' => 0.3,
                    'pointRadius' => 0,
                    'pointHitRadius' => 10,
                ],
                [
                    'label' => 'Connect',
                    'data' => $logs->pluck('connect_time_ms')->toArray(),
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 1.5,
                    'tension' => 0.3,
                    'pointRadius' => 0,
                    'pointHitRadius' => 10,
                ],
                [
                    'label' => 'DNS',
                    'data' => $logs->pluck('dns_time_ms')->toArray(),
                    'borderColor' => '#8b5cf6',
                    'borderWidth' => 1.5,
                    'tension' => 0.3,
                    'pointRadius' => 0,
                    'pointHitRadius' => 10,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'line',
                            padding: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + (context.parsed.y != null ? context.parsed.y.toFixed(1) + 'ms' : 'N/A');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxTicksLimit: 12,
                            maxRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + 'ms';
                            }
                        },
                        grid: {
                            color: 'rgba(107, 114, 128, 0.1)'
                        }
                    }
                }
            }
        JS);
    }
}
