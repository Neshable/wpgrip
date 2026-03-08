# Managing Sites

Sites are the core resource in WPGrip. Each site represents a WordPress installation that WPGrip manages over SSH.

## Adding a Site

See the [Adding Your First Site](/guide/adding-your-first-site) guide for a detailed walkthrough.

## Site Overview

The site overview page shows:

- **Connection status** — whether WPGrip can reach the site via SSH
- **WordPress version** — with update badge if a newer version is available
- **PHP version** — the server’s PHP version
- **Site screenshot** — automatically captured weekly
- **Quick actions** — sync, take screenshot, open site

## Syncing

WPGrip syncs your site data automatically once per day. You can also trigger a manual sync from the site overview.

A sync pulls:
- WordPress core version
- All installed plugins and their versions
- All installed themes and their versions
- WordPress configuration details

## Production vs. Staging

When adding a site, you can mark it as **Production** or **Staging**. Staging sites:
- Are visually distinguished in the dashboard
- Can be excluded from monitoring alerts
- Don’t count toward performance testing quotas

## Removing a Site

Go to the site’s overview page and click **Delete**. This removes the site from WPGrip only — nothing is changed on your actual server.

::: tip
Deleting a site also removes its monitoring history, backups stored in WPGrip, and deployment logs.
:::
