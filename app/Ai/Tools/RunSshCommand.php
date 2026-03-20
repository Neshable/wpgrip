<?php

namespace App\Ai\Tools;

use App\Ai\SshSessionManager;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * General-purpose SSH command tool for the AI agent.
 *
 * Uses a persistent SshSessionManager so all commands within one
 * agent conversation share a single SSH connection.
 *
 * Blocks destructive patterns but otherwise allows broad read/diagnostic
 * commands, WP-CLI, system inspection, log tailing, etc.
 */
class RunSshCommand implements Tool
{
    /**
     * Hard-blocked patterns that must never be executed.
     * Prevents data loss, privilege escalation, and shell escapes.
     */
    private const BLOCKED_PATTERNS = [
        // Destructive file operations
        'rm -rf /',
        'rm -rf /*',
        'rm -rf ~',
        'rm -rf .',
        'mkfs',
        'dd if=',
        ':(){',           // fork bomb
        'chmod -R 777 /', // recursive root chmod
        'chown -R',       // recursive ownership change

        // Privilege escalation
        'sudo ',
        'su ',
        'visudo',
        'passwd',

        // Package management (can break the server)
        'apt ',
        'apt-get ',
        'yum ',
        'dnf ',
        'pacman ',
        'snap ',

        // Service management
        'systemctl ',
        'service ',
        'init ',
        'shutdown',
        'reboot',
        'halt',
        'poweroff',

        // Dangerous WP-CLI
        'wp db drop',
        'wp db reset',
        'wp site delete',
        'wp eval-file',
        'wp shell',
        'wp core download',

        // Network / data exfiltration
        'curl ',
        'wget ',
        'nc ',
        'ncat ',
        'netcat ',
        'scp ',
        'rsync ',
        'sftp ',
        'ssh ',
        'ftp ',

        // Editors / interactive
        'vi ',
        'vim ',
        'nano ',
        'emacs ',
        'less ',
        'more ',

        // Crypto mining / process manipulation
        'nohup ',
        'screen ',
        'tmux ',
        'crontab ',
        'at ',

        // Python/perl/ruby execution
        'python ',
        'python3 ',
        'perl ',
        'ruby ',
        'node ',
        'php -r',
    ];

    /**
     * Shell metacharacters that could chain or redirect commands dangerously.
     * We allow pipes (|) for grep/awk/sort but block the truly dangerous ones.
     */
    private const BLOCKED_OPERATORS = [
        '&&',   // command chaining
        '||',   // conditional chaining
        ';',    // command separator
        '`',    // backtick subshell
        '$(',   // subshell
        '>>',   // append redirect (could modify files)
        '> ',   // output redirect (could overwrite files) — note trailing space
    ];

    /**
     * Allowed redirect: single > to /dev/null (common pattern for silencing output).
     * This is checked as an exception after blocking '> '.
     */

    public function __construct(
        private SshSessionManager $session,
    ) {}

    public function description(): Stringable|string
    {
        return <<<'DESC'
Execute a shell command on the site's server via SSH. The session persists across
multiple tool calls so you can inspect state built up by previous commands.

You can run:
- WP-CLI commands (wp plugin list, wp option get, wp search-replace --dry-run, etc.)
- File inspection (ls, cat, head, tail, find, du, stat, file, wc)
- Log inspection (tail -n 100 /path/to/error.log)
- System info (df -h, free -m, uptime, uname -a, whoami, pwd, env)
- Process info (ps aux, top -bn1)
- Network diagnostics (ping -c3, dig, host)
- Database (wp db query "SELECT ..." for read-only queries, wp db size, wp db tables)
- Grep/search (grep -r "pattern" path)

Blocked: destructive operations (rm -rf, sudo, service management, package installs, editors, downloads).
Use pipes (|) freely for grep, awk, sort, head, tail, wc, etc.
DESC;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'command' => $schema->string()
                ->required()
                ->description('The shell command to execute. Runs in the site\'s document root by default.'),
            'working_directory' => $schema->string()
                ->description('Optional: run the command in this directory instead of the site root. Use absolute paths.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $cmd = trim($request['command'] ?? '');
        if (empty($cmd)) {
            return 'Error: No command provided.';
        }

        // Security: check blocked patterns (case-insensitive)
        $cmdLower = mb_strtolower($cmd);
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (str_contains($cmdLower, mb_strtolower($pattern))) {
                return "⛔ Blocked: command contains disallowed pattern '{$pattern}'. "
                     . 'This restriction exists to prevent accidental damage to the server.';
            }
        }

        // Security: check blocked operators
        foreach (self::BLOCKED_OPERATORS as $op) {
            if (str_contains($cmd, $op)) {
                // Allow "> /dev/null"
                if ($op === '> ' && preg_match('#>\s*/dev/null#', $cmd)) {
                    continue;
                }
                return "⛔ Blocked: command contains disallowed operator '{$op}'. "
                     . 'Use a single command at a time. Pipes (|) are allowed for filtering output.';
            }
        }

        // Execute via the shared session
        $workDir = trim($request['working_directory'] ?? '');

        if (! empty($workDir)) {
            $result = $this->session->exec('cd ' . escapeshellarg($workDir) . ' && ' . $cmd);
        } else {
            $result = $this->session->execInSiteDir($cmd);
        }

        if (! empty($result['error']) && ! $result['success']) {
            // Connection-level error
            if (str_contains($result['error'], 'SSH')) {
                return '❌ SSH Error: ' . $result['error'];
            }
        }

        $output = $result['output'] ?? '';
        $exitCode = $result['exit_code'] ?? -1;

        // Truncate very long output
        $maxLen = 12000;
        $truncated = false;
        if (strlen($output) > $maxLen) {
            $output = substr($output, 0, $maxLen);
            $truncated = true;
        }

        $status = $result['success'] ? '✅ Success' : "⚠️ Exit code {$exitCode}";
        $header = "{$status} | Command: {$cmd}";

        if ($truncated) {
            $header .= ' | ⚠️ Output truncated (showing first 12KB)';
        }

        if (empty(trim($output))) {
            return $header . "\n(no output)";
        }

        return $header . "\n" . $output;
    }
}
