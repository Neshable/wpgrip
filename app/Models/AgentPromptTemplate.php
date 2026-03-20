<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPromptTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'content',
        'variables',
        'is_default',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_default' => 'boolean',
    ];

    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first()
            ?? static::where('slug', 'site-agent-default')->first();
    }
}
