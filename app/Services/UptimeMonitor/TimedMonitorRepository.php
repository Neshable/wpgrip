<?php

namespace App\Services\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\MonitorCollection;
use Spatie\UptimeMonitor\MonitorRepository;

/**
 * Extends Spatie's MonitorRepository to return TimedMonitorCollection
 * instances instead of the default MonitorCollection.
 */
class TimedMonitorRepository extends MonitorRepository
{
    public static function getEnabled(): Collection
    {
        $monitors = parent::getEnabled();

        return TimedMonitorCollection::make($monitors)->sortByHost();
    }

    public static function getForUptimeCheck(): MonitorCollection
    {
        $monitors = parent::getForUptimeCheck();

        return TimedMonitorCollection::make($monitors)->sortByHost();
    }
}
