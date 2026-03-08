# Database Backups

WPGrip can create and restore MySQL database backups for your WordPress sites over SSH.

## Creating a Backup

1. Go to **Backups** in the sidebar (or a specific site’s backup page)
2. Click **Create Backup**
3. Select the site
4. The backup runs immediately as a background job

WPGrip uses `wp db export` over SSH to dump the database, then transfers the file via SFTP.

## Backup Limits

The number of backups you can store depends on your plan:

| Plan | Backup Limit |
|------|--------------|
| Basic | 20 |
| Pro | 60 |
| Agency | Unlimited |
| Enterprise | Unlimited |

## Restoring a Backup

1. Find the backup in the **Backups** list
2. Click **Restore**
3. Confirm the action

::: danger
Restoring a backup **overwrites the current database** on the target site. This cannot be undone. Always create a fresh backup before restoring an older one.
:::

## Backup Contents

Backups include:
- ✅ Full MySQL database dump
- ❌ Files are **not** included (use Git deployments for code)

## Data Retention

Backups are retained as long as your account is active. Old backups count toward your plan’s backup limit.

::: tip
For critical sites, we recommend also maintaining your own backup strategy (e.g., server-level snapshots) in addition to WPGrip backups.
:::
