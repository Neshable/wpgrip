<?php

namespace App\Services;

use App\Models\Site;
use App\Models\ShelleyInstance;
use Illuminate\Support\Facades\Log;

class ShelleyManager
{
    const SHELLEY_BIN    = '/usr/local/bin/shelley-bin';
    const BASE_DIR       = '/var/shelley/sites';
    const BASE_PORT      = 9200;
    const MAX_INSTANCES  = 99;   // ports 9200–9298
    const IDLE_MINUTES   = 30;
    const STARTUP_WAIT   = 3;    // seconds to wait for Shelley to bind

    /**
     * Ensure a Shelley instance is running for the given site.
     * Returns the port it is listening on.
     */
    public function ensureRunning(Site $site): int
    {
        $instance = ShelleyInstance::where('site_id', $site->id)->first();

        if ($instance) {
            if ($this->isProcessRunning($instance->pid)) {
                $instance->update(['last_accessed_at' => now()]);
                return $instance->port;
            }
            // Stale record — process died
            $instance->delete();
        }

        return $this->start($site);
    }

    /**
     * Start a new Shelley instance for the site.
     */
    private function start(Site $site): int
    {
        $port    = $this->allocatePort();
        $workdir = $this->setupWorkdir($site);
        $logfile = "$workdir/shelley.log";
        $dbPath  = "$workdir/shelley.db";
        $apiKey  = $this->readEnvKey('ANTHROPIC_API_KEY');

        // Write a launcher script to avoid env var escaping issues with shell_exec
        $launcherPath    = "$workdir/start.sh";
        $launcherContent = "#!/bin/bash\n";
        $launcherContent .= "export ANTHROPIC_API_KEY=" . escapeshellarg($apiKey) . "\n";
        $launcherContent .= "export HOME=" . escapeshellarg($workdir) . "\n";
        $launcherContent .= escapeshellarg(self::SHELLEY_BIN);
        $launcherContent .= " -db " . escapeshellarg($dbPath);
        $launcherContent .= " serve -port $port -socket none";
        $launcherContent .= " >> " . escapeshellarg($logfile) . " 2>&1\n";
        file_put_contents($launcherPath, $launcherContent);
        chmod($launcherPath, 0750);

        // Launch in background, capture PID
        $cmd = "bash " . escapeshellarg($launcherPath) . " > /dev/null 2>&1 & echo $!";
        $pid = (int) shell_exec($cmd);

        if ($pid <= 0) {
            throw new \RuntimeException("Failed to start Shelley for site {$site->id}");
        }

        // Save DB record immediately -- don't block waiting for port.
        // The iframe will show a loading state until Shelley is ready (~2-3s).
        ShelleyInstance::create([
            'site_id'          => $site->id,
            'port'             => $port,
            'pid'              => $pid,
            'workdir'          => $workdir,
            'last_accessed_at' => now(),
        ]);

        Log::info("Shelley started for site {$site->id} on port {$port} (PID {$pid})");

        return $port;
    }

    /**
     * Build (or rebuild) the working directory for a site.
     */
    private function setupWorkdir(Site $site): string
    {
        $workdir = self::BASE_DIR . '/' . $site->id;

        @mkdir($workdir,        0755, true);
        @mkdir("$workdir/.ssh", 0700, true);

        $server = $site->server;
        $tenant = $site->tenant ?? $this->getTenant($site);

        // SSH private key
        if ($tenant && $tenant->ssh_private) {
            file_put_contents("$workdir/.ssh/id_rsa", $tenant->ssh_private);
            chmod("$workdir/.ssh/id_rsa", 0600);
        }

        // SSH client config
        if ($server) {
            $sshPort = $server->ssh_port ?? 22;
            $sshUser = $site->ssh_user ?: 'root';

            $config  = "Host site\n";
            $config .= "  HostName {$server->ip}\n";
            $config .= "  User {$sshUser}\n";
            $config .= "  Port {$sshPort}\n";
            $config .= "  IdentityFile $workdir/.ssh/id_rsa\n";
            $config .= "  StrictHostKeyChecking no\n";
            $config .= "  UserKnownHostsFile /dev/null\n";

            file_put_contents("$workdir/.ssh/config", $config);
            chmod("$workdir/.ssh/config", 0600);
        }

        $this->writeDearLlm($site, $workdir, $server, $tenant);

        // Fix ownership so Shelley (running as www-data) can write
        shell_exec("chown -R www-data:www-data " . escapeshellarg($workdir));

        return $workdir;
    }

    /**
     * Write the dear_llm.md guidance file that scopes Shelley to this site.
     */
    private function writeDearLlm(Site $site, string $workdir, $server, $tenant): void
    {
        $hasSsh = $server && $tenant?->ssh_private;

        $md  = "# WPGrip Site Agent\n\n";
        $md .= "You are an AI assistant embedded in **WPGrip**, a WordPress site management platform.\n";
        $md .= "You are scoped exclusively to the following WordPress site. Be concise and helpful.\n\n";

        $md .= "## Site Information\n";
        $md .= "- **Name:** {$site->name}\n";
        $md .= "- **URL:** {$site->url}\n";
        $md .= "- **Document Root:** " . ($site->dir_path ?: 'unknown') . "\n";
        $md .= "- **PHP Version:** " . ($site->php_ver ?: 'unknown') . "\n";
        if ($site->error_log_path) {
            $md .= "- **Error Log:** {$site->error_log_path}\n";
        }

        if ($server) {
            $md .= "\n## Server Information\n";
            $md .= "- **IP:** {$server->ip}\n";
            $md .= "- **SSH User:** " . ($site->ssh_user ?: 'root') . "\n";
            $md .= "- **SSH Port:** " . ($server->ssh_port ?? 22) . "\n";
        }

        if ($hasSsh) {
            $md .= "\n## SSH Access\n";
            $md .= "SSH is fully configured. You can connect with simply:\n";
            $md .= "```\nssh site\n```\n";
            $md .= "The SSH config and key are in `{$workdir}/.ssh/`. HOME is set to `{$workdir}` so SSH picks them up automatically.\n\n";
            $md .= "### Useful read-only commands\n";
            $md .= "```bash\n";
            $md .= "# List site files\nssh site ls -la " . ($site->dir_path ?: '/var/www/html') . "\n\n";
            $md .= "# WP-CLI (read-only examples)\n";
            $md .= "ssh site wp --path=" . ($site->dir_path ?: '/var/www/html') . " plugin list\n";
            $md .= "ssh site wp --path=" . ($site->dir_path ?: '/var/www/html') . " core version\n";
            $md .= "ssh site wp --path=" . ($site->dir_path ?: '/var/www/html') . " user list\n\n";
            if ($site->error_log_path) {
                $md .= "# Read error log\nssh site tail -n 200 {$site->error_log_path}\n";
            }
            $md .= "```\n";
        } else {
            $md .= "\n## SSH Access\n";
            $md .= "SSH is **not configured** for this site (no SSH key or server details available).\n";
            $md .= "You can still answer questions about the site based on the information above.\n";
        }

        $md .= "\n## Your Role & Restrictions\n";
        $md .= "- **READ-ONLY**: You must not modify files, run updates, install/remove plugins, change settings, or make any changes to the remote server.\n";
        $md .= "- Focus on diagnostics, log analysis, plugin/theme inspection, and answering questions about this site.\n";
        $md .= "- If asked to do something destructive or write-enabled, politely decline and explain that you are in read-only mode.\n";

        file_put_contents("$workdir/dear_llm.md", $md);
    }

    /**
     * Kill all Shelley instances that haven't been accessed in IDLE_MINUTES.
     */
    public function killIdle(): void
    {
        $cutoff = now()->subMinutes(self::IDLE_MINUTES);

        ShelleyInstance::where('last_accessed_at', '<', $cutoff)
            ->get()
            ->each(function (ShelleyInstance $instance) {
                $this->kill($instance);
            });
    }

    public function kill(ShelleyInstance $instance): void
    {
        if ($this->isProcessRunning($instance->pid)) {
            posix_kill($instance->pid, SIGTERM);
        }
        $instance->delete();
        Log::info("Shelley killed for site {$instance->site_id} (PID {$instance->pid})");
    }

    private function allocatePort(): int
    {
        $used = ShelleyInstance::pluck('port')->toArray();

        for ($i = 0; $i < self::MAX_INSTANCES; $i++) {
            $port = self::BASE_PORT + $i;
            if (!in_array($port, $used) && !$this->isPortInUse($port)) {
                return $port;
            }
        }

        throw new \RuntimeException('No available Shelley ports (9200–9298 exhausted)');
    }

    private function isProcessRunning(int $pid): bool
    {
        if ($pid <= 0) return false;
        return posix_kill($pid, 0) === true;
    }

    private function isPortInUse(int $port): bool
    {
        $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.1);
        if ($fp) {
            fclose($fp);
            return true;
        }
        return false;
    }

    private function readEnvKey(string $key): string
    {
        $path = base_path('.env');
        if (!file_exists($path)) return '';
        foreach (file($path) as $line) {
            $line = trim($line);
            if (str_starts_with($line, $key . '=')) {
                return trim(substr($line, strlen($key) + 1), " \t\n\r\"' ");
            }
        }
        return '';
    }

    private function getTenant(Site $site)
    {
        // Site belongs to a tenant via tenant_id
        return \App\Models\Tenant::find($site->tenant_id);
    }
}
