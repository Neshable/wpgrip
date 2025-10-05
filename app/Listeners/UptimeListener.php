<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Services\SlackNotifications;
use App\Models\MonitorLog;

use Spatie\UptimeMonitor\Events\UptimeCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        //
    }

     /**
     * Handle the UptimeCheckFailed event.
     */
    public function handleUptimeCheckFailed( UptimeCheckFailed $event ): void
    {
        $max_attempts = 3;
        $attempt = (int) ( $event->monitor->uptime_check_times_failed_in_a_row ?? 0 );

        // Send only for the first three consecutive failures, then snooze until recovery.
        if ( $attempt >= 1 && $attempt <= $max_attempts && $event->uptime_status == 'down' )
        {
            SlackNotifications::sendUptimeFailed( $event->monitor, $attempt );
        }

        // Handle the uptime log.
        $this->handleUptimeLog( $event->monitor );
        
    }

    /**
     * Handle the UptimeCheckFailed event.
     */
    public function handleUptimeCheckRecovered( UptimeCheckRecovered $event ): void
    {
        SlackNotifications::sendUptimeRecovered( $event->monitor );
    }


    /**
     * Handle the UptimeCheckSucceeded event.
     */
    public function handleUptimeCheckSucceeded( UptimeCheckSucceeded $event ): void
    {
        //SlackNotifications::sendUptimeFailed( $event->monitor );
        //$this->handleUptimeLog( $event->monitor );
    }

    public function handleUptimeLog( Monitor $monitor )
    {
        if ( isset( $monitor->site_id ) ) 
        {
            $monitorLog = new MonitorLog();
            $monitorLog->site_id = $monitor->site_id;
            $monitorLog->url = $monitor->url;
            $monitorLog->uptime_status = $monitor->uptime_status;
            $monitorLog->uptime_check_failure_reason = $monitor->uptime_check_failure_reason;
            $monitorLog->save();
        }
        
    }
}
