<?php

namespace App\Jobs\Site;

use App\Models\Site;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Jobs\Site\SyncSiteStats;

class SyncAllSitesStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sites = Site::where('is_staging', false)->get();

        foreach ($sites as $site) 
        {
            if ( $site->enabled )
            {
                SyncSiteStats::dispatch( $site, $latest_backup->frequency )->onQueue('default');
            }            
        }  
        
        return;
    }
}
