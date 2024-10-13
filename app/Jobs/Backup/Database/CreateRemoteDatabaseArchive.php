<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use Exception;

class CreateRemoteDatabaseArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $sitePath;
    protected $archiveFile;
    protected $snapshotId;

    /**
     * CreateRemoteDatabaseArchive constructor.
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
        $this->sitePath = $sitePath; // The directory where wp-cli is located
        $this->archiveFile = '/tmp/db-backup-' . time() . '.sql.gz'; // Compressed output file
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
        // For encryption use
        // $command = "cd {$this->sitePath} && wp db export - | gzip -9 | openssl enc -aes-256-cbc -salt -out {$this->archiveFile}";

        // Create a compressed database backup using wp-cli and gzip
        $command = "cd {$this->sitePath} && wp db export - | gzip -9 > {$this->archiveFile}";
        $ssh->exec($command);

        // Verify that the archive was created successfully
        $checkFile = $ssh->exec("if [ -f {$this->archiveFile} ]; then echo 'exists'; else echo 'not_found'; fi");
        if (trim($checkFile) !== 'exists') {
            throw new Exception('Failed to create compressed database archive on the remote server');
        }

        // Chain the next job to upload the archive to S3
        $this->chain(new UploadRemoteBackupToS3($this->remoteServer, $this->remoteUser, $this->remotePassword, $this->archiveFile, $this->snapshotId))->dispatch();
    }
}
