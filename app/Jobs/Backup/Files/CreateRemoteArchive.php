<?php

namespace App\Jobs\Backup\Files;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SSHSiteConnect;
use Exception;

use App\Models\Snapshot;

class CreateRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $site;
    protected $archiveFile;
    protected $snapshotId;

    /**
     * CreateRemoteArchive constructor.
     *
     * @param $site
     * @param int $snapshotId
     */
    public function __construct($site, $snapshotId)
    {
        $this->site = $site;
        $this->archiveFile = 'files-backup-' . $site->id . '-' . time() . '.tar.gz';
        $this->snapshotId = $snapshotId;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle()
    {
        // Establish SSH connection using SSHSiteConnect service
        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Step 1: Get the estimated size of the backup directory
        $directorySize = $connection->exec("du -sb {$this->site->dir_path} | awk '{print $1}'");
        $estimatedBackupSize = (int)trim($directorySize);

        // Step 2: Check available disk space on the remote server
        $availableSpace = $connection->exec("df -P /tmp | awk 'NR==2 {print $4}'");
        $availableSpaceBytes = trim($availableSpace) * 1024; // Convert KB to Bytes

        // Step 3: Compare the available disk space with the estimated backup size
        if ($availableSpaceBytes < $estimatedBackupSize) {
            throw new Exception('Insufficient disk space available on remote server to create the archive');
        }

        // Step 4: Create the archive, excluding the archive itself if it's in the same directory
        $tarCommand = "mkdir -p {$this->site->dir_path}/tmp && cd {$this->site->dir_path} && tar --exclude='tmp/*' -czf tmp/{$this->archiveFile} .";
        $connection->exec($tarCommand);

        // Make chmod 600 /path/to/file
        $connection->exec("chmod 600 {$this->site->dir_path}/tmp/{$this->archiveFile}");

        // Step 5: Verify archive creation success
        $checkFile = $connection->exec("if [ -f {$this->site->dir_path}/tmp/{$this->archiveFile} ]; then echo 'exists'; else echo 'not_found'; fi");
        if (trim($checkFile) !== 'exists') {
            throw new Exception('Failed to create archive on the remote server');
        }

        // Update the snapshot with the archive path
        $snapshot = Snapshot::find($this->snapshotId);
        $snapshot->local_path = $this->archiveFile;
        $snapshot->status = 'archived';
        $snapshot->save();

        // Close the SSH connection
        $connection->close();
    }
}
