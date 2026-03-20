<?php

namespace App\Ai\Tools;

use App\Models\Site;
use App\Services\SSHSiteConnect;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class UpdatePlugin implements Tool
{
    public function __construct(private Site $site) {}

    public function description(): Stringable|string
    {
        return 'Update a specific WordPress plugin to the latest version via SSH. Only use this after confirming with the user.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'plugin_slug' => $schema->string()->required()->description('The plugin slug to update, e.g. "akismet"'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            return 'Error: Cannot establish SSH connection to this site.';
        }

        $cmd = 'wp plugin update ' . escapeshellarg($request['plugin_slug']);
        $output = $connection->exec('cd ' . $this->site->dir_path . ' && ' . $cmd);
        $success = $connection->getExitStatusBool();
        $connection->close();

        if ($success) {
            return "Successfully updated plugin '{$request['plugin_slug']}'. Output:\n" . ($output ?: '(no output)');
        }

        return "Failed to update plugin '{$request['plugin_slug']}'. Output:\n" . ($output ?: '(no output)');
    }
}
