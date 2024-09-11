<?php

namespace App\Jobs\Site;

use App\Models\Site;

use App\Services\TakeScreenshot;
use App\Services\GripNotifications;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class TakeHomeScreenshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * Storage for the screenshot
     *
     * @var string
     */
    public $storage;

   


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, $storage = 'public' )
    {
        // Get original production site.
        $this->site = $site;
        $this->storage =  $storage;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site ) {
            // Check if we have a screenshot already and delete it.
            if ( $this->site->screenshot_path )
            {
                $this->deleteOld( $this->site->screenshot_path );
            }
            // Init the object.
            $new_take_screenshot = new TakeScreenshot( $this->site->url, $this->storage, $this->site );
            // Take the screenshot and return the filename to save in the db
            $screenshot_path = $new_take_screenshot->take_it();
            // Save it to the database.
            if ( $screenshot_path )
            {   
                $this->site->screenshot_path = $screenshot_path;
                $this->site->save();
            }
        }   

    }

    /**
     * Delete the old screenshot first.
     *
     * @return void
     */
    public function deleteOld( string $path )
    {
        if ( Storage::disk($this->storage)->exists( $path ) ) 
        {
            Storage::disk($this->storage)->delete( $path );
            $this->site->screenshot_path = null;
        }
    }
}
