<?php

namespace App\Jobs\Backup;

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

class RemoveFromS3 implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * The path to file in our S3 bucket
     *
     * @var string
     */
    public $path;

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
    public function __construct( string $path, $timestamp = null )
    {
        $this->path = $path;
        $this->timestamp = $timestamp ?: Carbon::now()->format('YmdHi');
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if( Storage::disk('s3')->exists( $this->path ) ) 
        {
            Storage::disk('s3')->delete($this->path);
        }      
    }
  

}
