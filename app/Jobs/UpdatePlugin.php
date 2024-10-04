<?php

namespace App\Jobs;

use App\Models\Site;
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

class UpdatePlugin implements ShouldQueue
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
     * The name of the plugin
     * 
     * @var string
     */
    public $plugin;

    /**
     * The version we need to update to.
     * 
     * @var string
     */
    public $version;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $plugin, ?float $version = null )
    {
        $this->site = $site;
        $this->plugin = $plugin;
        $this->version = $version;
        $this->user = auth()->user();
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
            // Get this from the server model.
            $ssh = new SSH2('20.234.150.120', 22);
            // Get the key from DB, decrypt and load.
            $key = PublicKeyLoader::load($this->user->ssh_private);
            // Make the connection.
            if ( !$ssh->login('brera', $key ) ) {
                throw new \Exception('Login failed');
            }

            // By default $ssh->exec() returns both stdout and stderr. To suppress stderr you can call QuiteMode
            // $ssh->enableQuietMode();
            // $ssh->enablePTY();
            $ssh->setTimeout(550);
            $output = $ssh->exec('cd /home/brera/webapps/brera-in-humanitas && wp plugin update ' . $this->plugin . ' --format=json');
            $ssh->exec('exit');

            // Find plugin version with the array and update the DB.
            // $sitemeta_model = $this->site->sitemeta;
            // $plugin_list = $sitemeta_model->plugins;
            // $plugin_list = json_decode($plugin_list);
   
            // $sitemeta_model->save();

            Notification::make()
            ->title('Plugin updated.')
            ->success()
            ->send();
           
      
            // Send notification to the user.
            // if ($backup->exists) {
                
            //     $this->user->notify(
            //         Notification::make()
            //             ->title('Database backup complete.')
            //             ->success()
            //             ->body( 'Database backup for ' . $this->site->name . ' is complete.' ) 
            //             ->toDatabase(),
            //     );
            // } 

        }
        
    }


}
