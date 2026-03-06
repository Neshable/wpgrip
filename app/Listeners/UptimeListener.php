<?php

namespace App\Listeners;

use App\Services\SlackNotifications;
use App\Models\MonitorLog;

use Spatie\UptimeMonitor\Events\UptimeCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeListener
{
    /**
     * Handle the UptimeCheckFailed event.
     */
    public function handleUptimeCheckFailed(UptimeCheckFailed $event): void
    {
        $monitor = $event->monitor;
        $maxAttempts = 3;
        $attempt = (int) ($monitor->uptime_check_times_failed_in_a_row ?? 0);

        // Send only for the first three consecutive failures, then snooze until recovery.
        if ($attempt >= 1 && $attempt <= $maxAttempts) {
            SlackNotifications::sendUptimeFailed($monitor, $attempt);
        }

        // Log the downtime event.
        $this->handleUptimeLog($monitor);
    }

    /**
     * Handle the UptimeCheckRecovered event.
     */
    public function handleUptimeCheckRecovered(UptimeCheckRecovered $event): void
    {
        SlackNotifications::sendUptimeRecovered($event->monitor);
    }

    /**
     * Handle the UptimeCheckSucceeded event.
     */
    public function handleUptimeCheckSucceeded(UptimeCheckSucceeded $event): void
    {
        // Log successful checks so the uptime chart has data.
        $this->handleUptimeLog($event->monitor);
    }

    /**
     * Create a MonitorLog entry for chart/history tracking.
     */
    public function handleUptimeLog(Monitor $monitor): void
    {
        if (!empty($monitor->site_id)) {
            MonitorLog::create([
                'site_id' => $monitor->site_id,
                'url' => (string) $monitor->url,
                'uptime_status' => $monitor->uptime_status,
                'uptime_check_failure_reason' => $monitor->uptime_check_failure_reason,
                'certificate_status' => $monitor->certificate_status ?? null,
                'certificate_issuer' => $monitor->certificate_issuer ?? null,
                'certificate_expiration_date' => $monitor->certificate_expiration_date ?? null,
            ]);
        }
    }
}
