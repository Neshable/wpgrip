<?php

namespace App\Jobs\Backup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Snapshot;
use Exception;


class DeleteOldSnapshots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $snapshot;

    /**
     * Create a new job instance.
     *
     * @param Snapshot $snapshot
     */
    public function __construct(Snapshot $snapshot)
    {
        $this->snapshot = $snapshot;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
       
        // Step 1: Delete the remote file from S3
        if ( $this->snapshot->remote_path ) {
            if ( Storage::disk('s3')->exists( $this->snapshot->remote_path . '/' . $this->snapshot->local_path ) ) {
                Storage::disk('s3')->delete( $this->snapshot->remote_path . '/' . $this->snapshot->local_path );
            }
        }

        // Step 2: Delete the model record
        $this->snapshot->delete();
    }
}
