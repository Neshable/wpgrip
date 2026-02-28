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
use App\Services\SSHSiteConnect;

class DeleteRemoteArchive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;
    public int $backoff = 60;

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
            throw new Exception('Snapshot has no local_path');
        }

        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            throw new Exception('SSH authentication failed for site ' . $this->site->id);
        }

        $tmpDir  = rtrim($this->site->dir_path, '/') . '/tmp';
        $outFile = $tmpDir . '/' . $snapshot->local_path;

        // Delete the remote archive (idempotent — no error if already gone)
        $connection->exec("rm -f {$outFile}");

        // Verify deletion using a correctly formed shell test
        $check = trim($connection->exec("[ -f {$outFile} ] && echo exists || echo gone"));
        $connection->close();

        if ($check === 'exists') {
            throw new Exception('Remote archive still present after delete: ' . $outFile);
        }

        // Mark snapshot complete
        $snapshot->status = 'completed';
        $snapshot->save();

        // Update the parent backup record
        if ($snapshot->backup) {
            $totalSize = $snapshot->backup->snapshots()->sum('size');

            $snapshot->backup->last_backup = Carbon::now();
            if ($totalSize > 0) {
                $snapshot->backup->size = $totalSize;
            }
            $snapshot->backup->save();
        }
    }
}
