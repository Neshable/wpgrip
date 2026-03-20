<?php

namespace App\Ai\Tools;

use App\Models\Site;
use App\Models\Backup;
use App\Jobs\Backup\ChainDbBackup;
use App\Jobs\Backup\ChainFilesBackup;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class TriggerBackup implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Trigger a backup job for the site. This dispatches the backup to the queue and returns immediately. The backup will complete in the background.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->enum(['files', 'database', 'full'])->description('The type of backup to create. Defaults to full.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        if (!$this->site->backup_enabled) {
            return 'Backups are not enabled for this site. The user needs to enable backups in the site settings first.';
        }

        try {
            $type = $request['type'] ?? 'full';

            $backup = Backup::create([
                'site_id'   => $this->site->id,
                'tenant_id' => $this->site->tenant_id,
                'type'      => $type === 'files' ? 'files' : 'db',
                'frequency' => 'manual',
                'status'    => 'pending',
            ]);

            if ($type === 'database') {
                ChainDbBackup::dispatch($backup, 'manual');
            } elseif ($type === 'files') {
                ChainFilesBackup::dispatch($backup, 'manual');
            } else {
                // Full backup: dispatch both DB and files
                ChainDbBackup::dispatch($backup, 'manual');

                $filesBackup = Backup::create([
                    'site_id'   => $this->site->id,
                    'tenant_id' => $this->site->tenant_id,
                    'type'      => 'files',
                    'frequency' => 'manual',
                    'status'    => 'pending',
                ]);
                ChainFilesBackup::dispatch($filesBackup, 'manual');
            }

            return "Backup job ({$type}) has been queued. It will run in the background. Check the backup history for status.";
        } catch (\Throwable $e) {
            return 'Failed to dispatch backup job: ' . $e->getMessage();
        }
    }
}
