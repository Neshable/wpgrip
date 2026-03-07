<?php

namespace App\Services\UptimeMonitor;

use Generator;
use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;
use Spatie\UptimeMonitor\MonitorCollection;
use Spatie\UptimeMonitor\Models\Monitor;

class TimedMonitorCollection extends MonitorCollection
{
    /**
     * Timing data keyed by monitor index.
     */
    protected array $timings = [];

    /**
     * Override checkUptime to capture timing data and attach it to monitors.
     */
    public function checkUptime(): void
    {
        $this->resetItemKeys();

        (new EachPromise($this->getPromises(), [
            'concurrency' => config('uptime-monitor.uptime_check.concurrent_checks'),
            'fulfilled' => function (ResponseInterface $response, $index) {
                $monitor = $this->getMonitorAtIndex($index);

                // Attach timing data to the monitor instance for the listener to pick up.
                $monitor->responseTimingData = $this->timings[$index] ?? null;

                ConsoleOutput::info("Could reach {$monitor->url}");

                $monitor->uptimeRequestSucceeded($response);
            },

            'rejected' => function (TransferException $exception, $index) {
                $monitor = $this->getMonitorAtIndex($index);

                // Attach whatever timing we got (partial timings on failure).
                $monitor->responseTimingData = $this->timings[$index] ?? null;

                ConsoleOutput::error("Could not reach {$monitor->url} error: `{$exception->getMessage()}`");

                $monitor->uptimeRequestFailed($exception->getMessage());
            },
        ]))->promise()->wait();
    }

    /**
     * Override getPromises to inject on_stats callback.
     */
    protected function getPromises(): Generator
    {
        $client = GuzzleFactory::make(
            config('uptime-monitor.uptime_check.guzzle_options', []),
            config('uptime-monitor.uptime_check.retry_connection_after_milliseconds', 100)
        );

        foreach ($this->items as $index => $monitor) {
            ConsoleOutput::info("Checking {$monitor->url}");

            $currentIndex = $index;

            $promise = $client->requestAsync(
                $monitor->uptime_check_method,
                $monitor->url,
                array_filter([
                    'connect_timeout' => config('uptime-monitor.uptime_check.timeout_per_site'),
                    'headers' => $this->getPromiseHeaders($monitor),
                    'body' => $monitor->uptime_check_payload,
                    'on_stats' => function (TransferStats $stats) use ($currentIndex) {
                        $this->captureTimings($currentIndex, $stats);
                    },
                ])
            )->then(
                function (ResponseInterface $response) {
                    return $response;
                },
                function (TransferException $exception) {
                    if (in_array($exception->getCode(), config('uptime-monitor.uptime_check.additional_status_codes', []))) {
                        return $exception->getResponse();
                    }

                    throw $exception;
                }
            );

            yield $promise;
        }
    }

    /**
     * Capture timing data from Guzzle TransferStats.
     */
    protected function captureTimings(int $index, TransferStats $stats): void
    {
        $handlerStats = $stats->getHandlerStats();

        // All curl times are in seconds — convert to milliseconds.
        $namelookup = ($handlerStats['namelookup_time'] ?? 0) * 1000;
        $connect = ($handlerStats['connect_time'] ?? 0) * 1000;
        $pretransfer = ($handlerStats['pretransfer_time'] ?? 0) * 1000;
        $starttransfer = ($handlerStats['starttransfer_time'] ?? 0) * 1000;
        $total = ($stats->getTransferTime() ?? 0) * 1000;

        $this->timings[$index] = [
            'total_ms'    => round($total, 2),
            'dns_ms'      => round($namelookup, 2),
            'connect_ms'  => round($connect > 0 ? $connect - $namelookup : 0, 2),
            'tls_ms'      => round($pretransfer > 0 ? $pretransfer - $connect : 0, 2),
            'ttfb_ms'     => round($starttransfer > 0 ? $starttransfer - $pretransfer : 0, 2),
            'transfer_ms' => round($total > 0 ? $total - $starttransfer : 0, 2),
        ];
    }

    /**
     * Build headers for the promise. Mirrors parent's private promiseHeaders.
     */
    private function getPromiseHeaders(Monitor $monitor): array
    {
        return collect([])
            ->merge(['User-Agent' => config('uptime-monitor.uptime_check.user_agent')])
            ->merge(config('uptime-monitor.uptime_check.additional_headers') ?? [])
            ->merge($monitor->uptime_check_additional_headers)
            ->toArray();
    }
}
