<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteMeta extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sites_meta';

    protected $fillable = [
        'site_id',
        'is_vulnerable',
	];

    /**
     * Get the site that owns the data.
     */
    public function site()
    {
        return $this->belongsTo( Site::class );
    }
}
