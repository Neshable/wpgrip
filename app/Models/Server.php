<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Enums\HostingProvider;
use App\Enums\ServerType;

use Filament\Facades\Filament; 
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Server extends Model
{
    use HasFactory;

    protected $fillable = ['name','ip', 'private_ip', 'ssh_port', 'provider', 'type', 'tenant_id'];

    protected $casts = [
        'provider' => HostingProvider::class,
        'type' => ServerType::class,   
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($server) {
            $server->tenant_id = Filament::getTenant()->id;
        });
    }

     /**
     * Get the websites for this client.
     */
    // public function sites(): HasMany
    // {
    //     return $this->hasMany(Site::class);
    // }

    /**
     * Get the owner of this server
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


    // /**
    //  * When saving, apply the owner.
    //  *
    //  * @param array $options
    //  * @return void
    //  */
    // public function save(array $options = array())
    // {
    //     // @todo halt if not exist.
    //     $this->tenant_id = Filament::getTenant()->id;

    //     parent::save($options);
    // }
}
