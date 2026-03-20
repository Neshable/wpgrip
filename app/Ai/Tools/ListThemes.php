<?php

namespace App\Ai\Tools;

use App\Models\Site;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListThemes implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'List all WordPress themes installed on the site with their version, status, available updates, and vulnerability information.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status_filter' => $schema->string()->enum(['all', 'active', 'inactive', 'update-available'])->default('all'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $this->site->loadMissing('themes');
        $themes = $this->site->themes;

        if ($themes->isEmpty()) {
            return 'No themes found for this site. The site may need to be synced.';
        }

        $filter = $request['status_filter'] ?? 'all';

        $filtered = $themes->filter(function ($theme) use ($filter) {
            if ($filter === 'all') return true;
            if ($filter === 'active') return $theme->pivot->status === 'active';
            if ($filter === 'inactive') return $theme->pivot->status === 'inactive';
            if ($filter === 'update-available') return ! empty($theme->pivot->update_version);

            return true;
        });

        if ($filtered->isEmpty()) {
            return "No themes found matching filter: {$filter}";
        }

        $lines = ['| Theme | Version | Status | Update Available | Vulnerable |', '|---|---|---|---|---|'];
        foreach ($filtered as $theme) {
            $p = $theme->pivot;
            $updateCol = $p->update_version ? "→ {$p->update_version}" : 'up to date';
            $vulnCol = $p->is_vulnerable ? '⚠️ YES' : 'no';
            $lines[] = "| {$theme->name} | {$p->version} | {$p->status} | {$updateCol} | {$vulnCol} |";
        }

        return implode("\n", $lines);
    }
}
