<?php

namespace App\Jobs;

use App\Models\Site;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Filament\Notifications\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ListAllWPPlugins implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

     /**
     * The User instance.
     *
     * @var \App\Models\User
     */
    public $user;

    
     /**
     * The server instance.
     *
     * @var \App\Models\server
     */
    public $server;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site )
    {
        $this->site = $site;
        $this->server = $this->site->server;
        $this->user = auth()->user();
    }

    // wp --ssh=root@20.234.150.120/home/brera/webapps/brera-in-humanitas plugin list --format=json --allow-roo

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        if ( $this->server->ip ) {
            
            // Get this from the server model.
            $ssh = new SSH2( $this->server->ip , 22);
            // Get the key from DB, decrypt and load.
            $key = PublicKeyLoader::load($this->user->ssh_private);
            // Make the connection.
            if ( !$ssh->login($this->site->ssh_user, $key ) ) {
                throw new \Exception('Login failed');
            }
            // $ssh->enablePTY();

            $ssh->setTimeout(360);
            $output = $ssh->exec('cd ' . $this->site->dir_path . ' && wp plugin list --fields=name,status,update,version,update_version,update_package,title,description --format=json 2> /dev/null');
            $ssh->exec('exit');

            $this->user->notify(
                Notification::make()
                    ->title('All good')
                    ->success()
                    ->body( 'something went wrong' ) 
                    ->toDatabase(),
            );

            
            if ( $output ) {
                 // Clean the JSON from any warning messages we may have.
                 // @todo extract to helper method.
                preg_match("/\[[^\]]*\]/", $output, $matches);
                $this->save_to_db($matches[0]);
            }
    
        }
        
    }

    /**
     * Save the result in the DB
     *
     * @return void
     */
    public function save_to_db( $plugin_list )
    {
        $get_meta_model = $this->site->sitemeta;
        $get_meta_model->plugins = $plugin_list;
        $get_meta_model->save();

    }

         


}
