<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false; // only created_at, set by DB default

    protected $fillable = [
        'tenant_id',
        'user_id',
        'site_id',
        'action',
        'category',
        'subject_label',
        'meta',
        'ip_address',
        'status',
        'created_at',
    ];

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Human-readable action label, e.g. "plugin.updated" → "Plugin Updated"
     */
    public function getActionLabelAttribute(): string
    {
        return ucwords(str_replace(['.', '_'], ' ', $this->action));
    }

    /**
     * Resolved actor name: user display name or "System" for queue jobs.
     */
    public function getActorAttribute(): string
    {
        return $this->user?->name ?? 'System';
    }
}
