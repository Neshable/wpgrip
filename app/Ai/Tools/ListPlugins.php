<?php

namespace App\Ai\Tools;

use App\Models\Site;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListPlugins implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'List all WordPress plugins installed on the site with their version, status (active/inactive), available updates, and vulnerability information.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status_filter' => $schema->string()->enum(['all', 'active', 'inactive', 'update-available'])->default('all'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $this->site->loadMissing('plugins');
        $plugins = $this->site->plugins;

        if ($plugins->isEmpty()) {
            return 'No plugins found for this site. The site may need to be synced.';
        }

        $filter = $request['status_filter'] ?? 'all';

        $filtered = $plugins->filter(function ($plugin) use ($filter) {
            if ($filter === 'all') return true;
            if ($filter === 'active') return $plugin->pivot->status === 'active';
            if ($filter === 'inactive') return $plugin->pivot->status === 'inactive';
            if ($filter === 'update-available') return ! empty($plugin->pivot->update_version);

            return true;
        });

        if ($filtered->isEmpty()) {
            return "No plugins found matching filter: {$filter}";
        }

        $lines = ['| Plugin | Version | Status | Update Available | Vulnerable |', '|---|---|---|---|---|'];
        foreach ($filtered as $plugin) {
            $p = $plugin->pivot;
            $updateCol = $p->update_version ? "→ {$p->update_version}" : 'up to date';
            $vulnCol = $p->is_vulnerable ? '⚠️ YES' : 'no';
            $lines[] = "| {$plugin->name} | {$p->version} | {$p->status} | {$updateCol} | {$vulnCol} |";
        }

        return implode("\n", $lines);
    }
}
