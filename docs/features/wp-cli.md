# WP-CLI Access

WPGrip uses [WP-CLI](https://wp-cli.org/) as its primary interface with your WordPress sites. All data syncing, plugin detection, and configuration reading happens via WP-CLI commands over SSH.

## What WPGrip Uses WP-CLI For

| Operation | WP-CLI Command |
|-----------|---------------|
| Get WordPress version | `wp core version` |
| List plugins | `wp plugin list --format=json` |
| List themes | `wp theme list --format=json` |
| Get site URL | `wp option get siteurl` |
| Check database | `wp db check` |
| Export database | `wp db export` |

## Requirements

WP-CLI must be:
1. **Installed** on the server
2. **In the PATH** of the SSH user
3. **Version 2.0+** (recommended: latest)

### Installing WP-CLI

```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
php wp-cli.phar --info  # verify it works
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

### Verifying Installation

```bash
wp --version
# WP-CLI 2.10.0
```

## Permissions

The SSH user that WPGrip connects as must have:
- **Read access** to the WordPress installation directory
- **Execute permission** for WP-CLI
- **Read access** to `wp-config.php` (for database operations)
- **Write access** to the WordPress directory (only if using backup restore features)

::: tip
For maximum security, create a dedicated `wpgrip` user with read-only access to the WordPress directory. Grant write access only if you need backup restore or deployment features.
:::
