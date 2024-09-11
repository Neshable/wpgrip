<?php

namespace App\Jobs\Backup;

use App\Models\Site;
use App\Models\Backup;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

use App\Services\SSHSiteConnect;
use App\Services\SSHService;

use Illuminate\Support\Facades\Process;

use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Storage;
use App\Services\BackupLocation;
use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Bus\Batchable;


class CopyFromRemote implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public $maxExceptions = 2;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

    /**
     * The carbon timestamp
     *
     * @var Carbon
     */
    public $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, $timestamp = null )
    {
        $this->site = $site;
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site->dir_path ) {
            
            // Init a service.
            $ssh_service = new SSHService( $this->site, $this->timestamp );
           
            // https://spatie.be/products/laravel-backup-server
            $trigger = $ssh_service->rsyncRemoteDirectory();
   
            
            if ( $trigger ) {
                return true;
            }

            return false;
        
        }

    }
  

}
