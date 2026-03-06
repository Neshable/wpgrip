<?php

namespace App\Services;

use App\Models\Site;
use App\Models\User;
use App\Models\UptimeMonitor;
use Spatie\UptimeMonitor\Models\Monitor;
use App\Models\Backup;
use App\Models\Repository;

use Carbon\Carbon;
use Filament\Notifications\Actions\Action;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Filament\Notifications\Notification;

class SlackNotifications {

    public static $webhook;

    public static $site;

    public static function sendUptimeRecovered( Monitor $monitor )
    {
        $site_id = $monitor->site_id;
        if ( $site_id ) {
            $site = Site::find( $site_id );
            if ( $site ) {
                // Find the tenant and send them notificaiton.
                $tenant = $site->tenant;
                if ( $tenant && $tenant->enable_slack && !empty( $tenant->slack_webhook )  ) {
                    SlackAlert::to( $tenant->slack_webhook )->blocks([
                        self::addBlock( 'header', ":large_green_circle: Website back online." ),
                        self::addBlock( 'divider' ),
                        self::addBlock( 'section', "Website " . $monitor->url . " is up again" ),   
                        self::addBlock( 'divider' ),
                    ]);
                }
            }
        }

    }

    public static function sendGitPullSuccess( Repository $repository, ?Site $site = null, string $deployment_type = 'manual' )
    {
        $tenant = $repository->tenant;

        if ( !$tenant || !$tenant->enable_slack || empty( $tenant->slack_webhook ) ) {
            return;
        }

        // Determine branch from pivot (site <-> repo connection)
        $branch = null;
        if ( $site ) {
            $pivotSite = $repository->sites->firstWhere('id', $site->id);
            $branch = $pivotSite?->pivot?->branch;
        }

        // Get the latest deployment commit info
        $deployment = null;
        if ( $site ) {
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

        $providerIcon = match( $repository->provider ) {
            'bitbucket' => ':bitbucket:',
            'github'    => ':github:',
            default     => ':git:',
        };

        $blocks = [
            self::addBlock( 'header', $title ),
            self::addBlock( 'divider' ),
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
        if ( $deployment && $deployment->commit ) {
            $shortHash = substr($deployment->commit, 0, 7);
            $commitMsg = $deployment->message ? \Illuminate\Support\Str::limit($deployment->message, 80) : 'No message';
            $author = $deployment->committer ?? 'Unknown';

            $blocks[] = self::addBlock( 'divider' );
            $blocks[] = [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => ":memo: *Latest commit*\n`" . $shortHash . "` — " . $commitMsg . "\n_by " . $author . "_"
                ],
            ];
        }

        $blocks[] = self::addBlock( 'divider' );
        $blocks[] = [
            "type" => "context",
            "elements" => [
                [
                    "type" => "mrkdwn",
                    "text" => "Deployed via <" . rtrim(config('app.url'), '/') . "|WPGrip> at " . now()->format('H:i, M j Y')
                ],
            ],
        ];

        SlackAlert::to( $tenant->slack_webhook )->blocks( $blocks );
    }

    public static function sendBackupSuccess( Backup $backup )
    {
        $site = $backup->site;
        if ( !$site ) {
            return;
        }

        $tenant = $site->tenant;
        if ( !$tenant || !$tenant->enable_slack || empty($tenant->slack_webhook) ) {
            return;
        }

        SlackAlert::to( $tenant->slack_webhook )->blocks([
            self::addBlock( 'header', ":large_green_circle: Backup complete." ),
            self::addBlock( 'section', "Backup for site " . ($site->name ?? $backup->site_id) . " completed." ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "Type of backup: " . $backup->type ),
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Size:*\n" . $backup->size
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "*Destination:*\n" . $backup->provider
                    ]
                ]
            ],
            self::addBlock( 'divider' ),
        ]);
    }

    public static function sendUptimeFailed( Monitor $monitor, int $attempt = 1 )
    {
        $site_id = $monitor->site_id;
        if ( $site_id ) {
            $site = Site::find( $site_id );
            if ( $site ) {
                // Find the tenant and send them notificaiton.
                $tenant = $site->tenant;
                if ( $tenant && $tenant->enable_slack && !empty( $tenant->slack_webhook )  ) {
                    $attemptText = '';
                    if ( $attempt === 1 ) {
                        $attemptText = 'First failed check.';
                    } elseif ( $attempt === 2 ) {
                        $attemptText = 'Second failed check in a row.';
                    } elseif ( $attempt === 3 ) {
                        $attemptText = 'Third failed check in a row. Likely not a false positive; please investigate. Notifications will be snoozed until the monitor recovers.';
                    }

                    $blocks = [
                        self::addBlock( 'header', ":red_circle: Website appears to be down!" ),
                        self::addBlock( 'section', "Website " . $monitor->url . " is not responding." ),
                        self::addBlock( 'section', "Reason: " . $monitor->uptime_check_failure_reason ),
                        self::addBlock( 'section', $attemptText ),
                        self::addBlock( 'divider' ),
                    ];

                    SlackAlert::to( $tenant->slack_webhook )->blocks( $blocks );
                }
            }
        }

    }

    // public static function sendPHPErrorsFound( Site $site, $count = 0, $type = 'fatal' )
    // {
    //     if ( !$site ) {
    //         return false;
    //     }
        
    //     // @todo webhook needs to be loaded from the current Team/Workspace related
    //     SlackAlert::to('https://hooks.slack.com/services/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')->blocks([
    //         self::addBlock( 'header', ":red_circle: Some PHP errors has been found in the log for " . $site->name ),
    //         self::addBlock( 'section', "During our last scan, we found " . $count . " errors of type - " . $type ),
    //         self::addBlock( 'divider' ),
    //         self::addBlock( 'section', "Check your dashboard for  " . $site->url . " for more information." ),
           
    //         [
    //             "type" => "section",
    //             "fields" => [
    //                 [
    //                     "type" => "mrkdwn",
    //                     "text" => "*Status:*\n Found some issues"
    //                 ],
    //                 [
    //                     "type" => "mrkdwn",
    //                     "text" => "*Errors:*\n" . $count . " " . $type . " errors found."
    //                 ]
    //             ]
    //         ],  
    //         self::addBlock( 'divider' ),
    //     ]);
    // }


    public static function addBlock( string $type = 'section', $text = 'not available' )
    {
        switch($type) 
        {
            case 'header':
                return [
                    "type" => "header",
                    "text" => [
                        "type" => "plain_text",
                        "text" => $text,
                        "emoji" => true
                    ]
                ];
                break;
            case 'section':
                return [
                    "type" => "section",
                    "text" => [
                        "type" => "mrkdwn",
                        "text" => $text
                    ]
                ];
                break;
            case 'divider':
                return [
                    "type" => "divider"
                ];
                break;
        }


        return [
            "type" => "section",
            "text" => [
                "type" => "mrkdwn",
                "text" => $text
            ]
        ];
    }

    
}