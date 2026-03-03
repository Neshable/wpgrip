<?php

namespace App\Jobs\Updates;

use App\Models\Site;
use App\Models\Plugin;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;
use App\Services\ActivityLogger;

use App\Enums\SiteStatus;
use Carbon\Carbon;
use App\Events\WPCoreUpdated;
use App\Services\WPCliService;
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWPCore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;


    /**
     * The string version of the plugin ( float not possible due to microversioning )
     *
     * @var string
     */
    public $version;

    /**
     * Force lower version number
     *
     * @var bool
     */
    public $force;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $version, bool $force = false )
    {
        // Get original production site.
        $this->site = $site;
        $this->version = $version;
        $this->force = $force;
    }
  
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        WPCoreUpdated::dispatch( $this->site );
        
        if ( $this->site ) {

            // Init a new connection to websites's production server.
            $connection = new SSHSiteConnect( $this->site );

            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();
                $this->resetUpdateStatus();
                return false;
            }

            $command = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::updateCoreVersion( false, $this->version, $this->force );
      
            // Let's login and run the WP Cli command.
            $output = $connection->exec( $command );
           
            $success = $connection->getExitStatusBool();
            $connection->close();  

            if ( $success )
            {
                $this->updateCoreVersion();
                GripNotifications::pluginUpdatedSuccess();
                WPCoreUpdated::dispatch( $this->site );
                ActivityLogger::siteAction('core.updated', $this->site, ['version' => $this->version]);
                return true;
            }

            $this->resetUpdateStatus();
            GripNotifications::pluginUpdatedFailed();
            ActivityLogger::siteAction('core.update_failed', $this->site, ['version' => $this->version], 'failed');
            return false;
        }
        
        $this->resetUpdateStatus();
    }

    public function updateCoreVersion()
    { 
         // If our record exist in the pivot table then update.
         $this->site->wp_ver = $this->version;
         $this->site->status = SiteStatus::Normal;
         $this->site->save();
    }

    public function resetUpdateStatus()
    {
        $this->site->status = SiteStatus::Normal;
        $this->site->save();
    }
}
