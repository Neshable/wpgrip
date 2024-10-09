<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'type', 'model', 'stats', 'reccomendations', 'ai_response', 'medium_issues', 'urgent_issues', 'created_at'
    ];

}
