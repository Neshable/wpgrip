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
            $site = Site::findOrFail( $site_id );
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

        if ( $tenant && $tenant->enable_slack && !empty( $tenant->slack_webhook ) ) {
            $typeEmoji  = $deployment_type === 'webhook' ? ':arrows_counterclockwise:' : ':bust_in_silhouette:';
            $typeLabel  = $deployment_type === 'webhook' ? 'Webhook (auto-deploy)' : 'Manual';
            $siteLabel  = $site ? $site->name . ' — ' . $site->url : 'Unknown site';

            $fields = [
                [
                    "type" => "mrkdwn",
                    "text" => "*Repository:*\n" . $repository->name
                ],
                [
                    "type" => "mrkdwn",
                    "text" => "*Site:*\n" . $siteLabel
                ],
                [
                    "type" => "mrkdwn",
                    "text" => "*Triggered by:*\n" . $typeEmoji . ' ' . $typeLabel
                ],
            ];

            SlackAlert::to( $tenant->slack_webhook )->blocks([
                self::addBlock( 'header', ":white_check_mark: Deployment successful" ),
                self::addBlock( 'divider' ),
                [
                    "type" => "section",
                    "fields" => $fields,
                ],
                self::addBlock( 'divider' ),
            ]);
        }
    }

    public static function sendBackupSuccess( Backup $backup )
    {
        // if ( !$monitor )
        // {
        //     return;
        // }
        // @todo webhook needs to be loaded from the current Team/Workspace related
        SlackAlert::to('https://hooks.slack.com/services/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')->blocks([
            self::addBlock( 'header', ":large_green_circle: Backup complete." ),
            self::addBlock( 'section', "Backup for site " . $backup->site_id . " completed." ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "Type of backup " . $backup->type ),
           
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
            self::addBlock( 'section', "<https://example.com|View request>" ),
        ]);
    }

    public static function sendUptimeFailed( Monitor $monitor, int $attempt = 1 )
    {
        $site_id = $monitor->site_id;
        if ( $site_id ) {
            $site = Site::findOrFail( $site_id );
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