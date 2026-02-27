<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShelleyInstance extends Model
{
    protected $fillable = [
        'site_id',
        'port',
        'pid',
        'workdir',
        'last_accessed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
