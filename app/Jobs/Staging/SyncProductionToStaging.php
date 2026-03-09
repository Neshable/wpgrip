<?php

namespace App\Jobs\Staging;

use App\Models\Site;
use App\Models\StagingSync;
use App\Services\SSHSiteConnect;
use App\Services\GripNotifications;
use App\Services\Staging\StagingSyncStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncProductionToStaging implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 hour
    public int $tries = 1;

    private ?SSHSiteConnect $prodSSH = null;
    private ?SSHSiteConnect $stagingSSH = null;

    public function __construct(
        public Site $productionSite,
        public Site $stagingSite,
        public StagingSync $syncRecord,
        public bool $syncDb = true,
        public bool $syncUploads = true,
    ) {
        $this->onQueue('staging');
    }

    public function handle(): void
    {
        $lock = Cache::lock("staging-sync:{$this->stagingSite->id}", 3600);

        if (! $lock->get()) {
            $this->syncRecord->fail('Another sync is already running for this staging site.');
            return;
        }

        try {
            $strategy = StagingSyncStrategy::resolve($this->productionSite, $this->stagingSite);
            $this->syncRecord->update(['strategy' => $strategy]);

            $this->preflight();

            if ($this->syncDb) {
                $this->syncDatabase($strategy);
            }

            if ($this->syncUploads) {
                $this->syncUploads($strategy);
            }

            $this->postSyncCleanup();

            $this->syncRecord->markAs('completed');

            // Update last_sync on the staging site
            $this->stagingSite->update(['last_sync' => now()]);

            // Notify user
            GripNotifications::getStagingSyncComplete($this->productionSite->user_id);

            Log::info('Staging sync completed', [
                'production' => $this->productionSite->id,
                'staging' => $this->stagingSite->id,
                'strategy' => $strategy,
                'duration' => $this->syncRecord->duration,
            ]);
        } catch (\Throwable $e) {
            $this->syncRecord->fail($e->getMessage());

            Log::error('Staging sync failed', [
                'production' => $this->productionSite->id,
                'staging' => $this->stagingSite->id,
                'error' => $e->getMessage(),
            ]);

            report($e);
        } finally {
            $this->closeConnections();
            $lock->release();
        }
    }

    /**
     * Preflight: verify SSH connectivity and WP-CLI on both sites.
     */
    private function preflight(): void
    {
        $this->syncRecord->markAs('preflight');

        // Connect to production
        $this->prodSSH = new SSHSiteConnect($this->productionSite);
        if (! $this->prodSSH->active) {
            throw new \RuntimeException('Cannot connect to production server via SSH.');
        }

        // Verify WP-CLI on production
        $wpCheck = trim($this->prodSSH->exec('cd ' . $this->productionSite->dir_path . ' && wp cli version 2>&1'));
        if (! str_contains($wpCheck, 'WP-CLI')) {
            throw new \RuntimeException('WP-CLI not available on production server.');
        }

        // Connect to staging
        $this->stagingSSH = new SSHSiteConnect($this->stagingSite);
        if (! $this->stagingSSH->active) {
            throw new \RuntimeException('Cannot connect to staging server via SSH.');
        }

        // Verify WP-CLI on staging
        $wpCheck = trim($this->stagingSSH->exec('cd ' . $this->stagingSite->dir_path . ' && wp cli version 2>&1'));
        if (! str_contains($wpCheck, 'WP-CLI')) {
            throw new \RuntimeException('WP-CLI not available on staging server.');
        }
    }

    /**
     * Sync database: export from prod, import to staging, search-replace.
     */
    private function syncDatabase(string $strategy): void
    {
        $this->syncRecord->markAs('syncing_db');

        $prodPath = escapeshellarg($this->productionSite->dir_path);
        $stagingPath = escapeshellarg($this->stagingSite->dir_path);

        // Get DB size for tracking
        $dbSize = trim($this->prodSSH->exec("cd {$prodPath} && wp db size --size_format=b 2>/dev/null | grep -oP '\\d+'"));
        if (is_numeric($dbSize)) {
            $this->syncRecord->update(['db_size_bytes' => (int) $dbSize]);
        }

        if ($strategy === 'local') {
            $this->syncDatabaseLocal($prodPath, $stagingPath);
        } else {
            $this->syncDatabaseRelay($prodPath, $stagingPath);
        }

        // Search-replace domains
        $this->syncRecord->markAs('replacing');
        $prodDomain = $this->getCleanDomain($this->productionSite->url);
        $stagingDomain = $this->getCleanDomain($this->stagingSite->url);

        if ($prodDomain && $stagingDomain && $prodDomain !== $stagingDomain) {
            $result = $this->stagingSSH->exec(
                "cd {$stagingPath} && wp search-replace " .
                escapeshellarg($prodDomain) . ' ' .
                escapeshellarg($stagingDomain) .
                ' --skip-columns=guid --report-changed-only 2>&1'
            );

            Log::info('Search-replace result', ['output' => $result]);
        }
    }

    /**
     * Same-server DB sync: pipe export directly to import via SSH.
     */
    private function syncDatabaseLocal(string $prodPath, string $stagingPath): void
    {
        $stagingUser = $this->stagingSite->ssh_user;
        $stagingIp = $this->stagingSite->server->ip;
        $stagingPathRaw = $this->stagingSite->dir_path;

        $command = "cd {$prodPath} && wp db export --single-transaction --quick - | gzip -9 | " .
            "ssh -o StrictHostKeyChecking=no {$stagingUser}@{$stagingIp} " .
            escapeshellarg("cd {$stagingPathRaw} && gzip -d | wp db import -");

        $result = $this->prodSSH->exec($command);

        // Verify import succeeded
        $tableCheck = trim($this->stagingSSH->exec("cd {$stagingPath} && wp db tables 2>&1"));
        if (empty($tableCheck) || str_contains($tableCheck, 'Error')) {
            throw new \RuntimeException('Database import verification failed: ' . $tableCheck);
        }
    }

    /**
     * Cross-server DB sync: export on prod, transfer gzipped dump to staging, import.
     * Uses temp file on staging to avoid pipe complexity across two SSH sessions.
     */
    private function syncDatabaseRelay(string $prodPath, string $stagingPath): void
    {
        $this->prodSSH->ssh->setTimeout(1800); // 30 min for large DBs
        $stagingPathRaw = $this->stagingSite->dir_path;
        $tempFile = '.wpgrip-sync-' . time() . '.sql.gz';

        // Step 1: Export + gzip on production, capture output
        $dump = $this->prodSSH->exec("cd {$prodPath} && wp db export --single-transaction --quick - | gzip -9");

        if (empty($dump)) {
            throw new \RuntimeException('Database export returned empty output.');
        }

        Log::info('Relay DB export captured', ['size_bytes' => strlen($dump)]);

        // Step 2: Write the gzipped dump to staging via stdin
        $this->stagingSSH->ssh->enablePTY();
        $this->stagingSSH->ssh->exec("cat > {$stagingPathRaw}/{$tempFile}");
        $this->stagingSSH->ssh->write($dump);
        $this->stagingSSH->ssh->write(chr(4)); // EOF (Ctrl+D)
        usleep(500000);
        $this->stagingSSH->ssh->disablePTY();

        // Step 3: Import on staging
        $importResult = $this->stagingSSH->exec(
            "cd {$stagingPathRaw} && gzip -d -c {$tempFile} | wp db import - 2>&1 && rm -f {$tempFile}"
        );

        Log::info('Relay DB import result', ['output' => $importResult]);

        // Verify
        $tableCheck = trim($this->stagingSSH->exec("cd {$stagingPath} && wp db tables 2>&1"));
        if (empty($tableCheck) || str_contains($tableCheck, 'Error')) {
            $this->stagingSSH->exec("rm -f {$stagingPathRaw}/{$tempFile}");
            throw new \RuntimeException('Relay database import verification failed: ' . $tableCheck);
        }
    }

    /**
     * Sync wp-content/uploads from production to staging.
     */
    private function syncUploads(string $strategy): void
    {
        $this->syncRecord->markAs('syncing_uploads');

        $prodPath = $this->productionSite->dir_path;
        $stagingPath = $this->stagingSite->dir_path;

        // Get uploads size for tracking
        $uploadsSize = trim($this->prodSSH->exec(
            "du -sb " . escapeshellarg("{$prodPath}/wp-content/uploads") . " 2>/dev/null | cut -f1"
        ));
        if (is_numeric($uploadsSize)) {
            $this->syncRecord->update(['uploads_size_bytes' => (int) $uploadsSize]);
        }

        if ($strategy === 'local') {
            $this->syncUploadsLocal($prodPath, $stagingPath);
        } else {
            $this->syncUploadsRelay($prodPath, $stagingPath);
        }
    }

    /**
     * Same-server uploads sync: rsync via SSH (handles different users).
     */
    private function syncUploadsLocal(string $prodPath, string $stagingPath): void
    {
        $stagingUser = $this->stagingSite->ssh_user;
        $stagingIp = $this->stagingSite->server->ip;

        $command = "rsync -az --delete --partial " .
            escapeshellarg("{$prodPath}/wp-content/uploads/") . ' ' .
            "{$stagingUser}@{$stagingIp}:" . escapeshellarg("{$stagingPath}/wp-content/uploads/");

        $result = $this->prodSSH->exec($command);

        Log::info('Local uploads rsync result', ['output' => substr($result, 0, 500)]);
    }

    /**
     * Cross-server uploads sync: tar stream through WPGrip memory.
     */
    private function syncUploadsRelay(string $prodPath, string $stagingPath): void
    {
        $this->prodSSH->ssh->setTimeout(1800);

        $tarData = $this->prodSSH->exec(
            "cd " . escapeshellarg($prodPath) . "/wp-content && tar czf - uploads/ 2>/dev/null"
        );

        if (empty($tarData)) {
            Log::warning('Uploads tar returned empty — directory may not exist or be empty.');
            return;
        }

        Log::info('Relay uploads tar captured', ['size_bytes' => strlen($tarData)]);

        // Write tar to staging and extract
        $this->stagingSSH->ssh->enablePTY();
        $this->stagingSSH->ssh->exec("cd " . escapeshellarg($stagingPath) . "/wp-content && tar xzf -");
        $this->stagingSSH->ssh->write($tarData);
        $this->stagingSSH->ssh->write(chr(4)); // EOF
        usleep(500000);
        $this->stagingSSH->ssh->disablePTY();

        Log::info('Relay uploads sync completed');
    }

    /**
     * Post-sync cleanup: flush caches, disable emails, rebuild permalinks.
     */
    private function postSyncCleanup(): void
    {
        $this->syncRecord->markAs('cleanup');

        $path = escapeshellarg($this->stagingSite->dir_path);

        // Update table prefix if production has a different one
        $prodPrefix = $this->productionSite->db_prefix;
        if ($prodPrefix) {
            $this->stagingSSH->exec(
                "cd {$path} && wp config set table_prefix " . escapeshellarg($prodPrefix)
            );
        }

        // Install + activate disable-emails plugin
        $this->stagingSSH->exec("cd {$path} && wp plugin install disable-emails --activate 2>&1");

        // Flush object cache
        $this->stagingSSH->exec("cd {$path} && wp cache flush 2>&1");

        // Flush rewrite rules
        $this->stagingSSH->exec("cd {$path} && wp rewrite flush --hard 2>&1");
    }

    /**
     * Extract clean domain from URL.
     */
    private function getCleanDomain(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host ? rtrim($host, '/') : null;
    }

    /**
     * Close all SSH connections.
     */
    private function closeConnections(): void
    {
        foreach ([$this->prodSSH, $this->stagingSSH] as $ssh) {
            if ($ssh) {
                try { $ssh->close(); } catch (\Throwable $e) { /* ignore */ }
            }
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->syncRecord->fail($exception->getMessage());
        $this->closeConnections();

        Log::error('Staging sync job failed', [
            'production' => $this->productionSite->id,
            'staging' => $this->stagingSite->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
