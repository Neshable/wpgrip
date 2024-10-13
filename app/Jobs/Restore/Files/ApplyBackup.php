<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use Exception;

class ApplyBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $backupFilePath;
    protected $snapshotId;

    /**
     * ApplyBackup constructor.
     *
     * @param string $remoteServer
     * @param string $remoteUser
     * @param string $remotePassword
     * @param string $backupFilePath
     * @param int $snapshotId
     */
    public function __construct($remoteServer, $remoteUser, $remotePassword, $backupFilePath, $snapshotId)
    {
        $this->remoteServer = $remoteServer;
        $this->remoteUser = $remoteUser;
        $this->remotePassword = $remotePassword;
        $this->backupFilePath = $backupFilePath;
        $this->snapshotId = $snapshotId;
    }

    public function handle()
    {
        // SSH into the remote server
        $ssh = new SSH2($this->remoteServer);
        if (!$ssh->login($this->remoteUser, $this->remotePassword)) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Extract the backup archive and apply it
        $extractCommand = "tar -xzf {$this->backupFilePath} -C /path/to/your/app/directory"; // Adjust the path
        $ssh->exec($extractCommand);

        // Verify if extraction was successful
        $checkExtraction = $ssh->exec("if [ $? -eq 0 ]; then echo 'success'; else echo 'failed'; fi");
        if (trim($checkExtraction) !== 'success') {
            throw new Exception('Failed to extract backup on the remote server');
        }

        // Chain the next job to delete the backup
        $this->chain(new DeleteDownloadedBackup($this->remoteServer, $this->remoteUser, $this->remotePassword, $this->backupFilePath, $this->snapshotId))->dispatch();
    }
}
