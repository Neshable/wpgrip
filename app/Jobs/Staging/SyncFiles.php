<?php

namespace App\Jobs\Staging;

use App\Models\Site;
use App\Models\StagingSite;
use App\Models\User;
use App\Models\Backup;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncFiles implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The StagingSite instance.
     *
     * @var \App\Models\Site
     */
    public $staging_site;

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
        if ($this->batch()->cancelled()) 
        {
            // Determine if the batch has been cancelled...
            return;
        }
        
        if ( $this->site ) 
        {
            $this->staging_site =  $this->site->children()->first();
           
            if ( !$this->staging_site ) {
                return;
            }

            // Init a new connection to websites's production server.
            $connection = new SSHSiteConnect( $this->site );
            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();

                return false;
            }
          
            // Copy files to staging root - skip uploads and wp-config.php for now.
            $connection->exec('rsync -avzrS --exclude="wp-config.php" --exclude="wp-content/uploads" --exclude="node_modules"  ' . $this->site->dir_path . '/*' . ' ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip . ':' . $this->staging_site->dir_path );
            $connection->close();
  
            return true;
        }   
    }
}
