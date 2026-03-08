# Adding Your First Site

This guide walks you through connecting your first WordPress site to WPGrip.

## Prerequisites

- A WPGrip account with an active subscription
- SSH access to the server hosting your WordPress site
- WP-CLI installed on the server (WPGrip uses it for all operations)

::: info Check for WP-CLI
Most managed WordPress hosts have WP-CLI pre-installed. Verify by running:
```bash
wp --version
```
If it’s not installed, see the [WP-CLI installation guide](https://wp-cli.org/#installing).
:::

## Step 1: Configure SSH Access

1. Go to **Settings → SSH Keys** in your WPGrip dashboard
2. Copy your workspace’s **public key**
3. Add it to your server:

```bash
# SSH into your server
ssh your-user@your-server.com

# Add the key
mkdir -p ~/.ssh
echo "your-wpgrip-public-key" >> ~/.ssh/authorized_keys
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
```

## Step 2: Add the Site

1. Navigate to **Sites** in the sidebar
2. Click **Add Site**
3. Fill in the connection details:

| Field | Example | Notes |
|-------|---------|-------|
| Site URL | `https://myblog.com` | Must include protocol |
| SSH Host | `192.168.1.100` | IP address or hostname |
| SSH User | `deploy` | The user that owns the WP files |
| SSH Port | `22` | Default is 22 |
| WP Path | `/var/www/myblog/public` | Absolute path to `wp-config.php` directory |
| Environment | Production | Or Staging |

4. Click **Create** — WPGrip will test the connection and run the first sync

## Step 3: Verify the Connection

After adding, your site card will show:

- ✅ **Connected** — SSH connection successful
- Plugin and theme counts
- WordPress version
- Uptime status (monitoring starts automatically)

::: warning Connection Failed?
If you see a connection error, check:
1. The SSH public key is added to the correct user’s `authorized_keys`
2. The SSH port is correct (some hosts use non-standard ports)
3. Your server’s firewall allows inbound SSH from WPGrip’s IPs
4. The WordPress path is correct and the SSH user has read access
:::

## What Happens After Connection

WPGrip automatically:

1. **Syncs site data** — plugins, themes, WordPress version, configuration
2. **Starts uptime monitoring** — checks every minute
3. **Checks SSL certificate** — expiry date and validity
4. **Scans for vulnerabilities** — cross-references installed plugins/themes against the WPScan database

Daily automatic syncs keep everything up to date.
