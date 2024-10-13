<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use App\Models\Snapshot;
use Exception;

class DeleteRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $archiveFile;
    protected $snapshotId;

    /**
     * DeleteRemoteBackupArchive constructor.
     *
     * @param string $remoteServer
     * @param string $remoteUser
     * @param string $remotePassword
     * @param string $archiveFile
     * @param int $snapshotId
     */
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
        // SSH into the remote server
        $ssh = new SSH2($this->remoteServer);
        if (!$ssh->login($this->remoteUser, $this->remotePassword)) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Delete the archive file from the remote server
        $ssh->exec("rm -f {$this->archiveFile}");

        // Verify the file is deleted
        $checkFile = $ssh->exec("if [ -f {$this->archiveFile} ]; then echo 'exists'; else echo 'not_found'; fi");
        if (trim($checkFile) === 'exists') {
            throw new Exception('Failed to delete archive from the remote server');
        }

        // Optionally update the snapshot status
        $snapshot = Snapshot::find($this->snapshotId);
        if ($snapshot) {
            $snapshot->status = 'complete'; // Or 'restored', depending on the context
            $snapshot->save();
        }
    }
}
