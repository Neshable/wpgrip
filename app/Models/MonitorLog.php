<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'url',
        'uptime_status',
        'uptime_check_failure_reason',
        'certificate_status',
        'certificate_issuer',
        'certificate_expiration_date',
        'certificate_check_failure_reason',
        'uptime_check_method',
        'uptime_check_payload',
        'uptime_check_additional_headers',
        'uptime_check_response_checker',
        'response_time_ms',
        'dns_time_ms',
        'connect_time_ms',
        'tls_time_ms',
        'ttfb_ms',
        'transfer_time_ms',
        'response_status_code',
        'response_body',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'certificate_expiration_date' => 'datetime',
        'response_time_ms' => 'integer',
        'dns_time_ms' => 'float',
        'connect_time_ms' => 'float',
        'tls_time_ms' => 'float',
        'ttfb_ms' => 'float',
        'transfer_time_ms' => 'float',
        'response_status_code' => 'integer',
    ];

    /**
     * Get the site that owns the monitor log.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Scope a query to only include logs for a specific site.
     */
    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope a query to only include logs with a specific uptime status.
     */
    public function scopeWithUptimeStatus($query, $status)
    {
        return $query->where('uptime_status', $status);
    }

    /**
     * Scope a query to only include logs with a specific certificate status.
     */
    public function scopeWithCertificateStatus($query, $status)
    {
        return $query->where('certificate_status', $status);
    }

    /**
     * Scope a query to only include logs from the last X hours.
     */
    public function scopeLastHours($query, $hours)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}