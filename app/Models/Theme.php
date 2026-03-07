<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description'
    ];

     /**
     * The roles that belong to the user.
     */
    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'theme_site')
            ->withPivot(['version', 'update_version', 'status', 'is_vulnerable', 'vuln_ids']);
    }
}
