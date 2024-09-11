<?php

namespace App\Jobs\Server;

use App\Models\Site;
use App\Models\PHPLog;

use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use App\Services\SlackNotifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TailErrorLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    public $type;

    /**
     * Count how many errors we found.
     *
     * @var int
     */
    public $count = 0;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $type = 'parse' )
    {
        // Get original production site.
        $this->site = $site;
        $this->type = $type;
    }

    public function getType()
    {
        switch ( $this->type )
        {
            case 'parse':
                return 'PHP Parse error';
                break;
            case 'fatal':
                return 'PHP Fatal error';
                break;
        }
    }
  
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site ) {

            if ( !$this->site->error_log_path )
            {
                // No error path specified
                return false;
            }

            // Init a new connection to websites's production server.
            $connection = new SSHSiteConnect( $this->site );

            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();
                return false;
            }

            $command = "grep -E '" . $this->getType() . "' " . escapeshellarg( $this->site->error_log_path ) . " | tail -n 50";
      
            // Let's login and run the WP Cli command.
            $output = $connection->exec( $command );
           
            $success = $connection->getExitStatusBool();
            $connection->close();  

            if ( $success )
            {
                $outputArray = explode("\n", $output);

                if ( $outputArray && !empty($outputArray) )
                {
                    $this->create_errors_records( $outputArray );
                    GripNotifications::pluginUpdatedSuccess();
                    // Check if we found any new errors and trigger notifications.
                    if ( $this->count > 1 )
                    {
                        // Optional - add database notification!
                        SlackNotifications::sendPHPErrorsFound( $this->site, $this->count, $this->type );
                    }

                    return true;
                }  
               
            }
   
            GripNotifications::pluginUpdatedFailed();
            return false;
        }   
    }

    /**
     * Save the log in the database
     *
     * @param array|null $errors
     * @return void
     */
    public function create_errors_records( ?array $errors )
    { 
        $this->count = 0;

        foreach($errors as $error) 
        {
            // Extract date and time from error message
            preg_match('/(\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2})/', $error, $matches);
            if (empty($matches)) {
                continue; // if date time string is not found in error message, skip this iteration
            }
            $dateTimeStr = $matches[1];
            $dateTime = Carbon::createFromFormat('Y/m/d H:i:s', $dateTimeStr);
        
            // Create a new log if doesn't exist
            $log = PHPLog::firstOrNew(['created_at' => $dateTime]);

            if(!$log->exists)
            {
                $log->site_id = $this->site->id;
                $log->type = $this->type;
                $log->message = $error;
                $log->created_at = $dateTime;

                // Get the number of existing records for this site
                $recordCount = PHPLog::where('site_id', $this->site->id)->count();

                // If the count exceeds the limit, delete the oldest records
                $limit = 100;
                
                if($recordCount >= $limit) 
                {
                    // Define the number of records we need to delete
                    $deleteCount = $recordCount - $limit + 1; // We add 1 because we're about to add a new record

                    // Delete the old records
                    PHPLog::where('site_id', $this->site->id)
                        ->orderBy('created_at', 'asc')
                        ->limit($deleteCount)
                        ->delete();
                }

                // Save the new record
                $log->save(['timestamps' => false]);

                // Increase the count of errors found!
                $this->count++;
            }
        }
        
    }
}
