# Getting Started

Welcome to WPGrip — the control panel for managing all your WordPress sites via SSH. No plugins, no agents, no attack surface.

## What is WPGrip?

WPGrip connects to your WordPress sites directly over SSH and manages them using WP-CLI. This means:

- **Zero plugins** installed on your sites
- **No performance overhead** — nothing runs on your servers
- **No security risk** — no third-party code in your WordPress
- **Full control** — direct SSH & WP-CLI access

## Quick Start

### 1. Create your account

Sign up at [wpgrip.com](https://wpgrip.com) and choose a plan. Your workspace is created automatically.

### 2. Add your SSH public key

WPGrip generates a unique SSH key pair for your workspace. Copy the public key from **Settings → SSH Keys** and add it to your server’s `~/.ssh/authorized_keys` file.

```bash
# On your server, add WPGrip’s public key
echo "ssh-rsa AAAA...your-key..." >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### 3. Add your first site

Go to **Sites → Add Site** and enter:

| Field | Description |
|-------|-------------|
| **Site URL** | Your site’s public URL (e.g., `https://example.com`) |
| **SSH Host** | Server IP or hostname |
| **SSH User** | The Unix user with access to the WordPress installation |
| **SSH Port** | Usually `22` |
| **WordPress Path** | Absolute path to the WordPress root (e.g., `/var/www/html`) |

### 4. Sync your site

Once connected, WPGrip automatically syncs:

- WordPress version and configuration
- All installed plugins and their versions
- All installed themes and their versions
- Uptime monitoring begins immediately

::: tip
The first sync usually takes 10–30 seconds depending on your server’s SSH response time.
:::

## Next Steps

- [Add your first site](/guide/adding-your-first-site) — detailed walkthrough
- [SSH connection guide](/guide/ssh-connection) — key setup, troubleshooting
- [Dashboard overview](/guide/dashboard-overview) — navigate the interface
