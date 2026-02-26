<?php

namespace App\Listeners;

use App\Events\Git\GitPullSuccess;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Services\SlackNotifications;

class RepositoryListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GitPullSuccess $event): void
    {
        SlackNotifications::sendGitPullSuccess( $event->repository, $event->site, $event->deployment_type );
    }

    /**
     * Handle the UptimeCheckSucceeded event.
     */
    public function handleGitPullSuccessful( GitPullSuccess $event ): void
    {
        SlackNotifications::sendGitPullSuccess( $event->repository, $event->site, $event->deployment_type );
    }
}
