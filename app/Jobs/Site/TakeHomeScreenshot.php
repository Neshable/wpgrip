<?php

namespace App\Jobs\Site;

use App\Models\Site;
use App\Services\TakeScreenshot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TakeHomeScreenshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries   = 2;
    public int $backoff = 30;

    public Site $site;
    public string $storage;

    public function __construct(Site $site, string $storage = 'public')
    {
        $this->site    = $site;
        $this->storage = $storage;
    }

    public function handle(): void
    {
        if (empty($this->site->url)) {
            Log::warning('TakeHomeScreenshot: site has no URL', ['site_id' => $this->site->id]);
            return;
        }

        // Delete the old screenshot from storage before taking a new one
        if ($this->site->screenshot_path) {
            try {
                if (Storage::disk($this->storage)->exists($this->site->screenshot_path)) {
                    Storage::disk($this->storage)->delete($this->site->screenshot_path);
                }
            } catch (\Throwable $e) {
                Log::warning('TakeHomeScreenshot: could not delete old screenshot', [
                    'site_id' => $this->site->id,
                    'path'    => $this->site->screenshot_path,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $service = new TakeScreenshot($this->site, $this->storage);
        $path    = $service->take();

        if ($path) {
            $this->site->screenshot_path = $path;
            $this->site->save();
        } else {
            Log::error('TakeHomeScreenshot: take() returned null', [
                'site_id' => $this->site->id,
                'url'     => $this->site->url,
            ]);
        }
    }
}
