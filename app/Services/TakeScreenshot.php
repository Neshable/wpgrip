<?php

namespace App\Services;

use App\Models\Site;

use SapientPro\ImageComparator\ImageComparator;
use App\Services\GripNotifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use Spatie\Browsershot\Browsershot;

use Filament\Notifications\Notification;
use App\Services\Speditor;

class TakeScreenshot {
    /**
     * Laravel storage provider - local, s3, etc.
     *
     * @var string
     */
    public $storage;

    /**
     * URL to take screenshot from
     *
     * @var string
     */
    public $url;

    /**
     * The site model
     *
     * @var Site
     */
    public $site;

    /**
     * Width of the screenshot in px
     *
     * @var int
     */
    public $width;

    /**
     * Height of the screenshot
     *
     * @var int
     */
    public $height;

    /**
     * Speditor class for file locations
     *
     * @var Speditor
     */
    public $speditor;

    /**
     * Constructor
     *
     * @param string|null $url
     * @param string $storage
     * @param integer $width
     * @param integer $height
     */
    public function __construct( ?string $url = null, $storage = 'local', $site, $width = 1280, $height = 700 )
    {
        $this->url =  $url;
        $this->storage =  $storage;
        $this->site = $site;
        $this->width =  $width;
        $this->height =  $height;
        // Create speditor object.
        $this->speditor = new Speditor( $site );
    }

    public function take_it()
    {
        if ( !$this->url )
        {
            // Bail of no url provided.
            return false;
        }

        $browsershot_obj = Browsershot::url( $this->url )
        ->setNodeBinary('/usr/bin/node')
        ->setNpmBinary('/usr/bin/npm');

        // Name of the file.
        $now_carbon = Carbon::now();    
        $path = $this->speditor->getS3ScreenshotPath() . '/homepage_' . $now_carbon . '_' . $this->site->id . '.jpg';

        $save = Storage::disk( $this->storage )->put(
            $path,
            $browsershot_obj
                ->windowSize( $this->width , $this->height )
                ->waitUntilNetworkIdle()
                ->screenshot()
        );

        if ( $save )
        {
            return $path;
        }
        
        return false;
    }

  
}