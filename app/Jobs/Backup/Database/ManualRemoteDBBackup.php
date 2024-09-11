<?php

namespace App\Jobs\Backup\Database;

use App\Models\Site;
use App\Models\Backup;
use App\Services\SSHService;

use Illuminate\Support\Facades\Storage;

use Filament\Notifications\Notification;
use Carbon\Carbon;
use App\Jobs\RemoteDBBackup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManualRemoteDBBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The site model
     *
     * @var App/Models/Site
     */
    public $site;

    public $timestamp;

    public $frequency;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $frequency = 'manual', $timestamp = null )
    {
        $this->site = $site;
        $this->frequency = $frequency;
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site )
        {
            $backup = Backup::create([
                'site_id' => $this->site->id,
                'team_id' => $this->site->team->id,
                'provider' => 's3',
                'file_path' => '',
                'type' => 'db',
                'frequency' => $this->frequency,
                'db_size' => 0, // in bytes
                'status' => 'processing',
                'delete_date' => Carbon::now()->addDays(60)
            ]);

            // Send notification to the user.
            if ( $backup->exists ) 
            {   
                // Dispatch background job and pass backup id to it?
                RemoteDBBackup::dispatch( $this->site, 'manual', $backup->id )->onQueue('longrunning');
            } 
        }
       
    }
   
}
