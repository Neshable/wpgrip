# Adding a Repository

Repositories in WPGrip let you deploy themes, plugins, and other code directly from Git to any connected site. This guide covers how to add a repository and configure access so your servers can pull code.

## Prerequisites

- A **Pro plan** or higher (repositories are not available on the Basic plan)
- A Git repository hosted on **GitHub** or **Bitbucket**
- SSH access to the server(s) where you'll deploy the code
- At least one site already connected to WPGrip

::: info Plan requirement
Git repository management requires a Pro, Agency, or Enterprise plan. If you're on the Basic plan, upgrade from **Settings → Billing** before proceeding.
:::

## Understanding the SSH Key Chain

Deploying code via Git involves **two separate SSH keys**. Confusing them is the most common source of problems.

| Key | Who has the private key | Who has the public key | Purpose |
|-----|------------------------|----------------------|----------|
| **WPGrip workspace key** | WPGrip | Your server (`authorized_keys`) | WPGrip SSHes into your server |
| **Server deploy key** | Your server | GitHub/Bitbucket (deploy keys) | Your server pulls code from Git |

When you click Deploy, WPGrip SSHes into your server (using the workspace key) and then runs `git pull` on the server. That `git pull` command authenticates with GitHub or Bitbucket using the **server's own SSH key** — not WPGrip's key.

::: danger The server needs its own key in GitHub/Bitbucket
WPGrip does **not** proxy Git traffic. Your server talks directly to the Git provider. If the server doesn't have an SSH key registered as a deploy key on the repository, `git pull` will fail with `Permission denied (publickey)`.
:::

## Step 1: Generate a Deploy Key on Your Server

SSH into the server that will run the deployments and generate a key pair for the user that owns the WordPress files:

```bash
# SSH into your server
ssh deploy@your-server.com

# Generate an Ed25519 key (recommended)
ssh-keygen -t ed25519 -C "deploy-key-for-my-repo" -f ~/.ssh/id_ed25519

# Or RSA if Ed25519 is not supported
ssh-keygen -t rsa -b 4096 -C "deploy-key-for-my-repo" -f ~/.ssh/id_rsa
```

When prompted for a passphrase, press Enter for no passphrase (deploy keys must be passphrase-free for automated deployments).

::: tip Already have a key?
If the server already has an SSH key pair, you can reuse it. Check with:
```bash
ls -la ~/.ssh/id_*.pub
```
If a `.pub` file exists, you can use that key — no need to generate a new one.
:::

Copy the public key:

```bash
cat ~/.ssh/id_ed25519.pub
```

You'll need this in the next step.

## Step 2: Add the Deploy Key to Your Git Provider

### GitHub

1. Go to your repository on GitHub
2. Click **Settings** (repository settings, not account settings)
3. In the left sidebar, click **Deploy keys**
4. Click **Add deploy key**
5. Enter a title (e.g., `WPGrip Production Server`)
6. Paste the server's **public key**
7. Leave "Allow write access" **unchecked** (read-only is sufficient for pulls)
8. Click **Add key**

### Bitbucket

1. Go to your repository on Bitbucket
2. Click **Repository settings** in the left sidebar
3. Under **Security**, click **Access keys**
4. Click **Add key**
5. Enter a label (e.g., `WPGrip Production Server`)
6. Paste the server's **public key**
7. Click **Add SSH key**

::: info Multiple servers?
If you deploy the same repository to multiple servers, each server needs its own deploy key added to the Git provider. GitHub allows multiple deploy keys per repository. For Bitbucket, the same key can be used across repositories in the same workspace.
:::

## Step 3: Verify Server Access to Git

Before adding the repository in WPGrip, confirm that your server can reach the Git provider:

```bash
# For GitHub
ssh -T git@github.com
# Expected: "Hi username! You've successfully authenticated..."

# For Bitbucket
ssh -T git@bitbucket.org
# Expected: "logged in as username."
```

If you see `Permission denied`, the deploy key is not set up correctly. Double-check that:

- The **public** key (not private) was added to the Git provider
- The key file has correct permissions: `chmod 600 ~/.ssh/id_ed25519`
- The `~/.ssh` directory has correct permissions: `chmod 700 ~/.ssh`

## Step 4: Add the Repository in WPGrip

Now add the repository to your WPGrip workspace:

1. Navigate to **Repositories** in the sidebar
2. Click **Add Repository**
3. Fill in the form:

| Field | Required | Description | Example |
|-------|----------|-------------|---------|
| **Type** | Yes | What kind of code this repository contains | `Plugin`, `Theme`, or `Other` |
| **Name** | Yes | Friendly name to find the repository faster | `My Custom Theme` |
| **GIT Remote URL** | Yes | The SSH clone URL of the repository | `git@github.com:user/repo.git` |
| **Provider** | Yes | Visual selector — choose GitHub or Bitbucket | GitHub or Bitbucket |

4. Click **Create**

### Why SSH URL, Not HTTPS?

The Git Remote URL **must** be in SSH format:

```
✅ git@github.com:username/my-theme.git
✅ git@bitbucket.org:workspace/my-plugin.git

❌ https://github.com/username/my-theme.git
❌ https://bitbucket.org/workspace/my-plugin.git
```

::: warning HTTPS URLs will not work
HTTPS Git URLs require username/password or token authentication on every pull. WPGrip uses SSH-based Git operations exclusively because they work with deploy keys and don't require storing credentials on your server.
:::

### Where to Find the SSH URL

**GitHub:** On the repository page, click the green **Code** button, select the **SSH** tab, and copy the URL.

**Bitbucket:** On the repository page, click **Clone**, switch to **SSH**, and copy the URL (remove the `git clone` prefix).

## After Adding the Repository

The repository is now registered in WPGrip, but it's not connected to any site yet. The next step is to [connect the repository to a site](/guide/connecting-repository-to-site) so you can start deploying.

From the repository detail page, you can:

- View and edit repository settings
- Connect sites for deployment
- Monitor deployment history across all connected sites

## Troubleshooting

### "Permission denied" during deploy

This almost always means the server's deploy key is not registered with the Git provider. Follow [Step 2](#step-2-add-the-deploy-key-to-your-git-provider) above.

### "Host key verification failed"

The server hasn't connected to the Git provider before and doesn't have it in `known_hosts`. Fix it by running once on the server:

```bash
# For GitHub
ssh-keyscan github.com >> ~/.ssh/known_hosts

# For Bitbucket
ssh-keyscan bitbucket.org >> ~/.ssh/known_hosts
```

### Repository type can't be changed after creation

The type (Plugin, Theme, Other) is set at creation time and determines the default deploy paths suggested when connecting sites. If you picked the wrong type, delete the repository and recreate it.

::: tip Private repositories
Deploy keys work with both public and private repositories. There is no difference in setup — the deploy key grants read access regardless of repository visibility.
:::
