<?php

namespace App\Jobs\Site;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GripNotifications;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

use App\Models\Site;
use App\Models\VRT;

use App\Services\ImageComparision;

class CompareImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The VRT instance.
     *
     * @var \App\Models\VRT
     */
    public $vrt;

    /**
     * Create a new job instance.
     */
    public function __construct( VRT $vrt, Site $site )
    {
        $this->site = $site;
        $this->vrt = $vrt;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $image_comp_obj = new ImageComparision();
        
        // Compare the images. @todo change local to s3 disk
        $result =  $image_comp_obj->compare_images( 
            Storage::disk('public')->path( $this->vrt->file_path ), 
            Storage::disk('public')->path( $this->site->screenshot_path )
        );

        if ( $result )
        {
            $this->vrt->similarity = $result;
            $this->vrt->save();
            $this->handleSimilarity( $result );
        }
    }

    /**
     * Compare the similarity score and trigger notificaiton if we need to.
     *
     * @param [type] $score
     * @return void
     */
    public function handleSimilarity(float $score, int $threshold = 96 ) 
    {
        if ( !$score ) 
        {
            return false;
        }

        if ($score <= 49) 
        {
            GripNotifications::getScreenshotMissmatchWarningToDB(
                'Visual regression test fail',
                'One or more of your recent regression tests failed. Please verify.',
                $this->site
            );
        }
        elseif ( $score <= $threshold ) 
        {
            GripNotifications::getScreenshotMissmatchWarningToDB(
                'Visual regression test fail',
                'One or more of your recent regression tests failed. Please verify.',
                $this->site
            );
        } else 
        {
            return true;
        }
    }
}
