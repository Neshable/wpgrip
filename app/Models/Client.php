<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Filament\Facades\Filament; 

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
		'name',
		'email',
		'country',
		'notes',
        'tenant_id'
	];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            $client->tenant_id = Filament::getTenant()->id;
        });
    }

     /**
     * Get the websites for this client.
     */
    // public function sites()
    // {
    //     return $this->hasMany(Site::class);
    // }

    /**
     * Get the owner of this site
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


}
