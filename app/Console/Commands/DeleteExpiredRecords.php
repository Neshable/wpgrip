<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Snapshot;
use App\Models\Deployment;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Backup\DeleteOldSnapshots;

class DeleteExpiredRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:expired-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete records that have reached their deletion date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         // Step 1: Fetch expired snapshots
         $expiredSnapshots = Snapshot::where('deletion_date', '<=', now() )->get();

         // Step 2: Dispatch jobs for each expired snapshot
         foreach ($expiredSnapshots as $snapshot) {
            DeleteOldSnapshots::dispatch( $snapshot );
            //  Bus::chain([
            //      new \App\Jobs\DeleteRemoteFileAndRecordJob($snapshot),
            //  ])->onQueue('longrunning')->dispatch();
         }
 
        // Prune deployment logs: keep only the 50 most recent per site-repository connection
        $pivotIds = Deployment::whereNotNull('pivot_id')
            ->select('pivot_id')
            ->groupBy('pivot_id')
            ->havingRaw('COUNT(*) > 50')
            ->pluck('pivot_id');

        foreach ($pivotIds as $pivotId) {
            $idsToKeep = Deployment::where('pivot_id', $pivotId)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->pluck('id');

            Deployment::where('pivot_id', $pivotId)
                ->whereNotIn('id', $idsToKeep)
                ->delete();
        }

        $this->info('Expired records have been scheduled for deletion.');
    }
}
