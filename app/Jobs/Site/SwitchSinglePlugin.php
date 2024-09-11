<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Plugin;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;
use App\Services\WPCliService;
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SwitchSinglePlugin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The slug of the plugin
     *
     * @var \App\Models\Plugin
     */
    public $plugin;

    /**
     * If set to true then we will deactivate the plugin
     *
     * @var bool
     */
    public $deactivate;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, Plugin $plugin, bool $deactivate = false  )
    {
        // Get original production site.
        $this->site = $site;
        $this->plugin = $plugin;
        $this->deactivate = $deactivate;
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
            
            if ( $this->deactivate )
            {
                $command = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::deactivatePlugin( $this->plugin->name );
            } else {
                $command = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::activatePlugin( $this->plugin->name );
            }
            
      
            // Let's login and run the WP Cli command.
            $output = $connection->exec( $command );
           
            $success = $connection->getExitStatusBool();
            $connection->close();  

            if ( $success )
            {
                $this->update_db();
                GripNotifications::pluginUpdatedSuccess();
                return true;
            }
   
            GripNotifications::pluginUpdatedFailed();
            return false;
        }   
    }

    public function update_db()
    { 
        $status = 'active';
        if ( $this->deactivate )
        {
            $status = 'inactive';
        }

         // If our record exist in the pivot table then update.
         if( $this->site->plugins()->find( $this->plugin->id ) )
         {
            $this->site->plugins()->updateExistingPivot( $this->plugin->id, array(
                         'status' => $status
            ) );
         }
    }
}
