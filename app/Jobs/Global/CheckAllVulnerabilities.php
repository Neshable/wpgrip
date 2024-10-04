<?php

namespace App\Jobs\Global;

use App\Models\Site;


use App\Jobs\External\CheckPluginVulnerabilities;

use App\Services\GripNotifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckAllVulnerabilities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;


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
        // Get all sites.
        $sites = Site::where('is_staging', false)
             ->where('ssh_connection', true)
             ->where('enabled', true)
             ->get();

        foreach ( $sites as $site )
        {
            Bus::chain([
                // Check the core vulnerabilities
                // Check the Plugin vulnerabilities first.
                new CheckPluginVulnerabilities( $site ),
               // Check the themes vulnerabilities
               // Send notification if something new is found - use field is_notified
            ])->catch(function (Throwable $e) {
                //  First batch job failure detected
                // GripNotifications::getBackupFail( $record->user_id );
            })->onQueue('longrunning')->dispatch();
        }
    }
}




