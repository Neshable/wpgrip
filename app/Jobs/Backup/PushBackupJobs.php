<?php

namespace App\Jobs\Backup;

use App\Models\Site;
use App\Models\Backup;
use App\Services\SSHService;

use Illuminate\Support\Facades\Storage;

use Filament\Notifications\Notification;
use Carbon\Carbon;
use App\Jobs\RemoteDBBackup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushBackupJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sites = Site::where('is_staging', false)->get();

        foreach ($sites as $site) 
        {
            if ( $site->backup_enabled )
            {
                // $site->files_schedule
                // $site->db_schedule

                // Get the latest site backup.
                $latest_backup = Backup::where('site_id', $site->id)
                    ->where('frequency','!=','manual')
                    ->orderBy('created_at', 'DESC')->first();

                // If there is a backup, check if it's manual.
                if ( $latest_backup )
                {
                    if ( $latest_backup->frequency )
                    {
                        if ( $this->compare_dates_schedule( $latest_backup->created_at, $latest_backup->frequency ) )
                        {
                            // Dispatch to background processing.
                            // @todo we need user owner.
                            RemoteDBBackup::dispatch( $site, $latest_backup->frequency );
                        }  
                    }   
                } 
                else
                // This case fires if we don't have any backup scheduled.
                {
                    // @todo use enums.
                    if ( $site->db_schedule )
                    {
                        // We dispatch to background processing anyways.
                        RemoteDBBackup::dispatch( $site, $site->db_schedule );
                    }
                    
                }
                
    
            }
            
        }
    
        // We need a schedule class that executes each hour maybe and goes through all sites
        // Check if backup is enabled
        // ( Checks their db_schedule and files_schedule - this should be checked from the backup itself ).
        // whehn creating a backup, put another table field for next_backup with datetime ( also delete_date )
        // when the schedule class runs, it checks if current_time >= next_backup_time
        // if true, puts a job in the queue
    }

    /**
     * Compares the datetimes and returns true if it's time for a new backup
     *
     * @return void
     */
    public function compare_dates_schedule( string $backup_creation_date, string $schedule = 'weekly' ) : bool
    {
        // Get the datetime now.
        $current_date_time = Carbon::now();
        // Init a carbon object out of created date.
        $carbon_created_at = Carbon::parse( $backup_creation_date );
        // Add hours or days based on the schedule.
        switch ( $schedule )
        {
            case 'daily':
                $compare_date = $carbon_created_at->addHours(24);
                break;
            case 'biweekly':
                $compare_date = $carbon_created_at->addHours(84);
                break;
            case 'weekly':
                $compare_date = $carbon_created_at->addHours(168);
                break;
            case 'monthly':
                $compare_date = $carbon_created_at->addHours(720);
                break;
            case 'manual':
                return false;
                break;
            default: 
                $compare_date = $carbon_created_at->addHours(720);
                break;
        }

        // Check if it's time for the new backup.
        if ( $current_date_time->gte($compare_date) )
        {
            return true;
        }

        return false;
        
    }

         


}
