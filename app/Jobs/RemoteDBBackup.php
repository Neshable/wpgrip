<?php

namespace App\Jobs;

use App\Models\Site;
use App\Models\User;
use App\Models\Backup;
use App\Events\BackupSuccessful;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use Filament\Notifications\Notification;
use App\Services\BackupLocation;

use App\Services\SSHSiteConnect;
use App\Services\SSHService;

use App\Services\GripNotifications;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoteDBBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

     /**
     * The server instance.
     *
     * @var \App\Models\server
     */
    public $server;

     /**
     * The User instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The team ID
     *
     * @var int
     */
    public $team_id;

    /**
     * The location object
     *
     * @var BackupLocation
     */
    public $path_locations;

    /**
     * The frequency of the backup
     *
     * @param string $frequency can be daily, weekly, biweekly, monthly
     */
    public $frequency;

    /**
     * The ID of the backup model that we created previously
     *
     * @var int
     */
    public $backup_id;

    /**
     * List of excluded tables
     *
     * @var array
     */
    public $excluded_tables;

    /**
     * The carbon timestamp
     *
     * @var Carbon
     */
    public $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $frequency = 'weekly', $backup_id = null, $timestamp = null )
    {
        $this->site = $site;
        $this->frequency = $frequency;
        $this->server = $this->site->server;
        $this->team_id = $this->site->team->id;
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
        // If that's set, the job will write within this model.
        $this->backup_id = $backup_id;
        $this->excluded_tables = '';
        // Check site owner.
        $this->user = User::find( $this->site->user_id );
        // Generate path object.
        $this->path_locations = new BackupLocation( $site, $this->timestamp );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site->dir_path ) {
            
            // Init a new connection to websites's production server.
            $connection = new SSHSiteConnect( $this->site );

            // If we don't have connection abort and send notification.
            if ( !$connection->active ) 
            {
                GripNotifications::getUnauthorizedNotificaiton();
                return false;
            }

            // Check if we have excluded some tables.
            $excluded_tables = $this->site->excluded_tables;
            if ( $excluded_tables ) {
                $this->excluded_tables = implode(',', $excluded_tables);
            }

            $command = 'cd ' . $this->site->dir_path . ' && wp db export --exclude_tables=' . $this->excluded_tables . ' - | gzip -9';
      
            // Let's login and run the WP Cli command.
            $output = $connection->exec( $command );
         
            $success = $connection->getExitStatusBool();
            $connection->close();  

            // Set target path
            $path = $this->path_locations->getS3DatabaseBackupPath() . '/' . $this->path_locations->db_name;

            // Upload it to s3
            $status = Storage::disk('s3')->put( $path, $output );
            
            if ( $status )
            {
                // Calculate size
                $size = Storage::disk('s3')->size( $path );

                if ( $this->backup_id )
                {   
                    // Find the model
                    $backup = Backup::find($this->backup_id);
                    
                    if ( $backup && $backup->site_id == $this->site->id ) 
                    {
                        // Just to be sure we are talking about the same site.
                        $backup->update([
                            'file_path' => $path,
                            'frequency' => $this->frequency,
                            'db_size' => $size,
                            'status' => 'active',
                            'delete_date' => Carbon::now()->addDays(60)
                        ]);  
                    }
                }
                else {
                    $backup = Backup::create([
                        'site_id' => $this->site->id,
                        'team_id' => $this->team_id,
                        'provider' => 's3',
                        'file_path' => $path,
                        'type' => 'db',
                        'frequency' => $this->frequency,
                        'db_size' => $size, // in bytes
                        'status' => 'active',
                        'delete_date' => Carbon::now()
                    ]);
                }

                

                 // Send notification to the user.
                if ( $backup->exists ) 
                {   
                    // Dispatch event.    
                    // BackupSuccessful::dispatch( $backup );
                    event(new BackupSuccessful( $backup ));

                    // dispatch user notification.
                    $this->user->notify(
                        Notification::make()
                            ->title('Database backup complete.')
                            ->success()
                            ->body( 'Database backup for ' . $this->site->name . ' is complete.' ) 
                            ->toDatabase(),
                    );
                } 
                
            }
            else 
            {
                \Sentry\captureMessage('Something went wrong during the manual db backup');
            }

           

        }
        
    }



}
