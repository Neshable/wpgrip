<?php

namespace App\Ai\Tools;

use App\Models\Site;
use App\Services\SSHSiteConnect;
use App\Services\WPCliService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class TogglePlugin implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Activate or deactivate a WordPress plugin on the site via SSH. Use the plugin slug (e.g., "akismet", "wordfence").';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'plugin_slug' => $schema->string()->required()->description('The plugin slug, e.g. "akismet"'),
            'action' => $schema->string()->enum(['activate', 'deactivate'])->required()->description('Whether to activate or deactivate the plugin'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            return 'Error: Cannot establish SSH connection to this site. The user should check the site SSH settings.';
        }

        $cmd = $request['action'] === 'activate'
            ? WPCliService::activatePlugin($request['plugin_slug'])
            : WPCliService::deactivatePlugin($request['plugin_slug']);

        $output = $connection->exec('cd ' . $this->site->dir_path . ' && ' . $cmd);
        $success = $connection->getExitStatusBool();
        $connection->close();

        if ($success) {
            return "Successfully {$request['action']}d plugin '{$request['plugin_slug']}'. Output: " . ($output ?: '(no output)');
        }

        return "Failed to {$request['action']} plugin '{$request['plugin_slug']}'. Output: " . ($output ?: '(no output)');
    }
}
