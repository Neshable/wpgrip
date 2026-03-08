# Adding a Site — Step by Step

This guide walks you through the complete Add Site wizard in WPGrip. Adding a site is a two-step process: first you provide connection details, then you assign a client and server. Before you begin, you **must** set up SSH access — this is the single most important step.

## Prerequisites

- A WPGrip account with an active subscription
- SSH access to the server hosting your WordPress site
- WP-CLI installed on the server
- The WordPress installation path on the server

::: danger SSH Key Setup Is Non-Negotiable
WPGrip connects to your servers exclusively over SSH. If the SSH key is not installed correctly, **nothing will work** — no syncing, no monitoring, no backups, no deployments. Complete the SSH key setup below before filling in any other field.
:::

## Understanding the WPGrip SSH Key

Every WPGrip workspace has a **unique SSH key pair**:

| Key | Location | Purpose |
|-----|----------|---------|
| **Private key** | Stored encrypted on WPGrip's servers | Used by WPGrip to authenticate with your server |
| **Public key** | Displayed in the Add Site form and in **Settings → SSH Keys** | You copy this to your server |

This is **not** the same as your server's own SSH host key, and it is **not** the same as a Git deploy key. The WPGrip workspace key is used solely for WPGrip to log in to your server as the SSH user you specify.

::: info WPGrip key vs. server key vs. deploy key
- **WPGrip workspace key** → authorises WPGrip to SSH into your server
- **Server host key** → your server's identity (you don't manage this)
- **Git deploy key** → authorises your server to pull from a Git repository (covered in the [Repositories guide](/guide/adding-a-repository))

Don't mix these up — each serves a different purpose.
:::

## Step 1: Main Info

Navigate to **Sites → Add Site**. The first screen of the wizard collects connection details.

### SSH Key Callout

At the very top of the form you'll see a prominent amber box:

> **SSH Key Setup**
>
> Copy the key below and add it to `~/.ssh/authorized_keys` on your server for the SSH user.

Below the message is your workspace's public key in a copyable block, followed by a one-liner command:

```bash
echo "ssh-rsa AAAA...your-workspace-public-key..." >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys
```

::: warning Run this as the correct user
The command must be run **as the SSH user** you intend to enter in the form — not as `root` (unless root *is* your SSH user). If you add the key to root's `authorized_keys` but enter `deploy` as the SSH user, the connection will fail.
:::

### Installing the Key — By Environment

#### Standard Ubuntu / Debian Server

```bash
# SSH into your server as the user that owns the WordPress files
ssh deploy@your-server.com

# Create the .ssh directory if it doesn't exist
mkdir -p ~/.ssh

# Append the WPGrip public key
echo "ssh-rsa AAAA...your-workspace-public-key..." >> ~/.ssh/authorized_keys

# Fix permissions (SSH is strict about this)
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
```

::: tip Permissions matter
OpenSSH refuses to use `authorized_keys` if the file or directory permissions are too open. Always set `700` on `~/.ssh` and `600` on `~/.ssh/authorized_keys`. The home directory itself should be `755` or stricter.
:::

#### cPanel

1. Log in to **cPanel** for the account that owns the WordPress installation
2. Navigate to **Security → SSH Access → Manage SSH Keys**
3. Click **Import Key**
4. Leave the key name as default
5. Paste the WPGrip public key into the **Public Key** field
6. Click **Import**
7. Back on the key list, click **Manage** next to the imported key and then **Authorize**

::: warning cPanel authorization step
cPanel imports keys in a *disabled* state. You **must** click Authorize after importing — this is the most common mistake on cPanel servers.
:::

#### Plesk

1. Log in to **Plesk**
2. Go to **Server Management → Tools & Settings → SSH Keys**
3. Click **Add Key**
4. Paste the WPGrip public key
5. Save and ensure the key is associated with the correct system user

#### RunCloud

1. Log in to **RunCloud**
2. Go to **Servers → (your server) → System Users**
3. Click the user that owns the WordPress site
4. Scroll to **SSH Keys** and click **Add SSH Key**
5. Paste the WPGrip public key and save

#### GridPane / SpinupWP / CloudPanel

Most modern server panels have an **SSH Keys** section under system user settings. Add the WPGrip public key there. Consult your panel's documentation if you can't find it.

### Common SSH Key Mistakes

| Mistake | Symptom | Fix |
|---------|---------|-----|
| Key added to wrong user | `Permission denied (publickey)` | Add the key to the `authorized_keys` of the **SSH user** entered in WPGrip |
| Missing Authorize step (cPanel) | `Permission denied (publickey)` | Go to SSH Access → Manage → Authorize |
| Wrong file permissions | `Permission denied (publickey)` | `chmod 700 ~/.ssh && chmod 600 ~/.ssh/authorized_keys` |
| Key pasted with line breaks | `Permission denied (publickey)` | The entire key must be on **one line** — no wrapping |
| SELinux blocking access | `Permission denied (publickey)` | Run `restorecon -Rv ~/.ssh` |
| Firewall blocks WPGrip IPs | Connection timeout | Allow inbound SSH from WPGrip's IP ranges |

### Form Fields

After the SSH key callout, fill in these fields:

| Field | Required | Description | Example |
|-------|----------|-------------|---------|
| **Staging site toggle** | No | "Will this be a staging site?" — if enabled, a dropdown appears to select the parent production site | — |
| **Name** | Yes | Friendly name to help you identify this site in the dashboard | `My Blog` |
| **URL** | Yes | Full URL of the website including `https://` | `https://myblog.com` |
| **SSH User** | Yes | The Unix user who owns the WordPress directory and can log in via SSH | `deploy` |
| **Dir Path** | Yes | Absolute filesystem path to the WordPress installation | `/var/www/html` |

::: tip Finding the Dir Path
SSH into your server and run:
```bash
wp option get siteurl --path=/var/www/html
```
If it returns your site's URL, that's the correct path. You can also run `pwd` in the directory that contains `wp-config.php`.
:::

::: warning Staging sites
If you enable the staging toggle, the site is flagged as staging and linked to a production site. Staging sites are excluded from certain features like scheduled backups. Only toggle this if the site is genuinely a staging/dev copy.
:::

Click **Next** to proceed to Step 2.

## Step 2: Client and Server

The second screen associates the site with a client and a server.

### Client

Select an existing client from the dropdown, or click **Create New Client** to open an inline form:

| Field | Required | Description |
|-------|----------|-------------|
| **Name** | Yes | Client's name or company name |
| **Email** | No | Contact email |
| **Country** | No | Client's country |
| **Notes** | No | Free-text notes about this client |

::: info For freelancers and solo users
If you manage your own sites, just create a single client record for yourself. The client field is required but designed for agency workflows.
:::

### Server

Select an existing server from the dropdown, or click **Create New Server** to open an inline form:

| Field | Required | Description | Example |
|-------|----------|-------------|---------|
| **Name** | Yes | Friendly server name | `Production DO Droplet` |
| **IP** | Yes | Server IP address | `167.71.45.123` |
| **SSH Port** | Yes | SSH port number | `22` |
| **Notes** | No | Free-text notes | `8GB RAM, NYC datacenter` |
| **Hosting Provider** | No | Where the server is hosted | `DigitalOcean` |
| **Server Type** | No | Server category | `VPS` |

::: tip Reuse servers across sites
If you host multiple WordPress sites on one server, create the server record once and reuse it. This keeps your infrastructure view clean and accurate.
:::

## Finishing Up

Click **Create** to save the site. WPGrip will:

1. **Test the SSH connection** — authenticates using your workspace key
2. **Verify WP-CLI** — runs a quick WP-CLI check on the server
3. **Run the first sync** — pulls WordPress version, plugins, themes, and configuration
4. **Start uptime monitoring** — begins checking every 60 seconds immediately
5. **Check SSL certificate** — reads certificate expiry and validity

You'll be redirected to the new site's overview page. If any step fails, you'll see an error message with specific guidance.

## After Adding Your Site

Once connected, explore these next steps:

- [Set up database backups](/guide/creating-backups) to protect your data
- [Connect a Git repository](/guide/connecting-repository-to-site) for code deployment
- [Review monitoring data](/guide/monitoring-setup) as it accumulates

## Troubleshooting

### "Connection failed" on save

Double-check every field — the most common causes are:

1. SSH key not installed (or installed for the wrong user)
2. Incorrect SSH port (some hosts use `2222`, `7822`, or other non-standard ports)
3. IP address is wrong or the server is behind a firewall
4. The Dir Path doesn't contain a WordPress installation

### "WP-CLI not found"

WPGrip requires WP-CLI on the server. Install it:

```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
wp --info  # verify installation
```

### Site shows as "Disconnected" after initial success

This usually means the SSH key was removed, the server was rebooted with new firewall rules, or the SSH user was modified. Re-add the WPGrip public key and verify the connection from the site's settings page.
