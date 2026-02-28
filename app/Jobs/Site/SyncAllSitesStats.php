<?php

namespace App\Jobs\Site;

use App\Models\Site;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Jobs\Site\SyncSiteStats;
use App\Jobs\Site\GetAllPlugins;
use App\Jobs\Site\GetAllThemes;

class SyncAllSitesStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tenant_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tenant_id = false )
    {
        $this->tenant_id = $tenant_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->tenant_id ) {
            $sites = Site::where('is_staging', false)->where('tenant_id', $this->tenant_id )->get();   
        } else {
            $sites = Site::where('is_staging', false)->get();
        }
        
        /**
         * Loop and dispatch the background sync
         */
        foreach ($sites as $site) 
        {
            if ( $site->enabled )
            {
                SyncSiteStats::dispatch( $site )->onQueue('default');
                GetAllPlugins::dispatch( $site )->onQueue('default');
                GetAllThemes::dispatch( $site )->onQueue('default');
            }            
        }  
        
        return true;
    }
}
