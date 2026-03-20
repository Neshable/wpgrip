<?php

namespace App\Ai\Tools;

use App\Ai\SshSessionManager;
use App\Models\Site;
use App\Services\SSHSiteConnect;
use App\Services\WPCliService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ClearCache implements Tool
{
    public function __construct(private Site $site, private ?SshSessionManager $session = null) {}

    public function description(): Stringable|string
    {
        return 'Clear WordPress cache on the site. Supports different cache types: object cache, WP Rocket, Autoptimize, W3 Total Cache, WP Super Cache, Cache Enabler, Fastest Cache, or Beaver Builder cache.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'cache_type' => $schema->string()->enum(['object', 'wprocket', 'autoptimize', 'w3_total_cache', 'supercache', 'cache_enabler', 'fastest_cache', 'beaver'])->required()->description('The type of cache to clear. Use "object" for the built-in WordPress object cache (wp cache flush).'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $type = $request['cache_type'];
        $cmd = WPCliService::clearCache($type);

        if (empty($cmd)) {
            return "Error: Unsupported cache type '{$type}'. Supported types: object, wprocket, autoptimize, w3_total_cache, supercache, cache_enabler, fastest_cache, beaver.";
        }

        if ($this->session) {
            $result = $this->session->execInSiteDir($cmd);

            if ($result['success']) {
                return "Cache cleared successfully ({$type}). Output: " . ($result['output'] ?: '(no output)');
            }

            return "Failed to clear cache. Output: " . ($result['output'] ?: ($result['error'] ?? '(no output)'));
        }

        $connection = new SSHSiteConnect($this->site);
        if (!$connection->active) {
            return 'Error: Cannot establish SSH connection to this site.';
        }

        $output = $connection->exec('cd ' . $this->site->dir_path . ' && ' . $cmd);
        $success = $connection->getExitStatusBool();
        $connection->close();

        if ($success) {
            return "Cache cleared successfully ({$type}). Output: " . ($output ?: '(no output)');
        }

        return "Failed to clear cache. Output: " . ($output ?: '(no output)');
    }
}
