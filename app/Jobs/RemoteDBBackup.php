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
use App\Services\ActivityLogger;

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
    public $tenant_id;

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

    private $encryption_key;

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
        $this->tenant_id = $this->site->tenant->id;
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
        // If that's set, the job will write within this model.
        $this->backup_id = $backup_id;
        $this->excluded_tables = '';
        // Check site owner.
        $this->user = User::find( $this->site->user_id );
        // Generate path object.
        $this->path_locations = new BackupLocation( $site, $this->timestamp );
        // Encryption key would be the tenant uuid
        $this->encryption_key =  $this->site->tenant->uuid;
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
            // Encrypt the output
            $encrypted_output = $this->encryptData($output);
            // Upload it to s3
            $status = Storage::disk('s3')->put( $path, $encrypted_output );
            
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
                        'tenant_id' => $this->tenant_id,
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

                    $this->user->notify(
                        Notification::make()
                            ->title('Database backup complete.')
                            ->success()
                            ->body( 'Database backup for ' . $this->site->name . ' is complete.' )
                            ->toDatabase(),
                    );
                    ActivityLogger::backupAction('backup.created', $this->site, [
                        'type' => 'database',
                        'size' => $backup->size ?? null,
                    ]);
                } 
                
            }
            else 
            {
                \Sentry\captureMessage('Something went wrong during the manual db backup');
            }

           

        }
        
    }

    /**
     * Encrypt the data
     *
     * @param  [type] $data
     * @return void
     */
    private function encryptData($data)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $this->encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt 
     *
     * @param  [type] $encryptedData
     * @param  [type] $key
     * @return void
     */
    public static function decryptData($encryptedData, $key)
    {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }



}
