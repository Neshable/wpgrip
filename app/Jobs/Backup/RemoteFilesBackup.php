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
use Carbon\Carbon;

use App\Jobs\Backup\CopyFromRemote;
use App\Jobs\Backup\CreateArchive;
use App\Jobs\Backup\SendToS3;
use App\Jobs\Backup\DeleteAfterBackup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;


class RemoteFilesBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200;

    /**
     * The Site instance.
     *
     * @var \App\Models\Site
     */
    public $site;

     /**
     * The server instance.
     *
     * @var \App\Models\server
     */
    public $server;

     /**
     * The User instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The frequency of the backup
     *
     * @param string $frequency can be daily, weekly, biweekly, monthly
     */
    public $frequency;

    /**
     * The timestamp used for backup
     *
     * @var Carbon
     */
    public $timestamp;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Site $site, string $frequency = 'weekly' )
    {
        $this->site = $site;
        $this->server = $this->site->server;
        $this->frequency = $frequency;
        $this->user = auth()->user();
        // Get a timestamp
        $this->timestamp = Carbon::now()->format('YmdHi');
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->site->dir_path ) {

            // Processing logic here...
            $batch = Bus::chain([
                new CopyFromRemote( $this->site, $this->timestamp ),
                new CreateArchive( $this->site, $this->timestamp ),
                new SendToS3( $this->site, $this->timestamp ),
                new DeleteAfterBackup( $this->site, $this->timestamp )
            ])->catch(function (Batch $batch, Throwable $e) {
                // First batch job failure detected
                $this->user->notify(
                    Notification::make()
                        ->title('Files backup failure.')
                        ->error()
                        ->body( 'There was an error doing the backup ' ) 
                        ->toDatabase(),
                );
            })->dispatch()->onQueue('longrunning');

            // You can then check the status of the batch using the batch ID
            // $batchId = $batch->id;

             // All jobs completed successfully
             $this->user->notify(
                Notification::make()
                    ->title('Files backup successs.')
                    ->success()
                    ->body( 'Files backup for ' . $this->site->name . ' is done.' ) 
                    ->toDatabase(),
            );

            // Later, or in a different request, you can retrieve the batch:
            //$batch = Bus::findBatch($batchId);

            // if ($batch->finished()) {
            //     // All jobs within the batch are finished
            // }
        
        }
        
    }


      

}
