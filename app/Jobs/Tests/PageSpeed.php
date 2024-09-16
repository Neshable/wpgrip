<?php

namespace App\Jobs\Tests;

use App\Models\Site;
use App\Models\PerformanceScore;

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
         
            if ( $insights && is_array( $insights ) )
            {
                PerformanceScore::create([
                    'site_id' => $this->site->id,
                    'team_id' => $this->site->team->id,
                    'strategy' => $this->strategy,
                    ...$insights
                    // 'performance' => $insights['performance'],
                    // 'fcp' => 6.0,
                    // "performance" => 22.0,
                    // "fcp" => 6.0
                    // "total_blocking_time" => 0.6
                    // "speed_index" => 13.2
                    // "lcp" => 17.7
                    // "time_interactive" => 1844425.0 //milliseconds
                    // "fmp" => 598650.0
                    // "server-response-time" => 43200
                ]);

                $this->site->sitemeta->lighthouse_last_sync = Carbon::now();
                $this->site->sitemeta->save();

                // Send notification
            }

        }

  
        
    }

}
