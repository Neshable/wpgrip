<?php

namespace App\Services;

use App\Models\ActivityLog;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Write an activity log entry.
     *
     * @param  string       $action        e.g. 'plugin.updated'
     * @param  string       $category      site | plugin | theme | backup | git | user | system
     * @param  string|null  $subjectLabel  Human-readable name of the thing acted on
     * @param  array        $meta          Extra key/value context
     * @param  int|null     $siteId        If scoped to a site
     * @param  int|null     $tenantId      Defaults to Filament tenant; pass explicitly from queue jobs
     * @param  int|null     $userId        Defaults to Auth::id(); null = system/queue
     * @param  string       $status        success | failed | pending
     */
    public static function log(
        string  $action,
        string  $category      = 'system',
        ?string $subjectLabel  = null,
        array   $meta          = [],
        ?int    $siteId        = null,
        ?int    $tenantId      = null,
        ?int    $userId        = -1,   // -1 = "use Auth::id()"
        string  $status        = 'success',
    ): void {
        try {
            // Resolve tenant
            $resolvedTenant = $tenantId
                ?? (Filament::getTenant()?->id)
                ?? null;

            if (! $resolvedTenant) {
                return; // Can't log without a workspace context
            }

            // Resolve user (-1 means "auto-detect from Auth")
            $resolvedUser = ($userId === -1) ? Auth::id() : $userId;

            ActivityLog::create([
                'tenant_id'     => $resolvedTenant,
                'user_id'       => $resolvedUser,
                'site_id'       => $siteId,
                'action'        => $action,
                'category'      => $category,
                'subject_label' => $subjectLabel,
                'meta'          => empty($meta) ? null : $meta,
                'ip_address'    => Request::ip(),
                'status'        => $status,
            ]);
        } catch (\Throwable $e) {
            // Never let logging crash the app
            \Illuminate\Support\Facades\Log::warning('ActivityLogger failed: ' . $e->getMessage());
        }
    }

    // ── Convenience shorthands ─────────────────────────────────────────────────

    public static function siteAction(string $action, \App\Models\Site $site, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'site', $site->name, $meta, $site->id, $site->tenant_id, -1, $status);
    }

    public static function pluginAction(string $action, \App\Models\Site $site, string $pluginName, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'plugin', $pluginName, array_merge(['site' => $site->name], $meta), $site->id, $site->tenant_id, -1, $status);
    }

    public static function themeAction(string $action, \App\Models\Site $site, string $themeName, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'theme', $themeName, array_merge(['site' => $site->name], $meta), $site->id, $site->tenant_id, -1, $status);
    }

    public static function backupAction(string $action, \App\Models\Site $site, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'backup', $site->name, $meta, $site->id, $site->tenant_id, -1, $status);
    }

    public static function gitAction(string $action, \App\Models\Site $site, string $label, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'git', $label, array_merge(['site' => $site->name], $meta), $site->id, $site->tenant_id, -1, $status);
    }

    public static function userAction(string $action, int $tenantId, string $label, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'user', $label, $meta, null, $tenantId, -1, $status);
    }

    public static function systemAction(string $action, int $tenantId, string $label, array $meta = [], string $status = 'success'): void
    {
        static::log($action, 'system', $label, $meta, null, $tenantId, null, $status);
    }
}
