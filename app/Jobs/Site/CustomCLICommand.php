<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Plugin;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomCLICommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;


    /**
     * The WP Cli command
     *
     * @var string
     */
    public $cmd;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $cmd )
    {
        // Get original production site.
        $this->site = $site;
        $this->cmd = $cmd;
    }
  
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site ) {

            // Init a new connection to websites's production server.
            $connection = new SSHSiteConnect( $this->site );

            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();
                return false;
            }

            $command = 'cd ' . $this->site->dir_path . ' && ' . $this->cmd;
      
            // Let's login and run the WP Cli command.
            $output = $connection->exec( $command );
            $success = $connection->getExitStatusBool();
            $connection->close();  

            if ( $success )
            {
                GripNotifications::commandSuccess();
                return true;
            }
   
            GripNotifications::commandFail();
            return false;
        }   
    }

}
