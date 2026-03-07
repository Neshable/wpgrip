<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\UptimeMonitor\Models\Monitor;

class UptimeMonitor extends Monitor
{
    protected $table = 'monitors';

    /**
     * Transient property set by TimedMonitorCollection after each check.
     * Contains: total_ms, dns_ms, connect_ms, tls_ms, ttfb_ms, transfer_ms
     */
    public ?array $responseTimingData = null;

    /**
     * Get the site.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
