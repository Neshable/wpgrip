<?php

namespace App\Services\Staging;

use App\Models\Site;

class StagingSyncStrategy
{
    /**
     * Determine the optimal sync strategy based on server topology.
     *
     * 'local'  — Both sites on the same server. Use local rsync + wp db pipe.
     * 'relay'  — Different servers. Stream through WPGrip memory via two SSH connections.
     */
    public static function resolve(Site $production, Site $staging): string
    {
        if ($production->server_id && $staging->server_id && $production->server_id === $staging->server_id) {
            return 'local';
        }

        return 'relay';
    }
}
