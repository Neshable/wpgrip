<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Net\SFTP;
use Exception;

class DownloadRemoteBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $backupFile;
    protected $snapshotId;

    /**
     * DownloadRemoteBackupFromS3 constructor.
     *
     * @param string $remoteServer
     * @param string $remoteUser
     * @param string $remotePassword
     * @param string $backupFile
     * @param int $snapshotId
     */
    public function __construct($remoteServer, $remoteUser, $remotePassword, $backupFile, $snapshotId)
    {
        $this->remoteServer = $remoteServer;
        $this->remoteUser = $remoteUser;
        $this->remotePassword = $remotePassword;
        $this->backupFile = $backupFile;
        $this->snapshotId = $snapshotId;
    }

    public function handle()
    {
        // Establish SFTP connection to the remote server
        $sftp = new SFTP($this->remoteServer);
        if (!$sftp->login($this->remoteUser, $this->remotePassword)) {
            throw new Exception('Failed to authenticate with remote server via SFTP');
        }

        // Stream the backup file from S3 directly to the remote server
        $s3Path = 'backups/' . $this->backupFile;
        $remoteBackupPath = '/tmp/' . $this->backupFile; // Remote path where the file will be saved

        // Get the S3 stream and put it directly to the remote server via SFTP
        $s3Stream = Storage::disk('s3')->readStream($s3Path); // Get the S3 stream
        if ($s3Stream === false) {
            throw new Exception('Failed to open S3 stream for backup file');
        }

        // Upload the stream directly to the remote server via SFTP
        if (!$sftp->put($remoteBackupPath, $s3Stream, SFTP::SOURCE_LOCAL_STREAM)) {
            throw new Exception('Failed to upload the backup file to the remote server via SFTP');
        }

        // Close the S3 stream
        fclose($s3Stream);

        // Chain the next job to apply the backup
        $this->chain(new ApplyBackup($this->remoteServer, $this->remoteUser, $this->remotePassword, $remoteBackupPath, $this->snapshotId))->dispatch();
    }
}
