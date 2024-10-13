<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use Exception;

class CreateRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $sitePath;
    protected $archiveFile;
    protected $snapshotId;

    /**
     * CreateRemoteArchive constructor.
     *
     * @param string $remoteServer
     * @param string $remoteUser
     * @param string $remotePassword
     * @param string $sitePath
     * @param int $snapshotId
     */
    public function __construct($remoteServer, $remoteUser, $remotePassword, $sitePath, $snapshotId)
    {
        $this->remoteServer = $remoteServer;
        $this->remoteUser = $remoteUser;
        $this->remotePassword = $remotePassword;
        $this->sitePath = $sitePath;
        $this->archiveFile = '/tmp/files-backup-' . time() . '.tar.gz';
        $this->snapshotId = $snapshotId;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle()
    {
        // Establish SSH connection
        $ssh = new SSH2($this->remoteServer);
        if (!$ssh->login($this->remoteUser, $this->remotePassword)) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Step 1: Get the estimated size of the backup directory
        $directorySize = $ssh->exec("du -sb {$this->sitePath} | awk '{print $1}'");
        $estimatedBackupSize = (int)trim($directorySize);

        // Step 2: Check available disk space on the remote server
        $availableSpace = $ssh->exec("df -P /tmp | awk 'NR==2 {print $4}'");
        $availableSpaceBytes = trim($availableSpace) * 1024; // Convert KB to Bytes

        // Step 3: Compare the available disk space with the estimated backup size
        if ($availableSpaceBytes < $estimatedBackupSize) {
            throw new Exception('Insufficient disk space available on remote server to create the archive');
        }

        // Step 4: Create the archive
        $tarCommand = "tar -czf {$this->archiveFile} -C {$this->sitePath} .";
        $ssh->exec($tarCommand);

        // Step 5: Verify archive creation success
        $checkFile = $ssh->exec("if [ -f {$this->archiveFile} ]; then echo 'exists'; else echo 'not_found'; fi");
        if (trim($checkFile) !== 'exists') {
            throw new Exception('Failed to create archive on the remote server');
        }

        // Step 6: Chain the next job to upload the archive to S3
        $this->chain(new UploadRemoteBackupToS3($this->remoteServer, $this->remoteUser, $this->remotePassword, $this->archiveFile, $this->snapshotId))->dispatch();
    }
}
