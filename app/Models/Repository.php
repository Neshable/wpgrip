<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'site_id',
        'type',
        'path',
        'provider',
        'remote',
        'secret',
        'branch'
    ];

    protected function commits(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    } 

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($repository) {
            // Generate a unique webhook token
            $repository->webhook = 'webhook_' . Str::random(40);
        });
    }

    /**
     * Get the post that owns the comment.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // This can belong to many sites now
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_repositories')
                    ->withPivot(['id', 'path', 'branch', 'status_text', 'status']);
    }
    
    
     /**
     * Get the owner of this site
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
