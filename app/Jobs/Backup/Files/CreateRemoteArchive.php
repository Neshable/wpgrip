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
    protected $backupDirectory;
    protected $archiveFile;
    protected $exclusions; // Store exclusions
    protected $snapshotId; // Store snapshot ID

    /**
     * CreateRemoteArchive constructor.
     *
     * @param string $remoteServer
     * @param string $remoteUser
     * @param string $remotePassword
     * @param string $backupDirectory
     * @param int $snapshotId
     * @param array $exclusions
     */
    public function __construct($remoteServer, $remoteUser, $remotePassword, $backupDirectory, $snapshotId, array $exclusions = [])
    {
        $this->remoteServer = $remoteServer;
        $this->remoteUser = $remoteUser;
        $this->remotePassword = $remotePassword;
        $this->backupDirectory = $backupDirectory;
        $this->archiveFile = '/tmp/backup-' . time() . '.tar.gz'; // Archive path on the remote server
        $this->exclusions = $exclusions; // Assign exclusions
        $this->snapshotId = $snapshotId; // Assign snapshot ID
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        // Establish SSH connection
        $ssh = new SSH2($this->remoteServer);
        if (!$ssh->login($this->remoteUser, $this->remotePassword)) {
            throw new Exception('Failed to authenticate with remote server');
        }

        // Build the tar command with exclusions
        $excludeOptions = '';
        foreach ($this->exclusions as $exclude) {
            $excludeOptions .= " --exclude='{$exclude}'";
        }

          // If there is a last backup timestamp, include only newer files
        //   $incrementalOption = '';
        //   if ($this->lastBackupTimestamp) {
        //       $incrementalOption = "--newer '{$this->lastBackupTimestamp}'"; // Include only files modified since last backup
        //   }

        // Create the archive file with tar and apply exclusions
        $tarCommand = "tar -czf {$this->archiveFile} {$excludeOptions} -C {$this->backupDirectory} .";
        $ssh->exec($tarCommand);

        // Verify archive creation success
        $checkFile = $ssh->exec("if [ -f {$this->archiveFile} ]; then echo 'exists'; else echo 'not_found'; fi");
        if (trim($checkFile) !== 'exists') {
            throw new Exception('Failed to create archive on the remote server');
        }

        // Move to next step: Chain Upload job
        $this->chain(new UploadRemoteArchive($this->remoteServer, $this->remoteUser, $this->remotePassword, $this->archiveFile, $this->snapshotId))->dispatch();
    }
}
