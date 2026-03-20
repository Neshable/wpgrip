<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiTokenUsage extends Model
{
    protected $table = 'ai_token_usage';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'site_id',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'model',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'uuid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get total tokens used by a tenant in the current calendar month.
     */
    public static function monthlyUsage(string $tenantId): int
    {
        return (int) static::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_tokens');
    }

    /**
     * Monthly token limit per workspace.
     */
    public const MONTHLY_LIMIT = 5_000_000;

    /**
     * Check if a tenant has exceeded the monthly limit.
     */
    public static function hasExceededLimit(string $tenantId): bool
    {
        return static::monthlyUsage($tenantId) >= static::MONTHLY_LIMIT;
    }

    /**
     * Tokens remaining this month.
     */
    public static function remaining(string $tenantId): int
    {
        return max(0, static::MONTHLY_LIMIT - static::monthlyUsage($tenantId));
    }
}
