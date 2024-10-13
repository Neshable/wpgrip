<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpseclib3\Net\SSH2;
use Exception;

class ApplyDatabaseBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $remoteServer;
    protected $remoteUser;
    protected $remotePassword;
    protected $backupFilePath;
    protected $snapshotId;

    /**
     * ApplyDatabaseBackup constructor.
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

        // Run the gunzip and import command simultaneously
        $importCommand = "gunzip -c {$this->backupFilePath} | wp db import - --path=/path/to/your/wp-app";
        $ssh->exec($importCommand);

        // Verify if the import was successful
        $checkImport = $ssh->exec("if [ $? -eq 0 ]; then echo 'success'; else echo 'failed'; fi");
        if (trim($checkImport) !== 'success') {
            throw new Exception('Failed to import database backup on the remote server');
        }

        // Chain the next job to delete the original .sql.gz file from the remote server
        $this->chain(new DeleteRemoteBackupArchive($this->remoteServer, $this->remoteUser, $this->remotePassword, $this->backupFilePath, $this->snapshotId))->dispatch();
    }
}
