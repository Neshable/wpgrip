<?php

namespace App\Jobs\Staging;

use App\Models\Site;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

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

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Exception;

class SyncFinalTweaks implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Site model
     *
     * @var Site
     */
    public $site;

    /**
     * Site model
     *
     * @var Site
     */
    private $staging_site;

    /**
     * SSH connection
     *
     * @var SSHSiteConnect
     */
    private $ssh_connection;

    public function __construct(Site $site)
    {    
        $this->site = $site;
        $this->staging_site = $this->site->children()->first(); 
    }

    public function handle()
    {
        // if ($this->batch()->cancelled()) 
        // {
        //     // Determine if the batch has been cancelled...
        //     return;
        // }
        
        if (!$this->site || !$this->staging_site) 
        {
            return false;
        }

        // Set a SSH connection to the server.
        $this->setupConnection();

        if ( $this->ssh_connection->active ) 
        {
            $this->updateTablePrefix();
            $this->importDatabase();
            $this->replaceDomains();
            $this->installAndActivatePlugin();
            $this->cleanup();

            // Update last synced here
            $this->staging_site->last_sync = Carbon::now();
            $this->staging_site->save();
            // Send database notification.
            GripNotifications::getStagingSyncComplete( $this->site->user_id );
            
            //GripNotifications::getGitPulledSuccess();

            // try {
                
            // } catch (Throwable $e) {
            //     // Handle exception, perhaps log it or send a notification
            //     GripNotifications::getGitPulledFailed();
            //     return false;
            // }
        }

        return true;
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

    private function updateTablePrefix()
    {
        // The command to update the table prefix in the wp-config.php file
        $newPrefix = $this->site->db_prefix; 

        $updatePrefixCommand = 'ssh -t ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip .
            ' "cd ' . $this->staging_site->dir_path .
            ' && wp config set table_prefix \'' . $newPrefix . '\'"';

        // Execute the command
        $what = $this->ssh_connection->exec( $updatePrefixCommand );

        // Check if the command was executed successfully
        if (!$this->ssh_connection->getExitStatusBool()) {
            throw new Exception("Failed to update the wp-config.php file with the new table prefix.");
        }   

        return true;
    }

    private function importDatabase()
    {
        $importCommand = 'ssh -t ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip .
                         ' "cd ' . $this->staging_site->dir_path .
                         ' && gzip -c -d latest.sql.gz | wp db import - && rm -rf latest.sql.gz"';
        $this->ssh_connection->exec($importCommand);

        // Check if the command was executed successfully
        if (!$this->ssh_connection->getExitStatusBool()) {
            throw new Exception("Failed to import the database.");
        }   
        
    }

    private function replaceDomains()
    {
        $replaceCommand = 'ssh -t ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip .
                          ' "cd ' . $this->staging_site->dir_path .
                          ' && wp search-replace \'' . $this->getCleanDomainNameFromUrl( $this->site->url ) . '\' \'' . $this->getCleanDomainNameFromUrl( $this->staging_site->url ) .
                          '\' --skip-columns=guid"';


        $replace_domains = $this->ssh_connection->exec($replaceCommand);

        // Check if the command was executed successfully
        if (!$this->ssh_connection->getExitStatusBool()) {
            throw new Exception("Failed to replace domains.");
        }  
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
        $pluginCommand = 'ssh -t ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip .
                         ' "cd ' . $this->staging_site->dir_path .
                         ' && wp plugin install disable-emails --activate"';
        $this->ssh_connection->exec($pluginCommand);
    }

    private function cleanup()
    {
        // Assuming that the SQL file is in the root directory of the site
        $cleanupCommand = 'ssh -t ' . $this->staging_site->ssh_user . '@' . $this->staging_site->server->ip .
                          ' "cd ' . $this->staging_site->dir_path . ' && rm -f latest.sql.gz"';

        $this->ssh_connection->exec($cleanupCommand);

        
    }
}
