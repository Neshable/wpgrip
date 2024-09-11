<?php

namespace App\Jobs\Staging;

use App\Models\Site;

use App\Services\GripNotifications;

use Filament\Notifications\Notification;
use App\Jobs\Staging\SyncDatabase;
use App\Jobs\Staging\SyncFiles;
use App\Jobs\Staging\SyncFinalTweaks;
use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SyncLiveToStaging implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   
    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $staging_site;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site )
    {    
        $this->site = $site;
        $this->staging_site = $this->site->children()->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site && $this->staging_site ) 
        {
          
            $batch = Bus::batch([
                [
                    new SyncDatabase($this->site),
                ],
                [
                    new SyncFiles($this->site),
                ],
                [
                    new SyncFinalTweaks($this->site),
                ]
            ])->then(function (Batch $batch) {
                // GripNotifications::getDatabaseSyncedNotification();
            })->catch(function (Batch $batch, Throwable $e) {
                // Somet catch.
            })->dispatch();

            // session(['batch_id' => $batch->id]); // Or store it in the database
            
            
            // return true;
        }   
    }
}
