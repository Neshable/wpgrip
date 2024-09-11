<?php

namespace App\Jobs;

use App\Models\Site;
use App\Services\SSHService;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Illuminate\Support\Facades\Storage;

use App\Services\WPCliService;
use Filament\Notifications\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckDBStructure implements ShouldQueue
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
            // Init a new connection to the server.
            $connection = new SSHService( $this->site->ssh_user, $this->server->ip, 22, $this->user->ssh_private );
             // If we don't have connection abort and send notification.
             if ( !$connection->active ) 
             {
                 Notification::make()
                     ->title('Login failed: Unauthorized.')
                     ->danger()
                     ->duration(5000)
                     ->send();
         
                 return false;
             }

            // Brake down into tables. Here, we specify the maximum gzip compression level of 9:
            $output = $connection->ssh->exec('cd ' . $this->site->dir_path . ' && wp db size --tables --size_format=kb --format=json');
            // Close the connection if we don't need it.
            $connection->close();

            if ( $output ) {
                // Clean the JSON from any warning messages we may have.
                // @todo extract to helper method.
                preg_match("/\[[^\]]*\]/", $output, $matches);
                $this->save_to_db( $matches[0] );
                // Send notification.
                Notification::make()
                    ->title('DB Structure pulled.')
                    ->success()
                    ->duration(5000)
                    ->send();
            } 
    
        }
        
    }

    /**
     * Save the result in the DB
     *
     * @return void
     */
    public function save_to_db( $output )
    {
        $get_meta_model = $this->site->sitemeta;
        $get_meta_model->db_tables = $output;
        $get_meta_model->save();

    }

         


}
