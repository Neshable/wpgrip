<?php

namespace App\Models;

use App\Services\SubscriptionManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Services\SSH\CreateUserSSHKeyPair;
use Illuminate\Support\Facades\Crypt;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uuid',
        'is_name_auto_generated',
        'enable_slack',
        'slack_webhook',
        'enable_email',
        'email',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($tenant) {

           // Generate a unique SSH Key Pair
           $key_pair = new CreateUserSSHKeyPair( $tenant->uuid );
           $private = $key_pair->generate();
           
           if ( $private ) {
               // Store the private SSH key in the profile.
               $tenant->ssh_private =  Crypt::encryptString( $private->toString('OpenSSH') );
               // Get the public and save it in the profile.
               $public = $private->getPublicKey();
               $tenant->ssh_public = Crypt::encryptString( str_replace('phpseclib-generated-key', 'wpgrip', $public->toString('OpenSSH') ));
               
               $tenant->save();
            }
           
        });

    }

    public function generateKey()
    {
         // Generate a unique SSH Key Pair
         $key_pair = new CreateUserSSHKeyPair( $this->uuid );
         $private = $key_pair->generate();
         
         if ( $private ) {
             // Store the private SSH key in the profile.
             $this->ssh_private =  Crypt::encryptString( $private->toString('OpenSSH') );
             // Get the public and save it in the profile.
             $public = $private->getPublicKey();
             $this->ssh_public = Crypt::encryptString( str_replace('phpseclib-generated-key', 'wpgrip', $public->toString('OpenSSH') ));
             
            return $this->save();
          }

          return false;

    }

    /**
     * Get the user SSH private key
     *
     * @return void
     */
    public function getPrivateKey()
    {
        // Get the user SSH Private.
        if ( $this->ssh_private ) 
        {
            try 
            {
                $ssh_private = Crypt::decryptString($this->ssh_private);
                return $ssh_private;
            } catch (DecryptException $e) {
                // THrow an error or notification.
                return false;
            }
        }

        return false;
        
    }

    /**
     * Get the user SSH public key
     *
     * @return void
     */
    public function getPublicKey()
    {
        // Get the user SSH Private.
        if ( $this->ssh_public ) 
        {
            try 
            {
                $ssh_public = Crypt::decryptString( $this->ssh_public );
                return $ssh_public;
            } catch (DecryptException $e) {
                // THrow an error or notification.
                return false;
            }
        }

        return false;
        
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(TenantUser::class)->withPivot('id')->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the backups for this site.
     */
    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function repositories()
    {
        return $this->hasMany(Repository::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function backups()
    {
        return $this->hasMany(Backup::class);
    }

    public function snapshots()
    {
        return $this->hasMany(Snapshot::class);
    }


    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stripeData(): HasOne
    {
        return $this->hasOne(UserStripeData::class);
    }

    public function subscriptionProductMetadata()
    {
        /** @var SubscriptionManager $subscriptionManager */
        $subscriptionManager = app(SubscriptionManager::class);

        return $subscriptionManager->getTenantSubscriptionProductMetadata($this);
    }

    public function getTenantPath(): string
    {
        return 'tenants/' . $this->uuid;
    }
}
