<?php

namespace App\Jobs;

use App\Models\Site;
use App\Models\User;
use App\Models\Backup;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetSiteStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site )
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site->dir_path ) 
        {
            if ( $this->site->server && $this->site->owner )
            {
                $owner = User::find( $this->site->owner );

                // Get this from the server model.
                $ssh = new SSH2( $this->site->server->ip , 22); // @todo extract to service.
                // Get the key from DB, decrypt and load.
                $key = PublicKeyLoader::load( $owner->ssh_private );

                // Make the connection.
                if ( !$ssh->login( $this->site->ssh_user, $key ) ) {
                    throw new \Exception('Login failed');
                }
                $ssh->setKeepAlive(10);
                // By default $ssh->exec() returns both stdout and stderr. To suppress stderr you can call QuiteMode
                $ssh->enableQuietMode();
                // $ssh->enablePTY();
                $ssh->setTimeout(600);
                // Brake down into tables. Here, we specify the maximum gzip compression level of 9:
                $output = $ssh->exec('cd ' . $this->site->dir_path . ' && wp db export - | gzip -9');

                $path = $this->generate_filename();


                
            }

        }
        
    }


}
