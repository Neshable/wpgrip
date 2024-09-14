<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\BackupSuccessful;
use App\Events\Git\GitPullSuccess;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
       
        // BackupSuccessful::class => [
        //     \App\Listeners\SendBackupNotification::class,
        // ],

        GitPullSuccess::class => [
            'App\Listeners\RepositoryListener@handleGitPullSuccessful',
            // \App\Listeners\SendRepositoryNotification::class,
        ],

        // Spatie uptime monitor related.
        'Spatie\UptimeMonitor\Events\UptimeCheckFailed' => [
            'App\Listeners\UptimeListener@handleUptimeCheckFailed',
        ],
        'Spatie\UptimeMonitor\Events\UptimeCheckRecovered' => [
            'App\Listeners\UptimeListener@handleUptimeCheckRecovered',
        ],
        'Spatie\UptimeMonitor\Events\UptimeCheckSucceeded' => [
            'App\Listeners\UptimeListener@handleUptimeCheckSucceeded',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
