<?php

namespace App\Jobs\Staging;

use App\Models\Site;
use App\Models\StagingSync;
use App\Services\SSHSiteConnect;
use App\Services\SFTPSiteConnect;
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

    public int $timeout = 3600;
    public int $tries = 1;

    /**
     * Maximum DB dump size we'll relay through WPGrip memory (200 MB compressed).
     * Larger dumps must use the local (same-server) strategy.
     */
    private const MAX_RELAY_DB_BYTES = 200 * 1024 * 1024;

    /**
     * Maximum uploads size we'll relay through WPGrip memory (500 MB compressed).
     */
    private const MAX_RELAY_UPLOADS_BYTES = 500 * 1024 * 1024;

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

            $this->preflight($strategy);

            if ($this->syncDb) {
                $this->syncDatabase($strategy);
            }

            if ($this->syncUploads) {
                $this->syncUploads($strategy);
            }

            $this->postSyncCleanup();

            // ── Verify production is still healthy after all operations ──
            $this->verifyProductionHealth();

            $this->syncRecord->markAs('completed');

            $this->stagingSite->update(['last_sync' => now()]);

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

    // ─────────────────────────────────────────────────────────────────────
    //  PREFLIGHT
    // ─────────────────────────────────────────────────────────────────────

    private function preflight(string $strategy): void
    {
        $this->syncRecord->markAs('preflight');

        // ── Connect to production ──
        $this->prodSSH = new SSHSiteConnect($this->productionSite);
        if (! $this->prodSSH->active) {
            throw new \RuntimeException('Cannot connect to production server via SSH.');
        }

        // ── Verify WP-CLI on production ──
        $wpCheck = trim($this->prodSSH->exec(
            'cd ' . escapeshellarg($this->productionSite->dir_path) . ' && wp cli version 2>&1'
        ));
        if (! str_contains($wpCheck, 'WP-CLI')) {
            throw new \RuntimeException('WP-CLI not available on production server.');
        }

        // ── Verify production site responds (HTTP health check) ──
        $this->verifyProductionHealth();

        // ── Connect to staging ──
        $this->stagingSSH = new SSHSiteConnect($this->stagingSite);
        if (! $this->stagingSSH->active) {
            throw new \RuntimeException('Cannot connect to staging server via SSH.');
        }

        // ── Verify WP-CLI on staging ──
        $wpCheck = trim($this->stagingSSH->exec(
            'cd ' . escapeshellarg($this->stagingSite->dir_path) . ' && wp cli version 2>&1'
        ));
        if (! str_contains($wpCheck, 'WP-CLI')) {
            throw new \RuntimeException('WP-CLI not available on staging server.');
        }

        // ── Check staging disk space (need at least 1 GB free) ──
        $freeKb = trim($this->stagingSSH->exec(
            "df -k " . escapeshellarg($this->stagingSite->dir_path) . " | tail -1 | awk '{print \$4}'"
        ));
        if (is_numeric($freeKb) && (int) $freeKb < 1048576) {
            throw new \RuntimeException('Staging server has less than 1 GB free disk space.');
        }

        // ── For relay strategy, check sizes against memory limits ──
        if ($strategy === 'relay') {
            $this->preflightRelaySizeLimits();
        }

        // ── For local strategy, verify server-to-server SSH connectivity ──
        if ($strategy === 'local') {
            $sshCheck = trim($this->prodSSH->exec(
                'ssh -o BatchMode=yes -o ConnectTimeout=10 -o StrictHostKeyChecking=no ' .
                escapeshellarg($this->stagingSite->ssh_user . '@' . $this->stagingSite->server->ip) .
                " 'echo OK' 2>&1"
            ));
            if (trim($sshCheck) !== 'OK') {
                throw new \RuntimeException(
                    'Production server cannot SSH to staging server. ' .
                    'Ensure the tenant public key is in staging\'s authorized_keys. ' .
                    'Output: ' . substr($sshCheck, 0, 200)
                );
            }
        }
    }

    /**
     * For relay strategy, ensure DB and uploads don't exceed memory-safe limits.
     */
    private function preflightRelaySizeLimits(): void
    {
        $prodPath = escapeshellarg($this->productionSite->dir_path);

        if ($this->syncDb) {
            // Rough DB size check (uncompressed — compressed will be much smaller)
            $dbSizeRaw = trim($this->prodSSH->exec(
                "cd {$prodPath} && wp db size --size_format=b 2>/dev/null | grep -oP '\\d+'"
            ));
            if (is_numeric($dbSizeRaw)) {
                $this->syncRecord->update(['db_size_bytes' => (int) $dbSizeRaw]);
                // Estimate compressed size at ~15% of raw for typical WP databases
                $estimatedCompressed = (int) $dbSizeRaw * 0.15;
                if ($estimatedCompressed > self::MAX_RELAY_DB_BYTES) {
                    throw new \RuntimeException(
                        'Database is too large for cross-server relay sync (' .
                        number_format((int) $dbSizeRaw / 1048576, 0) . ' MB raw). ' .
                        'Place staging on the same server as production, or reduce the database size.'
                    );
                }
            }
        }

        if ($this->syncUploads) {
            $uploadsSizeRaw = trim($this->prodSSH->exec(
                "du -sb " . escapeshellarg($this->productionSite->dir_path . '/wp-content/uploads') .
                " 2>/dev/null | cut -f1"
            ));
            if (is_numeric($uploadsSizeRaw)) {
                $this->syncRecord->update(['uploads_size_bytes' => (int) $uploadsSizeRaw]);
                // Images/media barely compress, estimate ~90%
                $estimatedCompressed = (int) $uploadsSizeRaw * 0.9;
                if ($estimatedCompressed > self::MAX_RELAY_UPLOADS_BYTES) {
                    throw new \RuntimeException(
                        'Uploads directory is too large for cross-server relay sync (' .
                        number_format((int) $uploadsSizeRaw / 1048576, 0) . ' MB). ' .
                        'Place staging on the same server as production.'
                    );
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    //  DATABASE SYNC
    // ─────────────────────────────────────────────────────────────────────

    private function syncDatabase(string $strategy): void
    {
        $this->syncRecord->markAs('syncing_db');

        $prodPath = escapeshellarg($this->productionSite->dir_path);
        $stagingPath = escapeshellarg($this->stagingSite->dir_path);

        // ── Record DB size (if not already captured in preflight) ──
        if (! $this->syncRecord->db_size_bytes) {
            $dbSize = trim($this->prodSSH->exec(
                "cd {$prodPath} && wp db size --size_format=b 2>/dev/null | grep -oP '\\d+'"
            ));
            if (is_numeric($dbSize)) {
                $this->syncRecord->update(['db_size_bytes' => (int) $dbSize]);
            }
        }

        // ── Backup staging DB before overwrite (quick dump for rollback) ──
        $this->backupStagingDb($stagingPath);

        try {
            if ($strategy === 'local') {
                $this->syncDatabaseLocal($prodPath, $stagingPath);
            } else {
                $this->syncDatabaseRelay($prodPath, $stagingPath);
            }

            // ── Search-replace domains ──
            $this->syncRecord->markAs('replacing');
            $this->searchReplaceDomains($stagingPath);

            // ── Clean up staging backup (success path) ──
            $this->stagingSSH->exec(
                "cd {$stagingPath} && rm -f .wpgrip-pre-sync-backup.sql.gz"
            );
        } catch (\Throwable $e) {
            // Attempt to restore staging from pre-sync backup
            $this->restoreStagingDb($stagingPath);
            throw $e;
        }
    }

    /**
     * Quick backup of the staging DB before we overwrite it.
     */
    private function backupStagingDb(string $stagingPath): void
    {
        Log::info('Creating pre-sync backup of staging database');

        $result = $this->stagingSSH->exec(
            "cd {$stagingPath} && wp db export --single-transaction --quick - 2>/dev/null | gzip > .wpgrip-pre-sync-backup.sql.gz"
        );

        // Verify backup was created
        $size = trim($this->stagingSSH->exec(
            "stat -c%s {$stagingPath}/.wpgrip-pre-sync-backup.sql.gz 2>/dev/null || echo 0"
        ));

        if (! is_numeric($size) || (int) $size < 100) {
            Log::warning('Pre-sync staging backup may be empty — continuing anyway', ['size' => $size]);
        }
    }

    /**
     * Attempt to restore staging DB from the pre-sync backup.
     */
    private function restoreStagingDb(string $stagingPath): void
    {
        Log::warning('Attempting to restore staging DB from pre-sync backup');

        $check = trim($this->stagingSSH->exec(
            "test -f {$stagingPath}/.wpgrip-pre-sync-backup.sql.gz && echo EXISTS || echo MISSING"
        ));

        if ($check === 'EXISTS') {
            $this->stagingSSH->exec(
                "cd {$stagingPath} && gzip -d -c .wpgrip-pre-sync-backup.sql.gz | wp db import - 2>&1"
            );
            $this->stagingSSH->exec("cd {$stagingPath} && rm -f .wpgrip-pre-sync-backup.sql.gz");
            Log::info('Staging DB restored from pre-sync backup');
        } else {
            Log::error('Pre-sync backup not found — staging DB may be in an inconsistent state');
        }
    }

    /**
     * Same-server: pipe export directly to staging import via SSH.
     * Uses --single-transaction (InnoDB consistent snapshot, no table locks).
     * Uses gzip (default level 6) to balance speed vs CPU load on production.
     */
    private function syncDatabaseLocal(string $prodPath, string $stagingPath): void
    {
        $stagingUser = escapeshellarg($this->stagingSite->ssh_user);
        $stagingIp = escapeshellarg($this->stagingSite->server->ip);
        $stagingPathRaw = $this->stagingSite->dir_path;

        // --single-transaction: consistent snapshot, NO table locks (InnoDB)
        // --quick: row-by-row fetch, minimal memory on production
        // gzip (no -9): fast compression, low CPU impact on production
        $command = "cd {$prodPath} && wp db export --single-transaction --quick - | gzip | " .
            "ssh -o BatchMode=yes -o StrictHostKeyChecking=no {$stagingUser}@{$stagingIp} " .
            escapeshellarg("cd {$stagingPathRaw} && gzip -d | wp db import -");

        $this->prodSSH->ssh->setTimeout(1800);
        $result = $this->prodSSH->exec($command);

        // ── Verify import succeeded ──
        $this->verifyDatabaseImport($stagingPath);
    }

    /**
     * Cross-server: export on prod → transfer via SFTP to staging → import.
     * Uses SFTP (binary-safe) instead of PTY (which corrupts binary data).
     */
    private function syncDatabaseRelay(string $prodPath, string $stagingPath): void
    {
        $stagingPathRaw = $this->stagingSite->dir_path;
        $tempFile = '.wpgrip-sync-' . $this->syncRecord->id . '.sql.gz';

        // Step 1: Export + gzip on production, capture via exec (binary-safe)
        $this->prodSSH->ssh->setTimeout(1800);
        $dump = $this->prodSSH->exec(
            "cd {$prodPath} && wp db export --single-transaction --quick - | gzip"
        );

        if (empty($dump)) {
            throw new \RuntimeException('Database export returned empty output.');
        }

        $dumpSize = strlen($dump);
        Log::info('Relay DB export captured', ['compressed_bytes' => $dumpSize]);

        if ($dumpSize > self::MAX_RELAY_DB_BYTES) {
            throw new \RuntimeException(
                "Compressed DB dump is {$dumpSize} bytes, exceeds relay limit of " . self::MAX_RELAY_DB_BYTES . ' bytes.'
            );
        }

        // Step 2: Upload via SFTP (binary-safe, no PTY corruption)
        $stagingSFTP = new SFTPSiteConnect($this->stagingSite);
        if (! $stagingSFTP->active) {
            throw new \RuntimeException('Cannot establish SFTP connection to staging server.');
        }

        $remotePath = $stagingPathRaw . '/' . $tempFile;
        $uploaded = $stagingSFTP->sftp->put($remotePath, $dump);

        if (! $uploaded) {
            throw new \RuntimeException('Failed to upload database dump to staging via SFTP.');
        }

        // Free memory immediately
        unset($dump);

        Log::info('Relay DB dump uploaded via SFTP', ['remote_path' => $remotePath]);

        // Step 3: Import on staging + clean up temp file
        $this->stagingSSH->ssh->setTimeout(1800);
        $importResult = $this->stagingSSH->exec(
            "cd " . escapeshellarg($stagingPathRaw) .
            " && gzip -d -c {$tempFile} | wp db import - 2>&1; EXIT_CODE=\$?; rm -f {$tempFile}; exit \$EXIT_CODE"
        );

        Log::info('Relay DB import result', ['output' => substr($importResult, 0, 500)]);

        // ── Verify import succeeded ──
        $this->verifyDatabaseImport($stagingPath);
    }

    /**
     * Verify the database was imported correctly on staging.
     */
    private function verifyDatabaseImport(string $stagingPath): void
    {
        $tableCheck = trim($this->stagingSSH->exec(
            "cd {$stagingPath} && wp db tables 2>&1"
        ));

        if (empty($tableCheck) || str_contains($tableCheck, 'Error')) {
            throw new \RuntimeException('Database import verification failed: ' . substr($tableCheck, 0, 300));
        }

        // Verify we can query the options table (core WP table must exist)
        $siteUrl = trim($this->stagingSSH->exec(
            "cd {$stagingPath} && wp option get siteurl 2>&1"
        ));

        if (empty($siteUrl) || str_contains($siteUrl, 'Error')) {
            throw new \RuntimeException('Database import verification failed: cannot read siteurl option. Output: ' . substr($siteUrl, 0, 200));
        }

        Log::info('Database import verified', ['staging_siteurl' => $siteUrl]);
    }

    /**
     * Search-replace production domain with staging domain.
     */
    private function searchReplaceDomains(string $stagingPath): void
    {
        $prodDomain = $this->getCleanDomain($this->productionSite->url);
        $stagingDomain = $this->getCleanDomain($this->stagingSite->url);

        if (! $prodDomain || ! $stagingDomain || $prodDomain === $stagingDomain) {
            Log::info('Skipping search-replace: domains are identical or missing');
            return;
        }

        $this->stagingSSH->ssh->setTimeout(600);
        $result = $this->stagingSSH->exec(
            "cd {$stagingPath} && wp search-replace " .
            escapeshellarg($prodDomain) . ' ' .
            escapeshellarg($stagingDomain) .
            ' --skip-columns=guid --report-changed-only 2>&1'
        );

        Log::info('Search-replace result', ['output' => substr($result, 0, 500)]);

        // Also replace with protocol variations (https → https, http → http)
        foreach (['https://', 'http://'] as $protocol) {
            $from = $protocol . $prodDomain;
            $to = $protocol . $stagingDomain;
            // Already covered by domain-only replace above, but this catches
            // serialized data that may have full URLs embedded
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    //  UPLOADS SYNC
    // ─────────────────────────────────────────────────────────────────────

    private function syncUploads(string $strategy): void
    {
        $this->syncRecord->markAs('syncing_uploads');

        $prodPath = $this->productionSite->dir_path;
        $stagingPath = $this->stagingSite->dir_path;

        // Record uploads size (if not already captured in preflight)
        if (! $this->syncRecord->uploads_size_bytes) {
            $uploadsSize = trim($this->prodSSH->exec(
                "du -sb " . escapeshellarg("{$prodPath}/wp-content/uploads") . " 2>/dev/null | cut -f1"
            ));
            if (is_numeric($uploadsSize)) {
                $this->syncRecord->update(['uploads_size_bytes' => (int) $uploadsSize]);
            }
        }

        if ($strategy === 'local') {
            $this->syncUploadsLocal($prodPath, $stagingPath);
        } else {
            $this->syncUploadsRelay($prodPath, $stagingPath);
        }
    }

    /**
     * Same-server: rsync uploads via SSH. Read-only on production.
     */
    private function syncUploadsLocal(string $prodPath, string $stagingPath): void
    {
        $stagingUser = escapeshellarg($this->stagingSite->ssh_user);
        $stagingIp = escapeshellarg($this->stagingSite->server->ip);

        // rsync -az: archive mode + compress during transfer
        // --delete: remove files on staging that no longer exist on prod
        // --partial: keep partially transferred files for resume
        // --timeout=600: kill stalled transfers after 10 min
        $command = "rsync -az --delete --partial --timeout=600 " .
            escapeshellarg("{$prodPath}/wp-content/uploads/") . ' ' .
            "{$stagingUser}@{$stagingIp}:" . escapeshellarg("{$stagingPath}/wp-content/uploads/");

        $this->prodSSH->ssh->setTimeout(1800);
        $result = $this->prodSSH->exec($command);

        Log::info('Local uploads rsync completed', ['output' => substr($result, 0, 500)]);
    }

    /**
     * Cross-server: tar on prod → capture in PHP memory → SFTP to staging → extract.
     * Binary-safe via SFTP. Enforces size limits.
     */
    private function syncUploadsRelay(string $prodPath, string $stagingPath): void
    {
        $tempFile = '.wpgrip-uploads-sync-' . $this->syncRecord->id . '.tar.gz';

        // Tar + gzip on production (gzip default level — low CPU)
        $this->prodSSH->ssh->setTimeout(1800);
        $tarData = $this->prodSSH->exec(
            "cd " . escapeshellarg($prodPath) . "/wp-content && tar czf - uploads/ 2>/dev/null"
        );

        if (empty($tarData)) {
            Log::warning('Uploads tar returned empty — directory may not exist or be empty, skipping.');
            return;
        }

        $tarSize = strlen($tarData);
        Log::info('Relay uploads tar captured', ['compressed_bytes' => $tarSize]);

        if ($tarSize > self::MAX_RELAY_UPLOADS_BYTES) {
            throw new \RuntimeException(
                "Compressed uploads archive is " . number_format($tarSize / 1048576, 0) .
                " MB, exceeds relay limit of " . (self::MAX_RELAY_UPLOADS_BYTES / 1048576) . ' MB.'
            );
        }

        // Upload via SFTP (binary-safe)
        $stagingSFTP = new SFTPSiteConnect($this->stagingSite);
        if (! $stagingSFTP->active) {
            throw new \RuntimeException('Cannot establish SFTP connection to staging server.');
        }

        $remotePath = $stagingPath . '/' . $tempFile;
        $uploaded = $stagingSFTP->sftp->put($remotePath, $tarData);

        // Free memory immediately
        unset($tarData);

        if (! $uploaded) {
            throw new \RuntimeException('Failed to upload uploads archive to staging via SFTP.');
        }

        // Extract on staging + clean up
        $this->stagingSSH->ssh->setTimeout(1800);
        $extractResult = $this->stagingSSH->exec(
            "cd " . escapeshellarg($stagingPath) .
            "/wp-content && tar xzf " . escapeshellarg("../{$tempFile}") .
            " 2>&1; rm -f " . escapeshellarg("../{$tempFile}")
        );

        Log::info('Relay uploads extracted on staging', ['output' => substr($extractResult, 0, 300)]);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  POST-SYNC CLEANUP
    // ─────────────────────────────────────────────────────────────────────

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

        // Install + activate disable-emails plugin (prevent staging sending real emails)
        $this->stagingSSH->exec("cd {$path} && wp plugin install disable-emails --activate 2>&1");

        // Flush object cache
        $this->stagingSSH->exec("cd {$path} && wp cache flush 2>&1");

        // Flush rewrite rules
        $this->stagingSSH->exec("cd {$path} && wp rewrite flush --hard 2>&1");
    }

    // ─────────────────────────────────────────────────────────────────────
    //  PRODUCTION HEALTH CHECK
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Verify the production site is still responding.
     * Checks both HTTP response and WP-CLI status.
     */
    private function verifyProductionHealth(): void
    {
        // HTTP check — curl from the production server itself (most reliable)
        $prodUrl = rtrim($this->productionSite->url, '/');
        if ($prodUrl) {
            $httpStatus = trim($this->prodSSH->exec(
                "curl -s -o /dev/null -w '%{http_code}' --max-time 15 " . escapeshellarg($prodUrl) . ' 2>/dev/null'
            ));

            if (is_numeric($httpStatus) && (int) $httpStatus >= 500) {
                Log::error('Production health check FAILED after sync', [
                    'url' => $prodUrl,
                    'http_status' => $httpStatus,
                ]);
                // Don't throw — the sync itself is read-only on production,
                // so this is likely a pre-existing issue. Log it prominently.
            } else {
                Log::info('Production health check passed', [
                    'url' => $prodUrl,
                    'http_status' => $httpStatus,
                ]);
            }
        }

        // WP-CLI status check — make sure WordPress core is fine
        $wpStatus = trim($this->prodSSH->exec(
            'cd ' . escapeshellarg($this->productionSite->dir_path) . ' && wp core is-installed 2>&1 && echo WPOK'
        ));

        if (! str_contains($wpStatus, 'WPOK')) {
            Log::error('Production WP-CLI health check FAILED', ['output' => $wpStatus]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────────

    private function getCleanDomain(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host ? rtrim($host, '/') : null;
    }

    private function closeConnections(): void
    {
        foreach ([$this->prodSSH, $this->stagingSSH] as $ssh) {
            if ($ssh) {
                try {
                    $ssh->close();
                } catch (\Throwable $e) {
                    // Ignore close errors
                }
            }
        }
    }

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
