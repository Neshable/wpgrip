# SSH Connection

WPGrip connects to your servers using SSH key-based authentication. This page covers how keys work, setup instructions, and troubleshooting.

## How It Works

Each WPGrip workspace gets a unique SSH key pair:

- **Private key** — stored encrypted in WPGrip, never leaves our servers
- **Public key** — you add this to your server’s `authorized_keys`

When WPGrip needs to interact with your site, it opens an SSH connection using your private key, runs WP-CLI commands, and closes the connection.

## Finding Your Public Key

1. Open your WPGrip dashboard
2. Go to **Settings → SSH Keys**
3. Click **Copy** next to your public key

## Adding the Key to Your Server

### Standard Linux/Ubuntu Server

```bash
ssh your-user@your-server.com
mkdir -p ~/.ssh
nano ~/.ssh/authorized_keys
# Paste the WPGrip public key on a new line, save
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
```

### cPanel / WHM

1. Log in to cPanel
2. Go to **Security → SSH Access → Manage SSH Keys**
3. Click **Import Key**
4. Paste the WPGrip public key
5. Click **Authorize**

### Plesk

1. Log in to Plesk
2. Go to **Server Management → Tools & Settings → SSH Keys**
3. Add the WPGrip public key

### RunCloud / ServerPilot / GridPane

Most server management panels have an SSH key section in the system user settings. Add the WPGrip public key there.

## Troubleshooting

### Permission Denied

| Cause | Fix |
|-------|-----|
| Key not added to correct user | Ensure the key is in the SSH user’s `~/.ssh/authorized_keys`, not root’s |
| Wrong file permissions | `chmod 700 ~/.ssh && chmod 600 ~/.ssh/authorized_keys` |
| Home directory permissions | `chmod 755 /home/username` |
| SELinux blocking | `restorecon -Rv ~/.ssh` |

### Connection Timeout

- Verify the SSH port is correct (some hosts use 2222 or others)
- Check if a firewall is blocking inbound SSH
- Ensure the server is reachable from the internet

### WP-CLI Not Found

WPGrip requires WP-CLI on the server. Install it:

```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

::: tip
WPGrip automatically detects the WP-CLI binary path. If it’s installed in a non-standard location, ensure it’s in the SSH user’s `$PATH`.
:::
