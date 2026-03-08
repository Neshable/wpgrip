# Dashboard Overview

The WPGrip dashboard is your central hub for managing all connected WordPress sites.

## Sidebar Navigation

| Section | Description |
|---------|-------------|
| **Sites** | View and manage all connected WordPress sites |
| **Servers** | Manage server connections and SSH settings |
| **Backups** | Create, schedule, and restore database backups |
| **Repositories** | Manage Git repositories for push-to-deploy |
| **Clients** | Organize sites by client (for agencies) |
| **Activity Log** | Audit trail of all actions taken in the workspace |

## Site Cards

Each site in the list shows at a glance:

- **Status indicator** — green (up), red (down), yellow (degraded)
- **Response time** — sparkline chart showing the last 24 hours
- **WordPress version** — with update availability badge
- **Plugin/theme counts** — with vulnerability indicators
- **Last synced** — when WPGrip last pulled data from the site

## Site Detail Pages

Click any site to access its detail pages:

- **Overview** — Key stats, quick actions, screenshot
- **Plugins** — Full plugin list with versions, update status, and vulnerabilities
- **Themes** — Same as plugins, for themes
- **Monitoring** — Uptime chart, response time breakdown, SSL status
- **Backups** — Backup history with create/restore actions
- **Performance** — Lighthouse scores over time (mobile & desktop)
- **Repositories** — Connected Git repos with deployment history

## Workspace Switcher

If you belong to multiple workspaces, use the switcher in the top-left to move between them. Each workspace has its own sites, SSH keys, billing, and team members.

## Plan Usage

The sidebar shows your current plan and site usage (e.g., **1 / 50** sites used). Click **Upgrade** to change plans.
