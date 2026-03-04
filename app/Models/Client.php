<?php

namespace App\Models;

use App\Enums\ClientStatus;
use App\Enums\ClientSource;
use App\Observers\ClientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Facades\Filament;

#[ObservedBy(ClientObserver::class)]
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company',
        'website',
        'email',
        'phone',
        'billing_email',
        'country',
        'address',
        'city',
        'postal_code',
        'timezone',
        'status',
        'source',
        'currency',
        'monthly_value',
        'contract_start',
        'contract_end',
        'notes',
        'tags',
        'avatar_path',
        'tenant_id',
    ];

    protected $casts = [
        'status'         => ClientStatus::class,
        'source'         => ClientSource::class,
        'monthly_value'  => 'decimal:2',
        'contract_start' => 'date',
        'contract_end'   => 'date',
        'tags'           => 'array',
    ];

    protected $attributes = [
        'status'   => 'active',
        'currency' => 'USD',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (Filament::getTenant()) {
                $client->tenant_id = Filament::getTenant()->id;
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ClientActivity::class)->latest();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getPrimaryContactAttribute(): ?ClientContact
    {
        return $this->contacts->firstWhere('is_primary', true)
            ?? $this->contacts->first();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company
            ? "{$this->company} ({$this->name})"
            : $this->name;
    }

    /**
     * Unique servers this client's sites are hosted on.
     */
    public function getServersCountAttribute(): int
    {
        return $this->sites()->distinct('server_id')->count('server_id');
    }

    public function getIsContractExpiringAttribute(): bool
    {
        if (!$this->contract_end) {
            return false;
        }

        return $this->contract_end->isBetween(now(), now()->addDays(30));
    }

    // -------------------------------------------------------------------------
    // Activity logging helpers
    // -------------------------------------------------------------------------

    public function logActivity(string $type, string $description, ?array $metadata = null): ClientActivity
    {
        return ClientActivity::log($this, $type, $description, null, $metadata);
    }
}
