<?php

namespace App\Services;

use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class TakeScreenshot
{
    public string $storage;
    public string $url;
    public Site $site;
    public int $width;
    public int $height;
    public Speditor $speditor;

    public function __construct(Site $site, string $storage = 'public', int $width = 1280, int $height = 700)
    {
        $this->site     = $site;
        $this->url      = $site->url;
        $this->storage  = $storage;
        $this->width    = $width;
        $this->height   = $height;
        $this->speditor = new Speditor($site);
    }

    public function take(): ?string
    {
        if (empty($this->url)) {
            return null;
        }

        $path = $this->speditor->getS3ScreenshotPath()
            . '/homepage_' . Carbon::now()->format('YmdHis')
            . '_' . $this->site->id . '.jpg';

        $bytes = Browsershot::url($this->url)
            ->dismissDialogs()
            ->disableJavascript()
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setEnvironmentOptions(['HOME' => '/var/www'])
            ->windowSize($this->width, $this->height)
            ->waitUntilNetworkIdle()
            ->screenshot();

        $saved = Storage::disk($this->storage)->put($path, $bytes);

        return $saved ? $path : null;
    }

    /** @deprecated use take() */
    public function take_it(): ?string
    {
        return $this->take();
    }
}
