

## About WPGrip

**WPGrip** is built using the beautiful Laravel framework (using [TALL](https://tallstack.dev/)) and offers an intuitive Filament admin panel that houses all the pre-built components like product, plans, discounts, payment providers, email providers, transactions, blog, user & role management, and much more.

**WPGrip** is developer-friendly, uses best coding practices, comes with an ever-growing automated tests that cover the critical components that it offers.

## Commands

- `php artisan delete:expired-records` - schedule deletion for old snapshots.

## Important schedules for WPGrip

### In `console.php`
- `Schedule::command('delete:expired-records')->daily();` - to trigger the deletion of old records and backups

### In `app.php`

- App\Jobs\Site\SyncAllSitesStats - Set to daily
- App\Jobs\Site\SyncAllSitesStats - Set to daily


