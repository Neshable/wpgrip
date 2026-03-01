<?php

namespace App\Jobs\Tests;

use App\Models\Site;
use App\Models\VRT;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\GripNotifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use Spatie\Browsershot\Browsershot;
use Filament\Notifications\Notification;
use App\Jobs\Site\CompareImages;
use App\Services\Speditor;
use App\Services\ImageComparision;

class VRTTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The Page we are screenshoting
     *
     * @var string
     */
    public $page;

    /**
     * The HTML Output
     *
     * @var string
     */
    public $html;

    /**
     * Speditor class
     *
     * @var Speditor
     */
    public $speditor;

    /**
     * Control if the process is run with dispatchSync
     * Enable realtime notification
     *
     * @var bool
     */
    public $manual;



   /**
     * Create a new job instance.
     *
     * @param  \App\Models\Site  $site
     */
    public function __construct( Site $site, $manual = false, string $page = 'home' )
    {
        $this->site = $site;
        $this->page = $page;
        $this->html = 'No tests';
        $this->manual = $manual;
        $this->speditor = new Speditor( $site );
    }


    /**
     * Execute the job.
     */
    public function handle()
    {
        if ( $this->site->url )
        {
            // Make sure we have the default home screenshot for reference.
            if ( $this->site->screenshot_path && Storage::disk('public')->exists( $this->site->screenshot_path ) ) 
            {   
                // Extract these to global variables.
                $browsershot_obj = Browsershot::url( $this->site->url )
                ->setNodeBinary('/usr/bin/node')
                ->setNpmBinary('/usr/bin/npm');

                $now_carbon = Carbon::now();
                $path_to_save = $this->speditor->getS3ScreenshotPath() . '/home_shot_' . $now_carbon . '_' . $this->site->id . '.jpg';
                
                Storage::disk('public')->put(
                    $path_to_save,
                    $browsershot_obj->windowSize(1280, 700)
                        ->waitUntilNetworkIdle()
                        ->screenshot()
                );
    
                $vrt = VRT::create([
                    'site_id' => $this->site->id,
                    'url' => $this->site->url,
                    'file_path' => $path_to_save,
                    'console' => '',
                    // 'similarity' => , // in bytes
                    'control' => false,
                ]);

                // Send notification to the user.
                if ( $vrt->exists ) 
                {   
                    // Dispatch async — comparison runs in background.
                    CompareImages::dispatch( $vrt, $this->site );
                    if ( $this->manual )
                    {
                        // If all good return notification.   
                        GripNotifications::getVRTSuccessNotification();
                    }
                    
                } 
            }
            else // We don't have reference screenshot, we need to create one!
            {
                if ( $this->manual )
                {
                GripNotifications::getNoDefaultScreenshotNotification();
                }
            }
   
        }
     
    }

}
