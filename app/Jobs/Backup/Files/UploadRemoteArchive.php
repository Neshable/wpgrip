<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SFTP;
use Illuminate\Support\Facades\Storage;
use Exception;

class UploadRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $archiveFile;
    protected $snapshotId;

    public function __construct($remoteServer, $remoteUser, $remotePassword, $archiveFile, $snapshotId)
    {
        $this->remoteServer = $remoteServer;
        $this->remoteUser = $remoteUser;
        $this->remotePassword = $remotePassword;
        $this->archiveFile = $archiveFile;
        $this->snapshotId = $snapshotId;
    }

    public function handle()
    {
        // Establish SFTP connection
        $sftp = new SFTP($this->remoteServer);
        if (!$sftp->login($this->remoteUser, $this->remotePassword)) {
            throw new Exception('Failed to authenticate with remote server for SFTP');
        }

        // Open the remote archive file for streaming
        $stream = $sftp->getStream($this->archiveFile);
        if (!$stream) {
            throw new Exception('Unable to open archive file for streaming from remote server');
        }

        // Upload the archive to S3 with SSE-S3 (AES-256 encryption)
        $s3Path = 'backups/' . basename($this->archiveFile);
        Storage::disk('s3')->put($s3Path, $stream, [
            'visibility' => 'private',
            'Content-Type' => 'application/gzip',
            'ServerSideEncryption' => 'AES256' // Enable AES-256 encryption
        ]);

        // Close the stream after upload
        fclose($stream);

        // Chain the next job: DeleteRemoteBackupArchive
        $this->chain(new DeleteRemoteBackupArchive($this->remoteServer, $this->remoteUser, $this->remotePassword, $this->archiveFile, $this->snapshotId))->dispatch();
    }
}
