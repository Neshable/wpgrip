<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Models\Plugin;
use App\Jobs\Site\GenerateSiteMd;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use App\Services\WPCliService;
 
use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSiteStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The connection object
     *
     * @var \App\Services\SSHSiteConnect
     */
    public $connection;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site )
    {
        // Get original production site.
        $this->site = $site;
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
            $this->connection = new SSHSiteConnect( $this->site );

            // If we don't have connection abort and send notification.
            if ( ! $this->connection->active ) 
            {
                $this->site->setConnectionStatus(false);
                GripNotifications::getUnauthorizedNotificaiton();

                return false;
            }

            // Determine what we need to fetch from our connection.
            $this->fetchDirectorySize();
            $this->fetchPHPVersion();
            $this->fetchWordPressCoreVersion();
            $this->fetchDBPrefix();
            $this->fetchCliVersion();
            $this->fetchDBData();

            $this->site->last_sync = Carbon::now();
            $this->site->save();
           
            $this->connection->close();

            // Use this class also as a connection checker.
            if ( !$this->site->getConnectionStatus() )
            {
                $this->site->setConnectionStatus(true);
            }

            GripNotifications::getSiteSyncedNotification();
            GenerateSiteMd::dispatch($this->site);
            return true;
        }   
    }
  

    protected function fetchDirectorySize()
    {
        $command = 'du -s ' . $this->site->dir_path . ' | awk \'{print $1}\'';
        $output = $this->connection->exec($command);

        if ($output) {
            $this->site->dir_size = floor( $output / 1024); // Convert to MB if needed
        }
    }

    protected function fetchPHPVersion()
    {
        $command = 'php -v | sed -e \'/^PHP/!d\' -e \'s/.* \([0-9]\+\.[0-9]\+\.[0-9]\+\).*$/\1/\'';
        $output = $this->connection->exec($command);

        if ($output) {
            $this->site->php_ver = floatval($output);
        }
    }

    protected function fetchWordPressCoreVersion()
    {
        $command = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::getCoreVersion();
        $output = $this->connection->exec($command);

        if ($output) {
            $this->site->wp_ver = floatval($output);
        }
    }

    protected function fetchDBPrefix()
    {
        $command = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::getDBPrefix();
        $output = $this->connection->exec($command);

        if ($output) {
            $this->site->db_prefix = trim( $output );
        }
    }

    protected function fetchCliVersion()
    {
        $command = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::getCliVersion();
        $output = $this->connection->exec($command);

        if ($output) {
            // The string is in the style of WP-CLI 2.7.1
            preg_match("/\d+\.\d+\.\d+/", $output, $matches);

            $versionNumber = $matches[0]; // This will contain '2.7.1'

            $this->site->cli_ver = floatval( $versionNumber );
        }
    }

    

    protected function fetchDBData()
    {
         // Get database size
        $dbSizeCommand = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::getDBSize();
        $dbSizeOutput = $this->connection->exec($dbSizeCommand);
        $this->site->sitemeta->db_size = $dbSizeOutput ? intval($dbSizeOutput) : null;

        // Get all database tables
        $dbTablesCommand = 'cd ' . $this->site->dir_path . ' && ' . WPCliService::getAllDBTables();
        $dbTablesOutput = $this->connection->exec($dbTablesCommand);
        
        if ($dbTablesOutput) {
            preg_match("/\[[^\]]*\]/", $dbTablesOutput, $matches);
            $this->site->sitemeta->db_tables = isset($matches[0]) ? json_decode($matches[0]) : [];
        }

        $this->site->sitemeta->save();
    }
}
