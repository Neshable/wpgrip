<?php

namespace App\Services;

use App\Services\BackupLocation;

use App\Models\Site;
use App\Models\User;
use App\Models\Backup;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Support\Facades\Process;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Filament\Notifications\Notification;
use App\Services\GripNotifications;

class SSHService {

    /**
     * The site object
     *
     * @var Site
     */
    public $site;

    /**
     * If we have the connection established.
     *
     * @var bool
     */
    public $active = false;

    /**
     * Temp file stream
     *
     */
    public $temp_file_stream;

    /**
     * The user private key path
     *
     * @var string
     */
    public $ssh_key_path;

    /**
     * The user object
     *
     * @var User
     */
    public $user;

    /**
     * Backup location service
     *
     * @var BackupLocations
     */
    public $backup_location;

    /**
     * Timestamp for the current job queue
     *
     * @var Carbon
     */
    public $timestamp;

     /**
     * Excluded files and folders
     *
     * @var string
     */
    public $excluded_files;


    /**
     * The
     *
     * @param string $ip
     * @param integer $port
     */
    public function __construct( Site $site, $timestamp = null )
    {
        $this->site = $site;
        $this->excluded_files = $this->site->excluded_files;
        $this->user = User::find( $this->site->user_id );
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
        $this->backup_location = new BackupLocation( $site, $this->timestamp );
    }

    /**
     * Gets the private key - string.
     *
     * @return void
     */
    public function getPrivateKey()
    {
        // Get the key from DB, decrypt and load.
        if ( $this->user->ssh_private )
        {
            try 
            {
                $decrypted_key = Crypt::decryptString( $this->user->ssh_private );
            } catch (DecryptException $e) {
                // throw some error.
            }
            $key = PublicKeyLoader::load( $decrypted_key );
            return $key->toString('OpenSSH');
        }

        return false;
    }

    /**
     * Generate a temp stream file in linux to hold our private key.
     * Should be deleted right after use.
     *
     * @return void
     */
    public function generateKeyFile()
    {
            $key = $this->getPrivateKey();
            if ( $key )
            {
                // Create a random file in temp.
                $this->temp_file_stream = \tmpfile();
                // Write the key to the file.
                fwrite( $this->temp_file_stream, (string) $key );
                // Get file path
                $this->ssh_key_path = stream_get_meta_data($this->temp_file_stream)['uri'];
                // Sets the file position indicator for the file referenced by stream
                fseek( $this->temp_file_stream, 0);
            }
           
    }

    /**
     * Delete our temp stream file for security reasons.
     *
     * @return bool
     */
    public function deleteKeyFile()
    {
        if ( $this->temp_file_stream )
        {
             // This deletes the file.
             return fclose( $this->temp_file_stream );
        }

        return false;
    }

    public function run( $command )
    {
        // Run the following local command.
        $process = Process::timeout(900)->run( $command );

        // $result->successful();
        // $result->failed();
        // $result->exitCode();
        // $result->output();
        // $result->errorOutput();
        // $result->throw();
        // $result->throwIf($condition);

        // $process->output();
        // dd($process->errorOutput());
        
        // executes after the command finishes
        if ( !$process->successful() ) {
            GripNotifications::getProcessFail( $this->site->user_id, 'Local process exited with status code ' . $process->exitCode() );
            return false;        
            // throw new ProcessFailedException($process);
        }

        return true;
        // return $process->output();

        // $result = Process::pipe(function (Pipe $pipe) {
        //     $pipe->command('ls -la');
        //     $pipe->command('grep -i "PHP"');
        //   });
    }

    public function getDirectory()
    {
        $this->generateKeyFile();
        $command = $this->run( 'ssh -i ' . $this->ssh_key_path . ' ' . $this->site->ssh_user . '@' . $this->site->server->ip . ' ls -lah' );
        $this->deleteKeyFile();

        return $command;
    }

    // public function runSSHCommand()
    // {
    //     $this->generateKeyFile();
    //     $command = $this->run( 'ssh -i ' . $this->ssh_key_path . ' ' . $this->site->ssh_user . '@' . $this->site->server->ip . ' ls -lah' );
    //     $this->deleteKeyFile();

    //     return $command;
    // }


    public function rsyncRemoteDirectory()
    {
        // We need to create the directory first otherwise it fails.
        $create_local_dir = $this->run( 'mkdir -p ' .  $this->backup_location->getLocalFilesBackupPath() );
        if ( $create_local_dir )
        {
            $this->generateKeyFile(); 
            // @todo needs fingerprint

            // Check if excluded some folders
            // Split the string by "/"
            $excluded_files = explode( "/", $this->excluded_files );
            if ( $excluded_files && is_array($excluded_files) )
            {
                // Build the --exclude options for rsync
                $excludeOptions = '';
                foreach ($excluded_files as $excludedItem) {
                    $excludeOptions .= ' --exclude="' . escapeshellarg($excludedItem) . '"';
                }
            }

            // -l Argument to preserve the symlinks from the atomic deployments
            $command_string = 'rsync -zrlSP' . $excludeOptions . ' --exclude="node_modules" -e "ssh -o StrictHostKeyChecking=no -i ' . $this->ssh_key_path . '"' . ' ' . $this->site->ssh_user . '@' . $this->site->server->ip . ':' . $this->site->dir_path . ' ' . $this->backup_location->getLocalFilesBackupPath();
            $command = $this->run( $command_string );
            $this->deleteKeyFile();

            return $command;
        }

        return false;
 
    }

    public function makeArchive()
    {
        return $command = $this->run( 'tar czf ' . $this->backup_location->getLocalBackupPath() . '/' . $this->backup_location->file_name . ' ' . $this->backup_location->getLocalFilesBackupPath() . '/*' );
    }

    public function extractArchive()
    {
        // tar czf ostechnix.tar.gz ostechnix/
    }

    public function sendToS3()
    {
        if ( Storage::disk('temp')->exists( $this->backup_location->file_name ) ) 
        {
            $status = Storage::disk('s3')->put(
                $this->backup_location->getS3FilesBackupPath() . '/' . $this->backup_location->file_name,
                Storage::disk('temp')->get( $this->backup_location->file_name )
            );

            if ( $status )
            {
                // Calculate size
                $size = Storage::disk('s3')->size( $this->backup_location->getS3FilesBackupPath() . '/' . $this->backup_location->file_name );
                
                $backup = Backup::create([
                    'site_id' => $this->site->id,
                    'team_id' => $this->site->team->id,
                    'provider' => 's3',
                    'file_path' => $this->backup_location->getS3FilesBackupPath() . '/' . $this->backup_location->file_name,
                    'type' => 'files',
                    'frequency' => 'manual',
                    'db_size' => $size, // in bytes
                    'status' => 'active',
                    'delete_date' => Carbon::now()
                ]);
               
            }
        }
   
    }

    public function deleteFolder()
    {
        return $command = $this->run( 'rm -rf ' . base_path('/temp_files' ) . '/*' );
    }
    
}