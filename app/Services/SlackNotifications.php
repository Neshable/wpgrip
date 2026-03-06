<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Tenant;
use App\Models\Backup;
use App\Models\Repository;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\SlackAlerts\Facades\SlackAlert;

class SlackNotifications
{
    /**
     * Resolve the correct webhook URL for a given notification channel.
     *
     * Channels:
     *   'alerts'      — Uptime, SSL, errors, backups (always uses main webhook)
     *   'deployments' — Git deploy success (uses dedicated webhook if set, else main)
     */
    protected static function resolveWebhook(Tenant $tenant, string $channel = 'alerts'): ?string
    {
        if (!$tenant->enable_slack || empty($tenant->slack_webhook)) {
            return null;
        }

        if ($channel === 'deployments' && !empty($tenant->slack_webhook_deployments)) {
            return $tenant->slack_webhook_deployments;
        }

        return $tenant->slack_webhook;
    }

    // ─── Uptime ──────────────────────────────────────────────────────

    public static function sendUptimeFailed(Monitor $monitor, int $attempt = 1): void
    {
        $site = self::siteFromMonitor($monitor);
        if (!$site) return;

        $tenant = $site->tenant;
        $webhook = self::resolveWebhook($tenant, 'alerts');
        if (!$webhook) return;

        $attemptText = match ($attempt) {
            1 => 'First failed check.',
            2 => 'Second consecutive failed check.',
            3 => 'Third consecutive failed check. Likely not a false positive — please investigate. Notifications snoozed until recovery.',
            default => "Check failed {$attempt} times in a row.",
        };

        SlackAlert::to($webhook)->blocks([
            self::addBlock('header', ':red_circle: Website appears to be down!'),
            self::addBlock('divider'),
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => ":globe_with_meridians: *Site*\n<" . rtrim($site->url, '/') . '|' . $site->name . '>'
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => ":link: *URL*\n" . (string) $monitor->url
                    ],
                ],
            ],
            self::addBlock('section', ":warning: *Reason:* " . ($monitor->uptime_check_failure_reason ?: 'Unknown')),
            self::addBlock('section', $attemptText),
            self::addBlock('divider'),
            self::contextBlock('Monitored by WPGrip'),
        ]);
    }

    public static function sendUptimeRecovered(Monitor $monitor): void
    {
        $site = self::siteFromMonitor($monitor);
        if (!$site) return;

        $tenant = $site->tenant;
        $webhook = self::resolveWebhook($tenant, 'alerts');
        if (!$webhook) return;

        SlackAlert::to($webhook)->blocks([
            self::addBlock('header', ':large_green_circle: Website back online'),
            self::addBlock('divider'),
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => ":globe_with_meridians: *Site*\n<" . rtrim($site->url, '/') . '|' . $site->name . '>'
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => ":link: *URL*\n" . (string) $monitor->url
                    ],
                ],
            ],
            self::addBlock('section', 'The website is responding normally again.'),
            self::addBlock('divider'),
            self::contextBlock('Monitored by WPGrip'),
        ]);
    }

    // ─── Deployments ─────────────────────────────────────────────────

    public static function sendGitPullSuccess(Repository $repository, ?Site $site = null, string $deployment_type = 'manual'): void
    {
        $tenant = $repository->tenant;
        if (!$tenant) return;

        $webhook = self::resolveWebhook($tenant, 'deployments');
        if (!$webhook) return;

        // Determine branch from pivot (site <-> repo connection)
        $branch = null;
        if ($site) {
            $pivotSite = $repository->sites->firstWhere('id', $site->id);
            $branch = $pivotSite?->pivot?->branch;
        }

        // Get the latest deployment commit info
        $deployment = null;
        if ($site) {
            $deployment = \App\Models\Deployment::where('repository_id', $repository->id)
                ->where('site_id', $site->id)
                ->latest()
                ->first();
        }

        $isWebhook = $deployment_type === 'webhook';
        $title = $isWebhook
            ? ':arrows_counterclockwise: Auto-deployment successful'
            : ':rocket: Manual deployment successful';

        $siteLabel = $site
            ? '<' . rtrim($site->url, '/') . '|' . $site->name . '>'
            : 'Unknown site';

        $providerIcon = match ($repository->provider) {
            'bitbucket' => ':bitbucket:',
            'github'    => ':github:',
            default     => ':git:',
        };

        $blocks = [
            self::addBlock('header', $title),
            self::addBlock('divider'),
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => ":globe_with_meridians: *Site*\n" . $siteLabel
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => $providerIcon . " *Repository*\n" . ($repository->name ?? 'Unknown')
                    ],
                ],
            ],
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => ":seedling: *Branch*\n`" . ($branch ?? 'unknown') . "`"
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => ":gear: *Trigger*\n" . ($isWebhook ? 'Push webhook' : 'Dashboard')
                    ],
                ],
            ],
        ];

        // Add commit info if available
        if ($deployment && $deployment->commit) {
            $shortHash = substr($deployment->commit, 0, 7);
            $commitMsg = $deployment->message ? \Illuminate\Support\Str::limit($deployment->message, 80) : 'No message';
            $author = $deployment->committer ?? 'Unknown';

            $blocks[] = self::addBlock('divider');
            $blocks[] = [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => ":memo: *Latest commit*\n`" . $shortHash . "` \u2014 " . $commitMsg . "\n_by " . $author . "_"
                ],
            ];
        }

        $blocks[] = self::addBlock('divider');
        $blocks[] = self::contextBlock('Deployed via WPGrip at ' . now()->format('H:i, M j Y'));

        SlackAlert::to($webhook)->blocks($blocks);
    }

    // ─── Backups ─────────────────────────────────────────────────────

    public static function sendBackupSuccess(Backup $backup): void
    {
        $site = $backup->site;
        if (!$site) return;

        $tenant = $site->tenant;
        if (!$tenant) return;

        $webhook = self::resolveWebhook($tenant, 'alerts');
        if (!$webhook) return;

        SlackAlert::to($webhook)->blocks([
            self::addBlock('header', ':large_green_circle: Backup complete'),
            self::addBlock('divider'),
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => ":globe_with_meridians: *Site*\n" . ($site->name ?? $backup->site_id)
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => ":file_folder: *Type*\n" . $backup->type
                    ],
                ],
            ],
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => ":floppy_disk: *Size*\n" . $backup->size
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => ":cloud: *Destination*\n" . $backup->provider
                    ],
                ],
            ],
            self::addBlock('divider'),
            self::contextBlock('Backed up via WPGrip'),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    /**
     * Look up the Site from a monitor's site_id, safely.
     */
    protected static function siteFromMonitor(Monitor $monitor): ?Site
    {
        return $monitor->site_id ? Site::find($monitor->site_id) : null;
    }

    /**
     * Build a Slack Block Kit block.
     */
    public static function addBlock(string $type = 'section', string $text = 'not available'): array
    {
        return match ($type) {
            'header' => [
                "type" => "header",
                "text" => [
                    "type" => "plain_text",
                    "text" => $text,
                    "emoji" => true,
                ],
            ],
            'divider' => [
                "type" => "divider",
            ],
            default => [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => $text,
                ],
            ],
        };
    }

    /**
     * Build a context block (small grey footer text).
     */
    public static function contextBlock(string $text): array
    {
        return [
            "type" => "context",
            "elements" => [
                [
                    "type" => "mrkdwn",
                    "text" => $text,
                ],
            ],
        ];
    }
}
