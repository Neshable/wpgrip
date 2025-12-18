<?php

namespace App\Jobs\Tests;

use App\Models\Site;
use App\Models\PerformanceData;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// use AdityaDees\LaravelLighthouse\LaravelLighthouse;

use Spatie\Lighthouse\Lighthouse;
use Filament\Notifications\Notification;

use App\Services\External\PageSpeedInsightsService;

class PageSpeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * Either desktop or mobile
     *
     * @var [type]
     */
    public $strategy;


   /**
     * Create a new job instance.
     *
     * @param  \App\Models\Site  $site
     * @return void
     */
    public function __construct(Site $site, string $strategy = 'mobile')
    {
        $this->site = $site;
        $this->strategy = $strategy;
    }

 
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ( $this->site->url )
        {
            $insights = PageSpeedInsightsService::fetchInsights( $this->site->url, $this->strategy );
         
            if ( $insights && is_array( $insights ) && !empty($insights) )
            {
                PerformanceData::create([
                    'site_id' => $this->site->id,
                    'strategy' => $this->strategy,
                    ...$insights
                ]);

                if ($this->site->sitemeta) {
                    $this->site->sitemeta->lighthouse_last_sync = Carbon::now();
                    $this->site->sitemeta->save();
                }

                // Send notification
            }

        }

  
        
    }

}
