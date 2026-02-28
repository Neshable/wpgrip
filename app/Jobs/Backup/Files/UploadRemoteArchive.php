<?php

namespace App\Jobs\Backup\Files;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;
use App\Models\Snapshot;
use App\Services\SFTPSiteConnect;

class UploadRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries   = 2;
    public int $backoff = 300;

    private $site;
    private int $snapshotId;

    public function __construct($site, int $snapshotId)
    {
        $this->site       = $site;
        $this->snapshotId = $snapshotId;
    }

    public function handle(): void
    {
        $snapshot = Snapshot::findOrFail($this->snapshotId);

        if (!$snapshot->local_path) {
            throw new Exception('Snapshot has no local_path — CreateRemoteDatabaseArchive may have failed');
        }

        $sftpService = new SFTPSiteConnect($this->site);
        if (!$sftpService->active) {
            throw new Exception('SFTP authentication failed for site ' . $this->site->id);
        }

        $remotePath = rtrim($this->site->dir_path, '/') . '/tmp/' . $snapshot->local_path;
        $s3Dir      = 'backups/site-' . $this->site->id;
        $s3Key      = $s3Dir . '/' . basename($snapshot->local_path);

        // ── Stream from SFTP directly into S3 ──────────────────────────────
        // phpseclib SFTP::get() can write directly to a PHP stream handle.
        // We open a temp stream in memory (no disk write on the Laravel server).
        $tmpStream = fopen('php://temp', 'r+');
        if (!$tmpStream) {
            throw new Exception('Could not open php://temp stream');
        }

        if (!$sftpService->sftp->get($remotePath, $tmpStream)) {
            fclose($tmpStream);
            throw new Exception('SFTP download failed for: ' . $remotePath);
        }

        rewind($tmpStream);

        // putStream() uploads a PHP resource — S3 SDK streams it in chunks,
        // so the Laravel server never holds the whole file in memory.
        $ok = Storage::disk('s3')->putStream($s3Key, $tmpStream);
        fclose($tmpStream);

        if (!$ok) {
            throw new Exception('S3 upload failed for key: ' . $s3Key);
        }

        $s3Size = Storage::disk('s3')->size($s3Key);

        $snapshot->remote_path   = $s3Dir;
        $snapshot->status        = 'cleaning';
        $snapshot->size          = $s3Size;
        $snapshot->deletion_date = Carbon::now()->addDays(60);
        $snapshot->save();
    }
}
