<?php

namespace App\Jobs\Tests;

use App\Models\Site;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Jobs\Tests\PageSpeed;


class ScheduleTests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Little helper var to determine if we should dispatch.
     *
     * @var [type]
     */
    private $should_dispatch;

    public $current_date_time;


   /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->should_dispatch = false;
        $this->current_date_time = Carbon::now();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Important to keep only prod sites.
        $sites = Site::where('is_staging', false)->get();


        foreach ($sites as $site) 
        {
            // Reset the variable.
            $this->should_dispatch = false;
            if ( !isset( $site->sitemeta ) ) 
            {
                continue;
            }

            if ( !$site->sitemeta->lighthouse_last_sync )
            {
                $this->should_dispatch = true;
            }
            
            else
            {
                // Init a carbon object out of created date.
                $carbon_created_at = Carbon::parse( $site->sitemeta->lighthouse_last_sync );

                // Check if it's time for the new backup - if had passed more than 1 day.
                if ( $this->current_date_time->gte( $carbon_created_at->addHours(24) ) )
                {
                    // It's time - mark it as true.
                    $this->should_dispatch = true;
                }    
            }

            // Dispatch the tests we need to do on the website.
            if ( $this->should_dispatch )
            {
                // @todo list jobs as an array?
                PageSpeed::dispatch( $site, 'mobile' );
                PageSpeed::dispatch( $site, 'desktop' );
            }
  
        }
    }
}
