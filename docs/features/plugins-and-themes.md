# Plugins & Themes

WPGrip automatically tracks all plugins and themes installed on your connected sites.

## Plugin Dashboard

The plugins panel shows every plugin across all your sites in one view:

- **Name & version** — currently installed version
- **Update available** — badge when a newer version exists
- **Active/Inactive** — whether the plugin is activated
- **Vulnerability status** — cross-referenced against the WPScan database
- **Site count** — how many of your sites have this plugin installed

## Theme Dashboard

The themes panel works identically to plugins — shows all themes across all sites with version and vulnerability info.

## Bulk View

Filter and sort plugins/themes by:
- Name
- Update status (updates available)
- Vulnerability status
- Active/inactive state
- Specific site

## Updates

WPGrip detects available updates during each sync. Update information includes:
- Current version vs. latest version
- Whether the update addresses a known vulnerability

::: info
WPGrip is a read-only monitoring tool by default. Plugin/theme updates are tracked but must be applied by you via SSH, WP-CLI, or your preferred deployment method.
:::

## Vulnerability Detection

Plugins and themes are automatically cross-referenced against the WPScan vulnerability database, which is synced daily. Vulnerable items are highlighted with a severity badge.
