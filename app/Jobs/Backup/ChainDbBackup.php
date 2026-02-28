<?php

namespace App\Jobs\Backup;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Exception;

use App\Models\Snapshot;
use App\Models\Site;
use App\Models\Backup;

use App\Jobs\Backup\Database\CreateRemoteDatabaseArchive;
use App\Jobs\Backup\Files\UploadRemoteArchive;
use App\Jobs\Backup\Files\DeleteRemoteArchive;

class ChainDbBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 30;
    public int $tries   = 1;

    private Backup $backup;
    public string $type;

    public function __construct(Backup $backup, string $type = 'manual')
    {
        $this->backup = $backup;
        $this->type   = $type;
    }

    public function handle(): void
    {
        $site = Site::find($this->backup->site_id);
        if (!$site) {
            throw new Exception('Site not found for backup id ' . $this->backup->id);
        }

        $snapshot = Snapshot::create([
            'enabled'   => true,
            'status'    => 'pending',
            'type'      => $this->type,
            'backup_id' => $this->backup->id,
            'tenant_id' => $this->backup->tenant_id,
        ]);

        Bus::chain([
            new CreateRemoteDatabaseArchive($site, $snapshot->id),
            new UploadRemoteArchive($site, $snapshot->id),
            new DeleteRemoteArchive($site, $snapshot->id),
        ])->catch(function (Throwable $e) use ($snapshot) {
            // Mark snapshot as failed so the UI reflects it
            $snapshot->status = 'failed';
            $snapshot->save();
        })->onQueue('longrunning')->dispatch();
    }
}
