<?php

namespace App\Ai;

use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

/**
 * Manages a persistent SSH session for the AI agent.
 *
 * One instance is created per agent streaming request and shared across
 * all tool invocations so they reuse the same connection.
 */
class SshSessionManager
{
    private ?SSH2 $connection = null;

    private bool $connected = false;

    private ?string $lastError = null;

    private int $commandCount = 0;

    public function __construct(
        private Site $site,
    ) {}

    /**
     * Get or establish the SSH connection.
     */
    public function connection(): ?SSH2
    {
        if ($this->connected && $this->connection?->isConnected()) {
            return $this->connection;
        }

        // Reset state for (re)connect
        $this->connected = false;
        $this->lastError = null;

        try {
            $server = $this->site->server;
            if (! $server) {
                $this->lastError = 'No server associated with this site.';
                return null;
            }

            $ip = $server->ip;
            $port = $server->port ?? 22;
            $sshUser = $this->site->ssh_user;

            if (empty($ip) || empty($sshUser)) {
                $this->lastError = 'Missing SSH credentials (IP or username).';
                return null;
            }

            // Resolve tenant SSH key
            $tenant = Tenant::find($this->site->tenant_id);
            if (! $tenant || empty($tenant->ssh_private)) {
                $this->lastError = 'No SSH key configured for this workspace.';
                return null;
            }

            $privateKey = Crypt::decryptString($tenant->ssh_private);
            $passphrase = $tenant->uuid;

            $ssh = new SSH2($ip, $port);
            $ssh->setTimeout(30);

            $key = PublicKeyLoader::load($privateKey, $passphrase);

            if (! $ssh->login($sshUser, $key)) {
                $this->lastError = "SSH login failed for {$sshUser}@{$ip}:{$port}.";
                return null;
            }

            $ssh->enableQuietMode();
            $ssh->setTimeout(60);

            $this->connection = $ssh;
            $this->connected = true;

            Log::info('Agent SSH session opened', [
                'site_id' => $this->site->id,
                'host' => "{$sshUser}@{$ip}:{$port}",
            ]);

            return $this->connection;

        } catch (\Throwable $e) {
            $this->lastError = 'SSH connection failed: ' . $e->getMessage();
            Log::warning('Agent SSH session failed', [
                'site_id' => $this->site->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Execute a command on the persistent session.
     */
    public function exec(string $command): array
    {
        $ssh = $this->connection();
        if (! $ssh) {
            return [
                'success' => false,
                'output' => '',
                'error' => $this->lastError ?? 'No SSH connection available.',
            ];
        }

        try {
            $output = $ssh->exec($command);
            $exitCode = $ssh->getExitStatus();
            $this->commandCount++;

            return [
                'success' => $exitCode === 0,
                'output' => $output ?? '',
                'exit_code' => $exitCode,
                'error' => $exitCode !== 0 ? "Command exited with code {$exitCode}" : null,
            ];
        } catch (\Throwable $e) {
            // Connection may have dropped — mark as disconnected so next call reconnects
            $this->connected = false;

            return [
                'success' => false,
                'output' => '',
                'error' => 'SSH exec failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute a command inside the site's document root.
     */
    public function execInSiteDir(string $command): array
    {
        $dir = $this->site->dir_path;
        if (empty($dir)) {
            return [
                'success' => false,
                'output' => '',
                'error' => 'Site directory path is not configured.',
            ];
        }

        return $this->exec('cd ' . escapeshellarg($dir) . ' && ' . $command);
    }

    /**
     * Whether the session is currently connected.
     */
    public function isConnected(): bool
    {
        return $this->connected && $this->connection?->isConnected();
    }

    /**
     * Get the last error message.
     */
    public function lastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * How many commands have been executed in this session.
     */
    public function commandCount(): int
    {
        return $this->commandCount;
    }

    /**
     * Get connection info for display.
     */
    public function connectionInfo(): array
    {
        $server = $this->site->server;

        return [
            'user' => $this->site->ssh_user ?? 'unknown',
            'host' => $server?->ip ?? 'unknown',
            'port' => $server?->port ?? 22,
            'site_dir' => $this->site->dir_path ?? 'unknown',
            'connected' => $this->isConnected(),
            'commands_run' => $this->commandCount,
        ];
    }

    /**
     * Close the SSH connection gracefully.
     */
    public function disconnect(): void
    {
        if ($this->connection) {
            try {
                $this->connection->disconnect();
            } catch (\Throwable $e) {
                // Ignore disconnect errors
            }

            Log::info('Agent SSH session closed', [
                'site_id' => $this->site->id,
                'commands_run' => $this->commandCount,
            ]);
        }

        $this->connection = null;
        $this->connected = false;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
