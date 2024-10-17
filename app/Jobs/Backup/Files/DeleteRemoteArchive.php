<?php

namespace App\Jobs\Backup\Files;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use phpseclib3\Net\SFTP;
use Illuminate\Support\Facades\Storage;
use Exception;

use Carbon\Carbon;
use App\Models\Snapshot;

// use App\Services\SFTPSiteConnect;
use App\Services\SSHSiteConnect;

class DeleteRemoteArchive implements ShouldQueue
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
    public function __construct( $site, $snapshot_id )
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
        $snapshot = Snapshot::find( $this->snapshot_id );
        if (!$snapshot || !$snapshot->local_path) {
            throw new Exception('No archive path found in snapshot record');
        }

        // Delete local file first
        $localTmpPath = storage_path('tmp'); 
        unlink( $localTmpPath . '/' . basename($snapshot->local_path) );


        // Establish SSH connection using SSHSiteConnect service
        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Delete the remote archive file
        $deleteCommand = "cd {$this->site->dir_path} && rm -rf tmp/{$snapshot->local_path}";
        $connection->exec($deleteCommand);

        // Verify the file was deleted successfully
        $checkFile = $connection->exec("if [ -f {$this->site->dir_path} . '/tmp/' . {$snapshot->local_path} ]; then echo 'exists'; else echo 'not_found'; fi");
        if (trim($checkFile) === 'exists') {
            throw new Exception('Failed to delete the archive file from the remote server');
        }

        // Close the SSH connection
        $connection->close();

        // Save our snapshot model
        $snapshot->status = 'completed';
        $snapshot->save();

         
        // Update the backup model.
        if ( $snapshot->backup )
        {
             // Calculate total size
            $totalSize = $snapshot->backup->snapshots()->sum('size');
            
            $snapshot->backup->last_backup = Carbon::now();
            $snapshot->backup->next_backup = Carbon::now()->addDays( $snapshot->backup->frequency ?? 30 );
            if (  $totalSize )
            {
                $snapshot->backup->size = $totalSize;
            }
            $snapshot->backup->save();
        }
    }
}
