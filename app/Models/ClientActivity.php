<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientActivity extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity for a client.
     */
    public static function log(Client $client, string $type, string $description, ?int $userId = null, ?array $metadata = null): static
    {
        return static::create([
            'client_id'   => $client->id,
            'user_id'     => $userId ?? auth()->id(),
            'type'        => $type,
            'description' => $description,
            'metadata'    => $metadata,
        ]);
    }
}
