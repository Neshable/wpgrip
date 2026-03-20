<?php

namespace App\Ai\Tools;

use App\Ai\SshSessionManager;
use App\Models\Site;
use App\Services\SSHSiteConnect;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class RunWpCliCommand implements Tool
{
    /**
     * Allowlisted WP-CLI commands (prefixes) that are safe to run.
     */
    private const ALLOWED_COMMANDS = [
        'wp plugin list',
        'wp theme list',
        'wp user list',
        'wp option get',
        'wp option list',
        'wp config get',
        'wp config list',
        'wp cron event list',
        'wp cron schedule list',
        'wp post list',
        'wp post-type list',
        'wp taxonomy list',
        'wp widget list',
        'wp sidebar list',
        'wp menu list',
        'wp rewrite list',
        'wp role list',
        'wp cap list',
        'wp db size',
        'wp db tables',
        'wp db check',
        'wp core version',
        'wp core check-update',
        'wp site url',
        'wp eval',
        'wp transient list',
        'wp transient delete',
        'wp cache flush',
        'wp rewrite flush',
        'wp media regenerate',
    ];

    /**
     * Explicitly blocked dangerous commands.
     */
    private const BLOCKED_PATTERNS = [
        'wp db reset',
        'wp db drop',
        'wp db import',
        'wp db export',
        'wp site delete',
        'wp plugin install',
        'wp theme install',
        'wp core download',
        'wp core update',
        'wp eval-file',
        'wp shell',
        'rm ',
        'sudo ',
        'chmod ',
        'chown ',
        'mv ',
        'cp ',
        '&&',
        '||',
        '|',
        ';',
        '>',
        '<',
        '`',
    ];

    public function __construct(private Site $site, private ?SshSessionManager $session = null) {}

    public function description(): Stringable|string
    {
        return 'Run a safe, read-only WP-CLI command on the site. Only allowlisted commands are permitted. Use this for detailed inspection that other tools don\'t cover.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'command' => $schema->string()->required()->description('The WP-CLI command to run, e.g. "wp user list --format=json"'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $cmd = trim($request['command']);

        // Security: check blocked patterns
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (str_contains($cmd, $pattern)) {
                return "Error: Command blocked for security reasons. The command contains a disallowed pattern: '{$pattern}'.";
            }
        }

        // Security: check allowlist
        $allowed = false;
        foreach (self::ALLOWED_COMMANDS as $prefix) {
            if (str_starts_with($cmd, $prefix)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return "Error: Command not in the allowlist. Allowed command prefixes: " . implode(', ', array_slice(self::ALLOWED_COMMANDS, 0, 10)) . '...';
        }

        if ($this->session) {
            $result = $this->session->execInSiteDir($cmd);

            if ($result['success']) {
                $output = $result['output'];
                $truncated = strlen($output) > 8000 ? substr($output, 0, 8000) . "\n\n... (output truncated)" : $output;
                return "Command executed successfully.\nOutput:\n" . ($truncated ?: '(no output)');
            }

            return "Command failed.\nOutput:\n" . ($result['output'] ?: ($result['error'] ?? '(no output)'));
        }

        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            return 'Error: Cannot establish SSH connection to this site.';
        }

        $output = $connection->exec('cd ' . $this->site->dir_path . ' && ' . $cmd);
        $success = $connection->getExitStatusBool();
        $connection->close();

        if ($success) {
            $truncated = strlen($output) > 8000 ? substr($output, 0, 8000) . "\n\n... (output truncated)" : $output;
            return "Command executed successfully.\nOutput:\n" . ($truncated ?: '(no output)');
        }

        return "Command failed.\nOutput:\n" . ($output ?: '(no output)');
    }
}
