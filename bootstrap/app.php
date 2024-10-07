<?php

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\Site\SyncAllSitesStats;
use App\Jobs\External\GetVulnerabilityDatabase;

use App\Jobs\Domain\BulkDomainExpiry;
use App\Jobs\Global\CheckAllVulnerabilities;


return Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\BlockedUser::class,
        ]);

        $middleware->alias([
            'redirected-auth' => \App\Http\Middleware\RedirectedAuth::class,
            'sitemapped' => \App\Http\Middleware\Sitemapped::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Do a sync for all sites.
        $schedule->job(new SyncAllSitesStats)->daily();
        $schedule->job(new GetVulnerabilityDatabase)->dailyAt('13:00');

        // $schedule->job(new CheckAllVulnerabilities)->dailyAt('14:00');

        // Domain specific - expiry date, blacklists...
        $schedule->job(new BulkDomainExpiry)->weekly();

        
        

        // Schedule::exec('node /home/forge/script.js')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
