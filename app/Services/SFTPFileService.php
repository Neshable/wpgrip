<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Tenant;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use phpseclib3\Exception\UnableToConnectException;

class SFTPFileService
{
    /**
     * Maximum file size allowed for reading (2 MB).
     */
    private const MAX_READ_SIZE = 2 * 1024 * 1024;

    /**
     * Cached SFTP connections keyed by site ID.
     *
     * @var array<int, static>
     */
    private static array $connections = [];

    /**
     * The Site model instance.
     */
    protected Site $site;

    /**
     * The underlying SFTP connection.
     */
    protected SFTP $sftp;

    /**
     * Whether the connection is active.
     */
    protected bool $active = false;

    /**
     * SSH username.
     */
    protected string $sshUser;

    /**
     * Server IP address.
     */
    protected string $ip;

    /**
     * SSH port.
     */
    protected int $port;

    /**
     * Decrypted SSH private key.
     */
    protected string $sshPrivate;

    /**
     * Key passphrase.
     */
    protected string $password = '';

    /**
     * Private constructor — use the static make() factory.
     */
    private function __construct(Site $site)
    {
        $this->site = $site;
        $this->loadCredentials();
        $this->connect();
    }

    /**
     * Create or retrieve a cached SFTPFileService instance for the given site.
     */
    public static function make(Site $site): static
    {
        $id = $site->id;

        if (isset(static::$connections[$id]) && static::$connections[$id]->isConnected()) {
            return static::$connections[$id];
        }

        $instance = new static($site);
        static::$connections[$id] = $instance;

        return $instance;
    }

    // -------------------------------------------------------------------------
    //  Connection management
    // -------------------------------------------------------------------------

    /**
     * Load SSH credentials from the Site and its related models.
     */
    protected function loadCredentials(): void
    {
        if ($this->site->server) {
            $this->ip = $this->site->server->ip;
            $this->sshUser = $this->site->ssh_user;
            $this->port = $this->site->server->port ?? 22;
        }

        $tenant = Tenant::find($this->site->tenant_id);

        if ($tenant) {
            try {
                $this->sshPrivate = Crypt::decryptString($tenant->ssh_private);
                $this->password = $tenant->uuid;
            } catch (DecryptException $e) {
                throw new \RuntimeException('Failed to decrypt SSH private key: ' . $e->getMessage(), 0, $e);
            }
        } else {
            throw new \RuntimeException('No tenant found for site ID ' . $this->site->id);
        }
    }

    /**
     * Establish the SFTP connection.
     */
    protected function connect(): void
    {
        $this->sftp = new SFTP($this->ip, $this->port, 10);

        $key = PublicKeyLoader::load($this->sshPrivate, $this->password);

        try {
            if (!$this->sftp->login($this->sshUser, $key)) {
                throw new \RuntimeException('SFTP login failed for ' . $this->sshUser . '@' . $this->ip);
            }

            $this->active = true;
        } catch (UnableToConnectException $e) {
            $this->active = false;
            report($e);
            throw new \RuntimeException('Unable to connect to SFTP server: ' . $e->getMessage(), 0, $e);
        } catch (\phpseclib3\Exception\ConnectionClosedException|\UnexpectedValueException $e) {
            $this->active = false;
            report($e);
            throw new \RuntimeException('SFTP connection error: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            $this->active = false;
            report($e->getMessage());
            throw new \RuntimeException('SFTP connection error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Check whether the SFTP connection is still alive.
     */
    public function isConnected(): bool
    {
        return $this->active && isset($this->sftp) && $this->sftp->isConnected();
    }

    /**
     * Disconnect the current SFTP session and remove from cache.
     */
    public function disconnect(): void
    {
        if (isset($this->sftp) && $this->sftp->isConnected()) {
            $this->sftp->disconnect();
        }

        $this->active = false;
        unset(static::$connections[$this->site->id]);
    }

    /**
     * Disconnect and reconnect.
     */
    public function reconnect(): static
    {
        $this->disconnect();
        $this->connect();
        static::$connections[$this->site->id] = $this;

        return $this;
    }

    // -------------------------------------------------------------------------
    //  Path security
    // -------------------------------------------------------------------------

    /**
     * Normalize and validate that the given path resides under the site's dir_path.
     *
     * Resolves `.` and `..` segments without relying on the remote filesystem,
     * then asserts the resulting absolute path starts with the site's root.
     *
     * @throws \InvalidArgumentException When the path escapes the allowed root.
     */
    public function normalizePath(string $path): string
    {
        $root = rtrim($this->site->dir_path, '/');

        // If the path is relative, anchor it to the site root.
        if (!str_starts_with($path, '/')) {
            $path = $root . '/' . $path;
        }

        // Resolve the path segments.
        $resolved = $this->resolvePath($path);

        // Ensure the resolved path is within the allowed root.
        if ($resolved !== $root && !str_starts_with($resolved, $root . '/')) {
            throw new \InvalidArgumentException(
                'Path traversal detected: the resolved path "' . $resolved . '" is outside the allowed root "' . $root . '".'
            );
        }

        return $resolved;
    }

    /**
     * Resolve `.` and `..` segments in an absolute path.
     */
    private function resolvePath(string $path): string
    {
        $parts = explode('/', $path);
        $resolved = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($resolved);
            } else {
                $resolved[] = $part;
            }
        }

        return '/' . implode('/', $resolved);
    }

    // -------------------------------------------------------------------------
    //  Directory operations
    // -------------------------------------------------------------------------

    /**
     * List the contents of a directory.
     *
     * @return array<int, array{name: string, path: string, type: string, size: int, permissions: string, modified: int}>
     */
    public function listDirectory(string $path): array
    {
        $path = $this->normalizePath($path);

        $listing = $this->sftp->rawlist($path);

        if ($listing === false) {
            throw new \RuntimeException('Failed to list directory: ' . $path);
        }

        $items = [];

        foreach ($listing as $name => $attrs) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $type = $this->resolveType($attrs);

            $items[] = [
                'name'        => $name,
                'path'        => rtrim($path, '/') . '/' . $name,
                'type'        => $type,
                'size'        => (int) ($attrs['size'] ?? 0),
                'permissions' => $this->formatPermissions($attrs['permissions'] ?? 0),
                'modified'    => (int) ($attrs['mtime'] ?? 0),
            ];
        }

        // Sort: directories first, then files, both alphabetically.
        usort($items, function (array $a, array $b): int {
            if ($a['type'] === 'dir' && $b['type'] !== 'dir') {
                return -1;
            }

            if ($a['type'] !== 'dir' && $b['type'] === 'dir') {
                return 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return $items;
    }

    /**
     * Create a directory.
     */
    public function createDirectory(string $path): bool
    {
        $path = $this->normalizePath($path);

        $result = $this->sftp->mkdir($path);

        if ($result === false) {
            throw new \RuntimeException('Failed to create directory: ' . $path);
        }

        return true;
    }

    /**
     * Delete an empty directory.
     */
    public function deleteDirectory(string $path): bool
    {
        $path = $this->normalizePath($path);

        $result = $this->sftp->rmdir($path);

        if ($result === false) {
            throw new \RuntimeException('Failed to delete directory (it may not be empty): ' . $path);
        }

        return true;
    }

    // -------------------------------------------------------------------------
    //  File operations
    // -------------------------------------------------------------------------

    /**
     * Read a file's content.
     *
     * @return array{content: string, size: int, writable: bool}
     */
    public function readFile(string $path): array
    {
        $path = $this->normalizePath($path);

        $stat = $this->sftp->stat($path);

        if ($stat === false) {
            throw new \RuntimeException('File not found: ' . $path);
        }

        $size = (int) ($stat['size'] ?? 0);

        if ($size > self::MAX_READ_SIZE) {
            throw new \RuntimeException(
                'File exceeds the maximum readable size of ' . (self::MAX_READ_SIZE / 1024 / 1024) . ' MB.'
            );
        }

        $content = $this->sftp->get($path);

        if ($content === false) {
            throw new \RuntimeException('Failed to read file: ' . $path);
        }

        // Check write permission via the permission bits.
        $permissions = $stat['permissions'] ?? 0;
        $writable = (bool) ($permissions & 0x0080); // owner write bit

        return [
            'content'  => $content,
            'size'     => $size,
            'writable' => $writable,
        ];
    }

    /**
     * Write content to a file.
     */
    public function writeFile(string $path, string $content): bool
    {
        $path = $this->normalizePath($path);

        $result = $this->sftp->put($path, $content);

        if ($result === false) {
            throw new \RuntimeException('Failed to write file: ' . $path);
        }

        return true;
    }

    /**
     * Create an empty file.
     */
    public function createFile(string $path): bool
    {
        $path = $this->normalizePath($path);

        $result = $this->sftp->put($path, '');

        if ($result === false) {
            throw new \RuntimeException('Failed to create file: ' . $path);
        }

        return true;
    }

    /**
     * Delete a file.
     */
    public function deleteFile(string $path): bool
    {
        $path = $this->normalizePath($path);

        $result = $this->sftp->delete($path, false);

        if ($result === false) {
            throw new \RuntimeException('Failed to delete file: ' . $path);
        }

        return true;
    }

    // -------------------------------------------------------------------------
    //  General operations
    // -------------------------------------------------------------------------

    /**
     * Rename or move a file/directory.
     */
    public function rename(string $from, string $to): bool
    {
        $from = $this->normalizePath($from);
        $to   = $this->normalizePath($to);

        $result = $this->sftp->rename($from, $to);

        if ($result === false) {
            throw new \RuntimeException('Failed to rename "' . $from . '" to "' . $to . '".');
        }

        return true;
    }

    /**
     * Get stat info for a file or directory.
     *
     * @return array{name: string, path: string, type: string, size: int, permissions: string, modified: int, accessed: int}
     */
    public function getFileInfo(string $path): array
    {
        $path = $this->normalizePath($path);

        $stat = $this->sftp->stat($path);

        if ($stat === false) {
            throw new \RuntimeException('Failed to stat: ' . $path);
        }

        $type = $this->resolveTypeFromMode($stat['permissions'] ?? 0);

        return [
            'name'        => basename($path),
            'path'        => $path,
            'type'        => $type,
            'size'        => (int) ($stat['size'] ?? 0),
            'permissions' => $this->formatPermissions($stat['permissions'] ?? 0),
            'modified'    => (int) ($stat['mtime'] ?? 0),
            'accessed'    => (int) ($stat['atime'] ?? 0),
        ];
    }

    // -------------------------------------------------------------------------
    //  Helpers
    // -------------------------------------------------------------------------

    /**
     * Determine the type from a rawlist attributes array.
     */
    private function resolveType(array $attrs): string
    {
        // phpseclib rawlist includes a 'type' key in some versions.
        $type = $attrs['type'] ?? null;

        if ($type === NET_SFTP_TYPE_DIRECTORY || ($type === null && isset($attrs['permissions']) && ($attrs['permissions'] & 0x4000))) {
            return 'dir';
        }

        if ($type === NET_SFTP_TYPE_SYMLINK) {
            return 'link';
        }

        // Fallback: check permission bits.
        if (isset($attrs['permissions'])) {
            return $this->resolveTypeFromMode($attrs['permissions']);
        }

        return 'file';
    }

    /**
     * Determine the type from a Unix permission mode integer.
     */
    private function resolveTypeFromMode(int $mode): string
    {
        if (($mode & 0xA000) === 0xA000) {
            return 'link';
        }

        if (($mode & 0x4000) === 0x4000) {
            return 'dir';
        }

        return 'file';
    }

    /**
     * Format a permission integer into an octal string (e.g. "0755").
     */
    private function formatPermissions(int $permissions): string
    {
        return substr(sprintf('%o', $permissions), -4);
    }
}
