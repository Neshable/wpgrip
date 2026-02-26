

## WPGrip — v2.3.4

**Laravel**: ^11.23  
**PHP**: >= 8.2  
**Node.js**: >= 18 (Vite ^5)

WPGrip is a Laravel-based platform (TALL stack + Filament) providing a full-featured admin panel and SaaS building blocks: products, plans, subscriptions, discounts, payment providers, email providers, invoices/transactions, user/roles/permissions, blog, monitoring, and site management.

### Features
- SaaS billing primitives (products, plans, plan prices, discounts, orders, subscriptions, trials, transactions)
- Payments integration scaffolding (Stripe, Paddle, LemonSqueezy via provider data tables)
- Filament Admin 3.x with ready-made resources and widgets
- Sites management (sites, repositories, plugins, themes, performance data, snapshots, backups, monitors)
- Git deployment system with push-to-deploy webhooks (GitHub & Bitbucket), deployment logs, and commit revert
- Uptime monitoring and certificate checks (Spatie Uptime Monitor)
- Multi-tenancy primitives (tenants, tenant users, invitations)
- Security and analytics (vulnerabilities DB sync, metrics)
- Blog and content primitives (posts, categories, media)

---

## Requirements
- PHP 8.2+
  - Extensions: PDO (mysql/pgsql/sqlsrv as needed), OpenSSL, Mbstring, Tokenizer, JSON, cURL, XML, Fileinfo, GD/Imagick
- Composer
- Node.js 18+ and npm (Vite 5)
- Database: MySQL 8+/MariaDB 10.6+, or PostgreSQL 13+, or SQL Server (per `config/database.php`)
- Redis (phpredis) for queues/cache recommended
- Chrome/Chromium for Browsershot/Lighthouse-based tasks (optional but recommended)

---

## Getting Started (Local Setup)

1) Clone and install dependencies
```bash
cp .env.example .env
composer install
npm install
```

2) Configure environment
- Set `APP_URL`, `APP_NAME`, `APP_ENV=local`, `APP_KEY` (run `php artisan key:generate` if not set)
- Set database credentials for `DB_CONNECTION` (default `mysql`) in `.env`
- Configure Redis if used (`REDIS_*`), mailers, and any third-party providers as needed

3) Generate key and run migrations/seeders
```bash
php artisan key:generate
php artisan migrate --seed
```

4) Build frontend assets
```bash
npm run dev   # or: npm run build
```

5) Run the application
```bash
php artisan serve
```

6) Queue worker and scheduler
```bash
php artisan queue:work
php artisan schedule:work
```

Optional: Horizon for queue monitoring
```bash
php artisan horizon
```

---

## Schedules

Schedules are defined in two places:

1) `routes/console.php`
```16:37:routes/console.php
// Schedule::command('app:generate-sitemap')->everyOddHour();

Schedule::command('app:metrics-beat')->dailyAt('00:01');

Schedule::command('monitor:check-uptime')->everyMinute()->runInBackground();
Schedule::command('monitor:check-certificate')->everyMinute()->runInBackground();

// Delete expired records
Schedule::command('delete:expired-records')->daily()->runInBackground();

Schedule::command('app:local-subscription-expiring-soon-reminder')->dailyAt('00:01');
Schedule::command('app:cleanup-local-subscription-statuses')->hourly();
Schedule::command('app:sync-seat-based-subscription-quantities')->hourly();

// Laravel General Commands
// Flush the failed jobs queue
Schedule::command('queue:flush')->daily();
```

2) `bootstrap/app.php`
```35:49:bootstrap/app.php
// Do a sync for all sites.
$schedule->job(new SyncAllSitesStats)->daily();
$schedule->job(new GetVulnerabilityDatabase)->dailyAt('13:00')->onOneServer();

// $schedule->job(new CheckAllVulnerabilities)->dailyAt('14:00');

// Domain specific - expiry date, blacklists...
$schedule->job(new BulkDomainExpiry)->weekly();

// Performance checks - @todo test with large amount of sites
$schedule->job(new ScheduleTests)->dailyAt('01:00')->onOneServer();
```

Run locally with:
```bash
php artisan schedule:work
```
Or via cron:
```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## Main Database Tables

The schema is extensive. Below are primary domains and representative tables (see `database/migrations` for full details):

- Authentication & Users
  - `users`, `password_reset_tokens`, `personal_access_tokens`
- Tenancy & Access
  - `tenants`, `tenant_user`, `invitations`, roles/permissions via Spatie (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`)
- Billing & Commerce
  - Catalog: `products`, `plans`, `plan_prices`, `intervals`, `currencies`
  - Orders: `orders`, `order_items`, `order_discounts`
  - Discounts: `discounts`, `discount_codes`, `discount_code_redemptions`, `discount_plan`, `discount_payment_provider_data`
  - Subscriptions: `subscriptions`, `subscription_versions`, `subscription_discounts`, `user_subscription_trials`
  - Transactions & Invoices: `transactions`, `transaction_versions`, `invoices`
  - Payment Providers: `payment_providers`, `plan_payment_provider_data`, `plan_price_payment_provider_data`, `user_stripe_data`, `one_time_products`, `one_time_product_prices`, `one_time_product_payment_provider_data`, `one_time_product_price_payment_provider_data`
  - Usage-based billing: `plan_meters`, `plan_meter_payment_provider_data`, `subscription_usages`
- Sites & DevOps
  - Sites: `sites`, `sites_meta`, `repositories`, `site_repositories`
  - WP Stack: `plugins`, `plugin_site`, `themes`, `theme_site`
  - CI/Deploy: `deployments`, `repo_commits`
  - Observability: `performance_data`, `monitors`, `notifications`, `backups`, `snapshots`, `ai_insights`
- Security & Vulnerabilities
  - `vulnerabilities`
- Content & Media
  - `blog_posts`, `blog_post_categories`, `media`
- Configuration & Metrics
  - `configs`, `metrics`, `metric_data`, `user_parameters`, `announcements`
- Providers & Integrations
  - `email_providers`, `verification_providers`, `oauth_login_providers`
- Jobs & Framework
  - `jobs`, `failed_jobs`, `migrations`

Note: Exact columns and indexes are defined in the respective migration files.

---

## Development Scripts

- Vite dev server: `npm run dev`
- Production build: `npm run build`

Post-install scripts (Composer) automatically publish assets and run Filament upgrade hooks.

---

## Testing

```bash
php artisan test
```

---

## Useful Artisan Commands
- `php artisan delete:expired-records` – deletes expired snapshots and prunes deployment logs (keeps last 50 per site-repository connection)
- Queue maintenance: `php artisan queue:flush` (scheduled daily)

---

## Notes
- Default DB connection is `mysql` (see `config/database.php`), but `sqlite`, `pgsql`, and `sqlsrv` are configured.
- Some scheduled jobs are constrained with `onOneServer()` for distributed environments.
- For Browsershot/Lighthouse, ensure a local Chrome/Chromium is available.


