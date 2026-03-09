<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StagingSync extends Model
{
    protected $fillable = [
        'production_site_id',
        'staging_site_id',
        'tenant_id',
        'status',
        'strategy',
        'sync_db',
        'sync_uploads',
        'db_size_bytes',
        'uploads_size_bytes',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'sync_db' => 'boolean',
        'sync_uploads' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function productionSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'production_site_id');
    }

    public function stagingSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'staging_site_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'uuid');
    }

    public function scopeLatestForStaging($query, int $stagingSiteId)
    {
        return $query->where('staging_site_id', $stagingSiteId)->latest();
    }

    public function markAs(string $status): self
    {
        $this->status = $status;

        if ($status === 'preflight') {
            $this->started_at = now();
        }

        if (in_array($status, ['completed', 'failed'])) {
            $this->completed_at = now();
        }

        $this->save();

        return $this;
    }

    public function fail(string $message): self
    {
        $this->error_message = $message;

        return $this->markAs('failed');
    }

    public function isRunning(): bool
    {
        return in_array($this->status, ['pending', 'preflight', 'syncing_db', 'syncing_uploads', 'replacing', 'cleanup']);
    }

    public function getDurationAttribute(): ?string
    {
        if (! $this->started_at) {
            return null;
        }

        $end = $this->completed_at ?? now();

        return $this->started_at->diffForHumans($end, true);
    }
}
