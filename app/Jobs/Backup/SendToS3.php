<?php

namespace App\Jobs\Backup;

use App\Models\Site;
use App\Models\Backup;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use App\Services\SSHSiteConnect;
use App\Services\SSHService;

use Illuminate\Support\Facades\Process;

use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Storage;
use App\Services\BackupLocation;
use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

class SendToS3 implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

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
    public function __construct( Site $site, $timestamp = null )
    {
        $this->site = $site;
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate path object.
        $path_locations = new BackupLocation( $this->site, $this->timestamp );
     

        if ( Storage::disk('temp')->exists( $path_locations->getRelativeLocalBackupFile() ) ) 
        {
            // Open stream to file.
           
            $stream = Storage::disk('temp')->readStream( $path_locations->getRelativeLocalBackupFile() );

            $status = Storage::disk('s3')->put(
                $path_locations->getS3FilesBackupPath() . '/' . $path_locations->file_name,
                $stream
            );

            // Make sure to close the stream if it's no longer needed
            if ( is_resource($stream) ) 
            {
                fclose($stream);
            }

            if ( $status )
            {
                // Calculate size
                $size = Storage::disk('s3')->size( $path_locations->getS3FilesBackupPath() . '/' . $path_locations->file_name );
                // @todo create model in the first part of the job batches.
                $backup = Backup::create([
                    'site_id' => $this->site->id,
                    'team_id' => $this->site->team->id,
                    'provider' => 's3',
                    'file_path' => $path_locations->getS3FilesBackupPath() . '/' . $path_locations->file_name,
                    'type' => 'files',
                    'frequency' => 'manual',
                    'db_size' => $size, // in bytes
                    'status' => 'active',
                    'delete_date' => Carbon::now()->addMonths(5)
                ]);

                if ($backup) {
                    return true;
                }
            }
        }
        
    }
  

}
