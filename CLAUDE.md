# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WPGrip is a Laravel 11 SaaS platform (TALL stack) for managing WordPress sites at scale. It's built on the SaaSykit boilerplate and provides subscription billing, multi-tenancy, WordPress site management via SSH/WP-CLI, uptime monitoring, backups, performance testing, and a Filament 3.x admin panel.

## Commands

### Development
```bash
npm run dev          # Vite dev server with hot reload
npm run build        # Production asset build
php artisan serve    # Laravel dev server
php artisan queue:work           # Process background jobs
php artisan schedule:work        # Run task scheduler locally
php artisan horizon              # Queue monitoring UI
```

### Database
```bash
php artisan migrate              # Run pending migrations
php artisan migrate --seed       # Migrate + seed
php artisan migrate:fresh --seed # Full reset + seed
```

### Testing
```bash
php artisan test                              # Run all tests
php artisan test --filter=TestName            # Run a single test
php artisan test tests/Feature/SomeTest.php   # Run a specific file
```

### Code Quality
```bash
./vendor/bin/pint                # Laravel Pint (PHP code formatter)
./vendor/bin/phpstan analyse     # PHPStan static analysis (level 3)
```

### Deployment
```bash
php artisan filament:upgrade     # Update Filament assets after upgrade
./vendor/bin/dep deploy          # Deploy with Deployer (deploy.php)
```

## Architecture

### Multi-Tenancy
The platform uses a custom tenancy model (not a package). Each `Tenant` is an organization with one or more `User`s. The `TenantUser` pivot manages roles within a tenant. The active tenant is resolved via Filament's panel context. Models like `Site` auto-assign `tenant_id` in their `boot()` method using `Filament::getTenant()`.

SSH keys are generated per tenant (`ssh_private`/`ssh_public` stored encrypted) and used to authenticate to remote servers. See [app/Services/SSH/CreateUserSSHKeyPair.php](app/Services/SSH/CreateUserSSHKeyPair.php).

### Filament Panels
Two separate Filament panels:
- **Admin** (`app/Filament/Admin/`) — Super-admin panel for platform management (users, tenants, billing, products, plans)
- **Dashboard** (`app/Filament/Dashboard/`) — Tenant-facing panel for managing sites, servers, repositories, backups

### WordPress Site Management
Sites are managed remotely over SSH using `phpseclib3`. The flow:
1. `SSHSiteConnect` establishes the connection using tenant's encrypted private key
2. `WPCliService` generates WP-CLI command strings (never executes directly)
3. Jobs (in `app/Jobs/Site/`) dispatch these commands and parse results

`SFTPSiteConnect` handles file transfers. `SyncSiteStats` is the main job that pulls plugin/theme/config data from a site.

### Billing System
Supports three payment providers via a common interface (`PaymentProviderInterface`):
- Stripe (`app/Services/PaymentProviders/Stripe/`)
- Paddle (`app/Services/PaymentProviders/Paddle/`)
- LemonSqueezy (`app/Services/PaymentProviders/LemonSqueezy/`)

Core billing flow: `CheckoutManager` → `OrderManager` → `SubscriptionManager` → provider-specific service. Webhook handlers live alongside each provider.

### Job Queue Architecture
Heavy operations run as queued jobs, organized by domain:
- `Jobs/Site/` — Sync site stats, stats aggregation
- `Jobs/Backup/` — Backup creation/restoration
- `Jobs/Domain/` — Domain expiry checks
- `Jobs/Git/` — Repository pulls and deployments
- `Jobs/Tests/` — Google Lighthouse page speed tests (uses Puppeteer/Node)
- `Jobs/External/` — Vulnerability database sync (WPScan)
- `Jobs/Updates/` — Plugin/theme update jobs

### Service Layer
Business logic is isolated in `app/Services/`. Key services:
- `TenantManager` / `TenantCreationManager` — Tenant lifecycle and onboarding
- `SubscriptionManager` / `TenantSubscriptionManager` — Subscription operations
- `CalculationManager` — Proration and pricing calculations
- `MetricsManager` — Analytics aggregation
- `GripNotifications` — Centralized Filament notification dispatch
- `WPCliService` — Static class returning WP-CLI command strings

### Scheduled Tasks
Defined in two places:
- [bootstrap/app.php](bootstrap/app.php) — Job-based schedules (site sync, vulnerability DB, domain checks, Lighthouse)
- [routes/console.php](routes/console.php) — Command-based schedules (metrics, uptime monitoring, subscription cleanup)

### Monitoring
Uptime and SSL certificate monitoring uses `spatie/laravel-uptime-monitor`. Each `Site` auto-creates an `UptimeMonitor` on creation. Monitors run every minute via scheduled commands.

### Configuration
- `config/` — Standard Laravel config files
- Settings manageable via Filament admin: general settings, invoice settings, tenancy settings, payment providers, email providers, OAuth providers
- `phpstan.neon` — PHPStan at level 3 analyzing `app/`
