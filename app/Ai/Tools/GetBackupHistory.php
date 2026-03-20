<?php

namespace App\Ai\Tools;

use App\Models\Site;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetBackupHistory implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Get the recent backup history for the site, including backup status, type, and size.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()->min(1)->max(20)->default(10),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $this->site->loadMissing('backups');
        $limit = $request['limit'] ?? 10;

        $backups = $this->site->backups()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        if ($backups->isEmpty()) {
            return 'No backups found for this site. Backup may not be enabled.';
        }

        $lines = [
            '## Recent Backups',
            '- Backup enabled: '.($this->site->backup_enabled ? 'yes' : 'no'),
            '',
            '| Date | Type | Status | Size |',
            '|---|---|---|---|',
        ];

        foreach ($backups as $backup) {
            $lines[] = sprintf(
                '| %s | %s | %s | %s |',
                $backup->created_at,
                $backup->type ?? 'full',
                $backup->status ?? 'unknown',
                $backup->size ? round($backup->size / 1024 / 1024, 1).' MB' : 'n/a',
            );
        }

        return implode("\n", $lines);
    }
}
