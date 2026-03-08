# Creating Database Backups

WPGrip creates on-demand MySQL database backups of your WordPress sites via SSH, stores them securely on Amazon S3, and lets you restore with one click. This guide covers creating backups, managing retention, restoring, and plan limits.

## Prerequisites

- At least one **production** site connected to WPGrip (staging sites are excluded)
- The site must have a working SSH connection
- WP-CLI installed on the server

::: info Staging sites cannot be backed up
The backup feature is limited to production sites. If you need to back up a staging site, temporarily change its type to production in the site settings, create the backup, then switch it back.
:::

## How Backups Work

When you create a backup, WPGrip:

1. Opens an SSH connection to the site's server
2. Runs `wp db export` via WP-CLI to dump the MySQL database to a `.sql` file
3. Transfers the dump file from the server to WPGrip via SFTP
4. Uploads the dump to encrypted **Amazon S3** storage
5. Removes the temporary dump file from your server
6. Records the backup metadata (size, date, retention) in your dashboard

The entire process runs as a background job. Small databases complete in seconds; larger databases (100MB+) may take a few minutes.

::: tip No server-side agents
Like everything in WPGrip, backups use standard SSH + WP-CLI. Nothing is installed on your server. The temporary `.sql` file is cleaned up automatically after transfer.
:::

## Creating a Backup

1. Navigate to **Backups** in the sidebar
2. Click **Create Backup**
3. Fill in the form:

| Field | Required | Description | Default |
|-------|----------|-------------|---------|
| **Website** | Yes | Select from your production sites | — |
| **Keep snapshots for** | Yes | How long to retain this backup before automatic deletion | 1 month |
| **Excluded tables** | No | Comma-separated list of table names to skip | — |
| **Storage** | Read-only | Shows Amazon S3 | Amazon S3 |

4. Click **Create**

### Retention Options

Choose how long the backup should be kept:

| Option | Backup auto-deleted after |
|--------|---------------------------|
| 1 week | 7 days |
| 2 weeks | 14 days |
| 1 month | 30 days |
| 2 months | 60 days |
| 3 months | 90 days |

::: warning Expired backups are permanently deleted
When the retention period ends, the backup is removed from S3 and cannot be recovered. If you need to keep a backup indefinitely, download it to your own storage before it expires.
:::

### Excluding Tables

The **Excluded tables** field accepts a comma-separated list of MySQL table names to omit from the backup. This is useful for:

- Skipping large, regenerable tables (e.g., cache or analytics tables)
- Reducing backup size and transfer time
- Excluding tables with sensitive data you don't want stored externally

```
wp_options, wp_postmeta, wp_statistics_visitor
```

::: danger Be careful what you exclude
Excluding critical tables like `wp_options`, `wp_posts`, or `wp_users` means a restore from this backup will be incomplete. Only exclude tables you understand and can regenerate. If unsure, leave this field empty.
:::

::: tip Use the WordPress table prefix
WPGrip passes excluded tables directly to `wp db export --exclude_tables=...`. Use the exact table names including the WordPress prefix (usually `wp_`). If your site uses a custom prefix like `wpsm_`, use that instead (e.g., `wpsm_options`).
:::

## Plan Limits

The number of backups you can store at any time depends on your plan:

| Plan | Backup limit |
|------|--------------|
| **Basic** | 20 backups |
| **Pro** | 60 backups |
| **Agency** | Unlimited |
| **Enterprise** | Unlimited |

This limit counts all backups across all sites in the workspace. When you reach the limit, you must delete an existing backup or wait for one to expire before creating a new one.

::: info Check your usage
Your current backup count is visible at the top of the Backups page. Plan upgrade is available from **Settings → Billing**.
:::

## Managing Backups

The **Backups** page lists all backups in your workspace with:

| Column | Description |
|--------|-------------|
| **Site name** | Which site this backup belongs to |
| **Size** | File size of the database dump (compressed) |
| **Status** | `Completed`, `In Progress`, `Failed` |
| **Created** | Date and time the backup was created |
| **Retention** | When the backup will be auto-deleted |

### Available Actions

Each completed backup has three actions:

#### Download

Downloads the `.sql` dump file to your local machine. Use this to:

- Keep an off-platform copy of critical backups
- Import the database into a local development environment
- Share with a developer for debugging

#### Restore

Restores the backup to the original site by importing the `.sql` file via `wp db import` over SSH.

::: danger Restore overwrites the current database
Restoring a backup **replaces the entire database** on the target site with the backup's contents. All changes made since the backup was created will be lost — posts, comments, settings, orders, everything.
:::

Before restoring, **always** create a fresh backup of the current database first:

1. Click **Create Backup** to back up the current state
2. Wait for it to complete
3. Then proceed with the restore of the older backup

This gives you a safety net in case the restore introduces problems.

#### Delete

Permanently removes the backup from S3 storage. This cannot be undone.

## Backup Best Practices

### Create backups before major changes

Always back up before:

- WordPress core updates
- Plugin or theme updates (especially major versions)
- Database migrations
- Bulk content edits or imports
- WooCommerce updates or payment gateway changes

### Use shorter retention for frequent backups

If you create daily backups, use 1-week or 2-week retention to stay within your plan's limit. Reserve longer retention (2–3 months) for milestone backups before major releases.

### Download critical backups

Don't rely solely on WPGrip retention. Download backups of important milestones (pre-launch, pre-migration) to your own storage.

### Monitor backup size over time

If your backup sizes are growing rapidly, investigate which tables are largest:

```bash
ssh deploy@your-server.com
wp db size --tables --path=/var/www/html
```

This helps you identify tables to exclude or data to prune.

## Troubleshooting

### Backup stuck at "In Progress"

Backups are background jobs. If one stays in progress for more than 10 minutes:

1. Check the site's SSH connection is working (go to the site overview and verify the connection status)
2. The database may be very large — tables with millions of rows take time to export
3. Server resources may be constrained — high CPU or I/O can slow the dump

If it's still stuck after 30 minutes, the job likely failed silently. Delete the stuck backup and try again.

### Backup fails with "WP-CLI error"

Common causes:

| Error | Fix |
|-------|-----|
| `Error: 'wp_' is not a registered wp command` | WP-CLI is outdated — update with `wp cli update` |
| `Error establishing a database connection` | WordPress can't reach MySQL — check `wp-config.php` credentials |
| `Access denied for user` | The MySQL user doesn't have `LOCK TABLES` privilege (needed for export) |

### Restore fails

Restore failures are usually caused by:

- **SSH connection dropped** during import — retry the restore
- **MySQL max_allowed_packet too small** for large insert statements — increase in MySQL config
- **Disk space full** on the server — free up space before retrying

::: tip Large database restores
For databases over 500MB, the restore may take several minutes. Do not navigate away from the page or trigger another restore while one is in progress.
:::
