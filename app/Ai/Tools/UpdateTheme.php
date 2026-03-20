<?php

namespace App\Ai\Tools;

use App\Ai\SshSessionManager;
use App\Models\Site;
use App\Services\SSHSiteConnect;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class UpdateTheme implements Tool
{
    public function __construct(private Site $site, private ?SshSessionManager $session = null) {}

    public function description(): Stringable|string
    {
        return 'Update a specific WordPress theme to the latest version via SSH. Only use this after confirming with the user.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'theme_slug' => $schema->string()->required()->description('The theme slug to update'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $cmd = 'wp theme update ' . escapeshellarg($request['theme_slug']);

        if ($this->session) {
            $result = $this->session->execInSiteDir($cmd);

            if ($result['success']) {
                return "Successfully updated theme '{$request['theme_slug']}'. Output:\n" . ($result['output'] ?: '(no output)');
            }

            return "Failed to update theme '{$request['theme_slug']}'. Output:\n" . ($result['output'] ?: ($result['error'] ?? '(no output)'));
        }

        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            return 'Error: Cannot establish SSH connection to this site.';
        }

        $output = $connection->exec('cd ' . $this->site->dir_path . ' && ' . $cmd);
        $success = $connection->getExitStatusBool();
        $connection->close();

        if ($success) {
            return "Successfully updated theme '{$request['theme_slug']}'. Output:\n" . ($output ?: '(no output)');
        }

        return "Failed to update theme '{$request['theme_slug']}'. Output:\n" . ($output ?: '(no output)');
    }
}
