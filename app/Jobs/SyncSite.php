<?php

namespace App\Jobs;


use App\Models\Site;

use App\Models\User;
use Filament\Notifications\Notification;

use Spatie\Lighthouse\Lighthouse;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Site  $site
     * @return void
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 
        
        // returns an instance of Spatie\Lighthouse\LighthouseResult
        // $result = Lighthouse::url('https://example.com')->run();
        // dd($result->scores());

        $this->send_notification();
        return true;
    }

    public function send_notification()
    {
        $recipient = auth()->user();
 
        $recipient->notify(
            Notification::make()
                ->title('Website Synced Successfully ')
                ->success()
                ->body( $this->site->name ) 
                ->toDatabase(),
        );
    }
}
