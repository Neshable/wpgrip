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
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncDatabase implements ShouldQueue
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

            // Check if we have connection between Live and Staging
            // @todo extract to different job to be first in the batch and check for connecitons
            $output = $connection->exec(  "ssh -o BatchMode=yes -o ConnectTimeout=10 {$this->staging_site->ssh_user}@{$this->staging_site->server->ip} 'echo 1'" );
        
            if ( trim($output) !== "1" ) 
            {
                // GripNotifications::getLiveToStagingError();
                return false;
            }

            // Create hidden .folder and export database.
            $connection->exec('cd ' . $this->site->dir_path . ' && mkdir .wpgrip' );
            $connection->exec('cd ' . $this->site->dir_path . ' && wp db export --exclude_tables=wp_icl_string_pages --all-tablespaces --single-transaction --quick --lock-tables=false - | gzip -9 - > ./.wpgrip/latest.sql.gz' );
            
            // Move database to staging root.
            $connection->exec('rsync -vzrS  ' . $this->site->dir_path . '/.wpgrip/latest.sql.gz' . ' ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip . ':' . $this->staging_site->dir_path );
            
            // Delete backup from prod server.
            $connection->exec('cd ' . $this->site->dir_path . ' && rm -rf .wpgrip' );
           
            $connection->close();

            return true;
        }   
    }

}
