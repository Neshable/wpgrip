<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// The runInBackground method may only be used when scheduling tasks via the command and exec methods.


// Schedule::command('app:generate-sitemap')->everyOddHour();

Schedule::command('app:metrics-beat')->dailyAt('00:01');

Schedule::command('monitor:check-uptime')->everyMinute()->runInBackground();
Schedule::command('monitor:check-certificate')->everyMinute()->runInBackground();

// Delete expired records
Schedule::command('delete:expired-records')->daily()->runInBackground();


Schedule::command('app:local-subscription-expiring-soon-reminder')->dailyAt('00:01');
Schedule::command('app:cleanup-local-subscription-statuses')->hourly();
Schedule::command('app:sync-seat-based-subscription-quantities')->hourly();

// Weekly site screenshots (Sunday at 02:00, runs in background queue)
Schedule::call(function () {
    \App\Models\Site::where('is_staging', false)
        ->whereNotNull('url')
        ->get()
        ->each(function ($site) {
            \App\Jobs\Site\TakeHomeScreenshot::dispatch($site);
        });
})->weekly()->sundays()->at('02:00')->name('weekly-site-screenshots')->withoutOverlapping();

// Laravel General Commands
// Flush the failed jobs queue
Schedule::command('queue:flush')->daily();
