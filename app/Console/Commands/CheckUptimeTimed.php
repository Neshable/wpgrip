<?php

namespace App\Console\Commands;

use App\Services\UptimeMonitor\TimedMonitorRepository;
use Spatie\UptimeMonitor\Commands\BaseCommand;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * Drop-in replacement for Spatie's monitor:check-uptime that uses
 * TimedMonitorCollection to capture Guzzle response timing data.
 */
class CheckUptimeTimed extends BaseCommand
{
    protected $signature = 'monitor:check-uptime
                            {--url= : Only check these urls}
                            {--f|force : Force run all monitors}';

    protected $description = 'Check the uptime of all sites (with response timing)';

    public function handle(): void
    {
        $monitors = $this->option('force')
            ? TimedMonitorRepository::getEnabled()
            : TimedMonitorRepository::getForUptimeCheck();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the uptime of ' . count($monitors) . ' monitors...');

        $monitors->checkUptime();

        $this->info('All done!');
    }
}
