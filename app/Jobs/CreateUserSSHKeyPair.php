<?php

namespace App\Jobs;

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

class CreateUserSSHKeyPair implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * User Model
     * 
     * @var User
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( User $user )
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( !$this->user->ssh_private ) {
            $private = RSA::createKey( 2048 );

            if ( $private )
            {
                $private = $private->withPadding( RSA::SIGNATURE_PKCS1 );
                $private = $private->withHash('sha256');
                $private = $private->withPassword( 'd728dah!83d#hf' ); // $this->user->password
            }

            $this->user->ssh_private =  Crypt::encryptString( $private->toString('OpenSSH') );

            if ( $private ) {
                $public = $private->getPublicKey();
                $this->user->ssh_public = Crypt::encryptString( $this->removeCommentsFromKey( $public->toString('OpenSSH') ) );
            }

            if ( $this->user->save() ) {
                Notification::make()
                    ->title('Personal SSH Key generated and saved')
                    ->success()
                    ->duration(5000)
                    ->send();
            }


        }
        
        // dd($private->__toString() );

        // $ciphertext = $private->getPublicKey()->encrypt($plaintext);
        // echo $private->decrypt($ciphertext);


    }

    public function removeCommentsFromKey( string $key )
    {
        return str_replace('phpseclib-generated-key', 'wpgrip', $key );
    }
}
