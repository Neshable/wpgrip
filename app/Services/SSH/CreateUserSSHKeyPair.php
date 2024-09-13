<?php

namespace App\Services\SSH;

use App\Models\User;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

use Filament\Notifications\Notification;

class CreateUserSSHKeyPair 
{
    /**
     * The uuid for the password.
     *
     * @var [type]
     */
    public $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $uuid )
    {
        // Get original production site.
        $this->uuid = $uuid;
    }

    /**
     * Execute the service.
     *
     * @return void
     */
    public function generate()
    {
        $private = RSA::createKey( 2048 );

        if ( $private )
        {
            $private = $private->withPadding( RSA::SIGNATURE_PKCS1 );
            $private = $private->withHash('sha256');
            $private = $private->withPassword( $this->uuid ); // $this->user->password

            return $private;
        }

        return false;

    

    }

    public function removeCommentsFromKey( string $key )
    {
        return str_replace('phpseclib-generated-key', 'wpgrip', $key );
    }
}
