<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Snapshot;
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
 
        // Additional models with different types of deletion logic can also be added here
        $this->info('Expired records have been scheduled for deletion.');
    }
}
