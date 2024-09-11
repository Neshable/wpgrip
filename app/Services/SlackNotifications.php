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
        if ( !$monitor )
        {
            return;
        }

        // @todo webhook needs to be loaded from the current Team/Workspace related
        SlackAlert::to('https://hooks.slack.com/services/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')->blocks([
            self::addBlock( 'header', ":large_green_circle: Incident resolved." ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "Website " . $monitor->url . " is up again." ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "<https://example.com|View request>" ),
        ]);
    }

    public static function sendGitPullSuccess( Repository $repository )
    {
        SlackAlert::to('https://hooks.slack.com/services/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')->blocks([
            self::addBlock( 'header', ":white_check_mark: Repository successfully fetched" ),
            self::addBlock( 'section', "Repository successfully synced  " ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "Repository - " . $repository->name ),
           
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*On branch:*\n" . $repository->branch
                    ]
                ]
            ],  
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "<https://example.com|View request>" ),
        ]);
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

    public static function sendUptimeFailed( Monitor $monitor )
    {
        // if ( !$monitor )
        // {
        //     return;
        // }
        // @todo webhook needs to be loaded from the current Team/Workspace related
        SlackAlert::to('https://hooks.slack.com/services/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')->blocks([
            self::addBlock( 'header', ":red_circle: Issue with a website!" ),
            self::addBlock( 'section', $monitor->uptime_check_failure_reason ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "Website " . $monitor->url . " is down." ),
           
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Status:*\n Down"
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "*Date:*\n" . $monitor->uptime_check_failed_event_fired_on_date
                    ]
                ]
            ],  
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "<https://example.com|View request>" ),
        ]);
    }

    public static function sendPHPErrorsFound( Site $site, $count = 0, $type = 'fatal' )
    {
        if ( !$site ) {
            return false;
        }
        
        // @todo webhook needs to be loaded from the current Team/Workspace related
        SlackAlert::to('https://hooks.slack.com/services/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')->blocks([
            self::addBlock( 'header', ":red_circle: Some PHP errors has been found in the log for " . $site->name ),
            self::addBlock( 'section', "During our last scan, we found " . $count . " errors of type - " . $type ),
            self::addBlock( 'divider' ),
            self::addBlock( 'section', "Check your dashboard for  " . $site->url . " for more information." ),
           
            [
                "type" => "section",
                "fields" => [
                    [
                        "type" => "mrkdwn",
                        "text" => "*Status:*\n Found some issues"
                    ],
                    [
                        "type" => "mrkdwn",
                        "text" => "*Errors:*\n" . $count . " " . $type . " errors found."
                    ]
                ]
            ],  
            self::addBlock( 'divider' ),
        ]);
    }


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