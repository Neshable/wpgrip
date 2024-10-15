<?php

namespace App\Jobs\Backup\Files;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SFTP;
use  Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Exception;

use Carbon\Carbon;
use App\Models\Snapshot;

use App\Services\SFTPSiteConnect;

class UploadRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $snapshot_id;

    protected $archiveFile;

    private $site;

    /**
     * Create a new job instance.
     *
     * @param int $snapshot_id
     */
    public function __construct($site, $snapshot_id)
    {
        $this->site = $site;
        $this->snapshot_id = $snapshot_id;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle()
    {
        // Fetch the snapshot and retrieve the archive path
        $snapshot = Snapshot::find($this->snapshot_id);
        if (!$snapshot || !$snapshot->local_path) {
            throw new Exception('No archive path found in snapshot record');
        }

        // Establish SFTP connection
        $sftp_service = new SFTPSiteConnect( $this->site );
        if (!$sftp_service->active) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Define the local Laravel tmp directory to store the backup
        $localTmpPath = storage_path('tmp'); // This will create or use storage/tmp folder
        if (!file_exists($localTmpPath)) {
            mkdir($localTmpPath, 0755, true); // Create the directory if it does not exist
        }

        // Create a local temporary file in the Laravel tmp directory
        $localTempFile = $localTmpPath . '/' . basename($snapshot->local_path);


        // Download the remote archive file to the local temporary file
        if (!$sftp_service->sftp->get( $this->site->dir_path . '/tmp/' . $snapshot->local_path, $localTempFile)) 
        {
            throw new Exception('Unable to download the archive file from the remote server');
        }

        // Upload the local file to S3
        $s3Path = 'backups/site-' . $this->site->id;

        // $fileStream = fopen($localTempFile, 'r');
        
        // Upload to s3
        $status = Storage::disk('s3')->putFileAs( $s3Path, new File( $localTempFile ), basename( $snapshot->local_path ) );
        // $status = Storage::disk('s3')->put( $s3Path, $fileStream );

        if ( $status )
        {
            // Save our snapshot model
            $snapshot->remote_path = $s3Path;
            $snapshot->status = 'cleaning';
            $snapshot->size = Storage::disk('s3')->size( $s3Path . '/' . basename($snapshot->local_path) );
            // @todo get the schedule.
            $snapshot->deletion_date = Carbon::now()->addMonths(5);
            $snapshot->save();

        }

        // Close the file stream and delete the local temporary file
        // fclose($fileStream);
        unlink($localTempFile);
    }
}
