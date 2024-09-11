<?php

namespace App\Jobs\Scheduled;

use App\Models\Site;
use App\Models\PHPLog;

use App\Jobs\Server\TailErrorLog;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use App\Services\SlackNotifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleErrorLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    public $current_date_time;


   /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->current_date_time = Carbon::now();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all sites
        $sites = Site::whereNotNull('error_log_path')
            ->where('error_log_path', '!=', '')
            ->where('is_staging', false)
            ->get();
        
        // Dispatch jobs for each site
        foreach($sites as $site) {
             // Dispatch 'fatal' job
             TailErrorLog::dispatch($site, 'fatal')->onQueue('default');
 
             // If you want to ensure that the 'parse' job is only dispatched after the 'fatal' one is finished
             // Consider dispatching it in the 'fatal' job's `handle` method or use `chain` method if it's in the same queue
             // Otherwise, 'parse' job will be dispatched immediately after 'fatal', they are not guaranteed to run one after another
             TailErrorLog::dispatch($site, 'parse')->onQueue('default');
         }
    }
}
