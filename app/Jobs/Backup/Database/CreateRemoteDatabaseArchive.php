<?php

namespace App\Jobs\Backup\Database;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SSHSiteConnect;
use Exception;
use App\Models\Snapshot;

class CreateRemoteDatabaseArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int Max seconds this job may run (30 min) */
    public int $timeout = 1800;

    /** @var int Retry once after 5 minutes if it fails */
    public int $tries = 2;
    public int $backoff = 300;

    protected $site;
    protected string $archiveFile;
    protected int $snapshotId;

    public function __construct($site, int $snapshotId)
    {
        $this->site       = $site;
        $this->snapshotId = $snapshotId;
        // Unique filename per snapshot — avoids collisions when multiple sites
        // trigger backups simultaneously.
        $this->archiveFile = 'db-backup-' . $site->id . '-' . $snapshotId . '-' . time() . '.sql.gz';
    }

    public function handle(): void
    {
        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            throw new Exception('SSH authentication failed for site ' . $this->site->id);
        }

        $dir     = rtrim($this->site->dir_path, '/');
        $tmpDir  = $dir . '/tmp';
        $outFile = $tmpDir . '/' . $this->archiveFile;

        // ── 1. Ensure tmp dir exists ──────────────────────────────────────────
        $connection->exec("mkdir -p {$tmpDir}");

        // ── 2. Disk-space check (df returns KB; convert to bytes) ─────────────
        // We estimate the dump will be at most the raw DB data size on disk.
        $rawDbSize = (int) trim($connection->exec(
            "wp --path={$dir} db size --size_format=b --all-tables --skip-plugins --skip-themes 2>/dev/null || echo 0"
        ));

        // df -k returns Available in 1K-blocks
        $availableKb = (int) trim($connection->exec("df -Pk {$tmpDir} | awk 'NR==2{print \$4}'"));
        $availableBytes = $availableKb * 1024;

        if ($availableBytes > 0 && $availableBytes < $rawDbSize) {
            throw new Exception(
                sprintf('Not enough disk space on remote server: need ~%s MB, have %s MB',
                    round($rawDbSize / 1048576, 1),
                    round($availableBytes / 1048576, 1)
                )
            );
        }

        // ── 3. Dump + gzip in one pipeline (no intermediate .sql file) ────────
        // --single-transaction + --quick avoids locking tables and keeps memory
        // usage low on the remote server.
        // Write to a .tmp file first, rename on success — prevents a partial
        // archive from being picked up by UploadRemoteArchive if something dies.
        $dumpCmd = implode(' ', [
            "wp --path={$dir} db export",
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
            '--all-tablespaces',
            '--skip-plugins',
            '--skip-themes',
            '-',
            "| gzip -6 > {$outFile}.tmp",
            "&& mv {$outFile}.tmp {$outFile}",
            "&& chmod 600 {$outFile}",
        ]);
        $connection->exec($dumpCmd);

        // ── 4. Verify the archive was actually created and is non-empty ────────
        $sizeOut = trim($connection->exec("stat -c%s {$outFile} 2>/dev/null || echo 0"));
        if ((int) $sizeOut === 0) {
            throw new Exception('Remote archive is missing or empty after dump: ' . $outFile);
        }

        // ── 5. Persist the archive name in the snapshot ───────────────────────
        $snapshot = Snapshot::findOrFail($this->snapshotId);
        $snapshot->local_path = $this->archiveFile;
        $snapshot->status     = 'archived';
        $snapshot->size       = (int) $sizeOut;
        $snapshot->save();

        $connection->close();
    }
}
