<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Plugin;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;
use App\Services\ActivityLogger;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSinglePlugin implements ShouldQueue
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
     * The string version of the plugin ( float not possible due to microversioning )
     *
     * @var string
     */
    public $version;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, Plugin $plugin, string $version )
    {
        // Get original production site.
        $this->site = $site;
        $this->plugin = $plugin;
        $this->version = $version;
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
            $command = 'cd ' . $this->site->dir_path . ' && wp plugin update ' . $this->plugin->name . ' --version='. $this->version;
      
            // Let's login and run the WP Cli command.
            $output = $connection->exec( $command );
           
            $success = $connection->getExitStatusBool();
            $connection->close();  

            if ( $success )
            {
                $this->update_db();
                GripNotifications::pluginUpdatedSuccess();
                ActivityLogger::pluginAction('plugin.updated', $this->site, $this->plugin->title ?? $this->plugin->name, [
                    'version' => $this->version,
                    'plugin'  => $this->plugin->name,
                ]);
                return true;
            }

            ActivityLogger::pluginAction('plugin.update_failed', $this->site, $this->plugin->title ?? $this->plugin->name, [
                'version' => $this->version,
            ], 'failed');
            GripNotifications::pluginUpdatedFailed();
            return false;
        }   
    }

    public function update_db()
    { 
         // If our record exist in the pivot table then update.
         if( $this->site->plugins()->find( $this->plugin->id ) )
         {
             $this->site->plugins()->updateExistingPivot( $this->plugin->id, array(
                         'version' => $this->version,
                         'is_vulnerable' => false,
                         'update_version' => null
                 ) );
         }
    }
}
