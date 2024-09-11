<?php

namespace App\Jobs\Backup;

use App\Models\Site;
use Illuminate\Support\Facades\Process;
use Carbon\Carbon;
use App\Services\BackupLocation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

class DeleteAfterBackup implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


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
            
            $path_locations = new BackupLocation( $this->site, $this->timestamp );

            // Command for archive directory - @todo extract to service.
            $command = 'rm -rf ' . $path_locations->getLocalBackupSitePath();

            // Run the following local command.
            $process = Process::timeout(800)->run( $command );

            // executes after the command finishes
            if ( !$process->successful() ) {
                return false;
                // throw new ProcessFailedException($process);
            }

            return true;

        }
        
    }
  

}
