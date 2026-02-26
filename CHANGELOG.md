# Changelog

## v2.3.4 — 2026-02-26

### Added
- **Revert Commit** action on connected sites — choose from last 5 deployed commits or enter a custom hash to hard-reset the server deployment
- Deployment logs are now capped at 50 per site-repository connection, pruned automatically by the daily cron

### Fixed
- Webhook auto-deploy never triggered — `auto_deploy` was read from the wrong model instead of the pivot table

---

## v2.3.3

### Added
- Switched to Claude agent for WP-CLI command execution
- Avatar image support per tenant

### Fixed
- Null check for `parts['host']` in SSH `-T` command
- Notes field no longer mandatory in MySQL settings
- Various UI/UX improvements
