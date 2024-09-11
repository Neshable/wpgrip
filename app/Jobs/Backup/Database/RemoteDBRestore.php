<?php

namespace App\Jobs\Backup\Database;

use App\Models\Site;
use App\Models\Backup;
use App\Services\SSHSiteConnect;

use Illuminate\Support\Facades\Storage;

use Filament\Notifications\Notification;
use Carbon\Carbon;
use App\Services\GripNotifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Throwable;
use Exception;

class RemoteDBRestore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The site model
     *
     * @var Site|int
     */
    public $site;

    /**
     * The parent site model, if we have staging
     *
     * @var Site|int|void
     */
    public $parent_site;

    public $timestamp;

    /**
     * The backup model or id
     *
     * @var Backup|int
     */
    public $backup;

    /**
     * SSH connection
     *
     * @var SSHSiteConnect
     */
    private $ssh_connection;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site|int $site, Backup|int $backup, $timestamp = null )
    {
        $this->site = $site;
        if ($site instanceof Site) {
            // If $site is a Site instance, use it directly
            $this->site = $site;
        } else {
            // Otherwise, treat $site as an ID and fetch the Site instance
            $this->site = Site::findOrFail($site);
        }

        if ($backup instanceof Backup) {
            // If $backup is a Backup instance, use it directly
            $this->backup = $backup;
        } else {
            // Otherwise, treat $backup as an ID and fetch the Backup instance
            $this->backup = Backup::findOrFail($backup);
        }

        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site->dir_path ) {

            // Check if we are syncing a staging or restoring live website
            if ( $this->site->is_staging )
            {
                // In this case we need to find the parent of this staging
                $this->parent_site = $this->site->parent()->first();
            }

            // Set a SSH connection to the server.
            $this->setupConnection();


            // If we don't have connection abort and send notification.
            if ( !$this->ssh_connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();
                return false;
            }

            // Step1 Generate the temp URL from S3
            // Step2 SSH to staging and download the file to root
            // Step3 Run the SyncFinalTweaks to import database and replace domains
            // Step4 Delete the downloaded file?
            $this->downloadRemoteBackup();
            $this->updateTablePrefix();
            $this->importDatabase();
            $this->replaceDomains();
            $this->installAndActivatePlugin();
            $this->cleanup();

            // Update last synced here
            if ( $this->parent_site ) 
            {
                $this->site->last_sync = Carbon::now();
                $this->site->save();
            }

            $this->ssh_connection->close();
            
            // Send database notification.
            GripNotifications::getStagingSyncComplete( $this->site->user_id );
            
            if ( true )
            {
                 
            }
            else 
            {
                \Sentry\captureMessage('Something went wrong during the db restore');
            }
        }
       
    }

    /**
     * Init a new SSH connection
     *
     * @return void
     */
    private function setupConnection()
    {
        $this->ssh_connection = new SSHSiteConnect( $this->site );
    }

    public function generateTempURL()
    {
        // Generate a temporary URL for a file on S3 that expires in 60 minutes
        return Storage::disk('s3')->temporaryUrl(
            $this->backup->file_path, now()->addMinutes(25)
        );

    }

    private function downloadRemoteBackup()
    {

        $downloadCommand = 'wget -O ' . $this->site->dir_path . '/latest_db.sql.gz "' . $this->generateTempURL() . '"';
        $this->ssh_connection->exec($downloadCommand);

        // Check if the command was executed successfully
        if (!$this->ssh_connection->getExitStatusBool()) {
            throw new Exception("Failed to download the backup.");
        }   
    }

    private function updateTablePrefix()
    {
        // Update the prefix if we are using the staging
        if ( $this->parent_site ) 
        {
            // The command to update the table prefix in the wp-config.php file
            $newPrefix = $this->parent_site->db_prefix; 

            $updatePrefixCommand = "cd " . $this->site->dir_path . " && wp config set table_prefix '" . $newPrefix . "'";
            $output = $this->ssh_connection->exec($updatePrefixCommand);
            
            // // Check if the command was executed successfully
            if (!$this->ssh_connection->getExitStatusBool()) {
                throw new Exception("Failed to update the wp-config.php file with the new table prefix.");
            }   

            return true;
        }

        return true;
       
    }

    private function importDatabase()
    {
        $importCommand = 'cd ' . $this->site->dir_path . ' && gzip -c -d latest_db.sql.gz | wp db import - && rm -rf latest_db.sql.gz';
        $test = $this->ssh_connection->exec($importCommand);

        // Check if the command was executed successfully
        if (!$this->ssh_connection->getExitStatusBool()) {
            throw new Exception("Failed to import the database.");
        }   

        return true;
        
    }

    private function replaceDomains()
    {
        // Update the domain if we are using the staging
        if ( $this->parent_site ) 
        {
            // Replace PRODUCTION domain with STAGING
            $updateDomains = 'cd ' . $this->site->dir_path . ' && wp search-replace \'' . $this->getCleanDomainNameFromUrl( $this->parent_site->url ) . '\' \'' . $this->getCleanDomainNameFromUrl( $this->site->url ) .
            '\' --skip-columns=guid';
            $this->ssh_connection->exec($updateDomains);
    
            
            // Check if the command was executed successfully
            if (!$this->ssh_connection->getExitStatusBool()) {
                throw new Exception("Failed to replace domains.");
            }   

            return true;
        }

        return true;
    }
    /**
     * Little helper function to get only the domain name.
     *
     * @param [type] $url
     * @return void
     */
    private function getCleanDomainNameFromUrl($url)
    {
        // Parse the URL and return the host (domain)
        $host = parse_url($url, PHP_URL_HOST);
    
        // Remove 'www.' if it exists
        // $host = preg_replace('/^www\./', '', $host);
    
        // Return the host without any trailing slashes
        return rtrim($host, '/');
    }

    private function installAndActivatePlugin()
    {
        // Update the domain if we are using the staging
        if ( $this->parent_site ) 
        {

            $pluginCommand = 'cd ' . $this->site->dir_path . ' && wp plugin install disable-emails --activate';
            $this->ssh_connection->exec($pluginCommand);
    

            // Check if the command was executed successfully
            if (!$this->ssh_connection->getExitStatusBool()) {
                throw new Exception("Failed to install some plugins.");
            }   

            return true;
        }

        return true;
    }

    private function cleanup()
    {
        $cleanupCommand = 'cd ' . $this->site->dir_path . ' && rm -rf latest_db.sql.gz';
        $this->ssh_connection->exec($cleanupCommand);
    
    }
   
}
