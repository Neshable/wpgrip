<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\UptimeMonitor\Models\Monitor;

class UptimeMonitor extends Monitor
{
    protected $table = 'monitors';

    /**
    * Get the site
    */
   public function site(): BelongsTo
   {
       return $this->belongsTo(Site::class);
   }
}
