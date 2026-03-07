<?php

namespace App\Console\Commands;

use App\Jobs\Backup\DeleteOldSnapshots;
use App\Models\Deployment;
use App\Models\MonitorLog;
use App\Models\PerformanceData;
use App\Models\Snapshot;
use Illuminate\Console\Command;

class DeleteExpiredRecords extends Command
{
    protected $signature = 'delete:expired-records';

    protected $description = 'Delete records that have reached their deletion date';

    /**
     * Number of days to retain time-series / history data.
     */
    protected int $retentionDays = 30;

    public function handle(): void
    {
        $this->deleteExpiredSnapshots();
        $this->pruneDeploymentLogs();
        $this->pruneMonitorLogs();
        $this->prunePerformanceData();

        $this->info('Expired records have been scheduled for deletion.');
    }

    /**
     * Dispatch deletion jobs for snapshots past their deletion_date.
     */
    protected function deleteExpiredSnapshots(): void
    {
        $expired = Snapshot::where('deletion_date', '<=', now())->get();

        foreach ($expired as $snapshot) {
            DeleteOldSnapshots::dispatch($snapshot);
        }

        if ($expired->count()) {
            $this->line("Scheduled {$expired->count()} expired snapshot(s) for deletion.");
        }
    }

    /**
     * Keep only the 50 most recent deployments per site-repository pivot.
     */
    protected function pruneDeploymentLogs(): void
    {
        $pivotIds = Deployment::whereNotNull('pivot_id')
            ->select('pivot_id')
            ->groupBy('pivot_id')
            ->havingRaw('COUNT(*) > 50')
            ->pluck('pivot_id');

        $totalDeleted = 0;

        foreach ($pivotIds as $pivotId) {
            $idsToKeep = Deployment::where('pivot_id', $pivotId)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->pluck('id');

            $totalDeleted += Deployment::where('pivot_id', $pivotId)
                ->whereNotIn('id', $idsToKeep)
                ->delete();
        }

        if ($totalDeleted) {
            $this->line("Pruned {$totalDeleted} old deployment log(s).");
        }
    }

    /**
     * Delete monitor_logs older than the retention period.
     */
    protected function pruneMonitorLogs(): void
    {
        $deleted = MonitorLog::where('created_at', '<', now()->subDays($this->retentionDays))->delete();

        if ($deleted) {
            $this->line("Deleted {$deleted} monitor log(s) older than {$this->retentionDays} days.");
        }
    }

    /**
     * Delete performance_data (Lighthouse results) older than the retention period.
     */
    protected function prunePerformanceData(): void
    {
        $deleted = PerformanceData::where('created_at', '<', now()->subDays($this->retentionDays))->delete();

        if ($deleted) {
            $this->line("Deleted {$deleted} performance data record(s) older than {$this->retentionDays} days.");
        }
    }
}
