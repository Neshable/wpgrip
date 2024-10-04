<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceData extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', 'performance', 'fcp', 'total_blocking_time', 'strategy',
        'speed_index', 'dom_size', 'lcp', 'time_interactive', 'network_server_latency', 'fmp', 'server_response_time'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
