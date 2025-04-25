<?php

/**
 * Job to take a screenshot of a site's homepage.
 *
 * This job handles the process of taking a screenshot of a site's homepage,
 * storing it in the specified storage disk, and updating the site record
 * with the new screenshot path.
 */

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
use Exception;

class TakeHomeScreenshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * Storage disk for the screenshot.
     *
     * @var string
     */
    public $storage;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Site $site The site to take a screenshot of
     * @param string $storage The storage disk to use
     * @return void
     */
    public function __construct(Site $site, string $storage = 'public')
    {
        $this->site = $site;
        $this->storage = $storage;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception If the screenshot process fails
     */
    public function handle()
    {
        if (!$this->site) {
            Log::error('Site instance is null in TakeHomeScreenshot job');
            return;
        }

        try {
            // Check if we have a screenshot already and delete it
            if ($this->site->screenshot_path) {
                $this->deleteOld($this->site->screenshot_path);
            }

            // Validate URL before proceeding
            if (empty($this->site->url)) {
                Log::error('Site URL is empty', ['site_id' => $this->site->id]);
                return;
            }

            // Initialize the screenshot service
            $new_take_screenshot = new TakeScreenshot(
                $this->site->url,
                $this->storage,
                $this->site
            );

            // Take the screenshot and get the path
            $screenshot_path = $new_take_screenshot->take_it();

            // Save the new screenshot path to the database
            if ($screenshot_path) {
                $this->site->screenshot_path = $screenshot_path;
                $this->site->save();
            } else {
                Log::error('Failed to take screenshot', [
                    'site_id' => $this->site->id,
                    'url' => $this->site->url
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error taking screenshot', [
                'site_id' => $this->site->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete the old screenshot.
     *
     * @param string $path The path of the screenshot to delete
     * @return void
     */
    private function deleteOld(string $path): void
    {
        try {
            if (Storage::disk($this->storage)->exists($path)) {
                Storage::disk($this->storage)->delete($path);
                $this->site->screenshot_path = null;
            }
        } catch (Exception $e) {
            Log::error('Error deleting old screenshot', [
                'site_id' => $this->site->id,
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
    }
}
