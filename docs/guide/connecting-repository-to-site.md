# Connecting a Repository to a Site

Once you've [added a repository](/guide/adding-a-repository) to your WPGrip workspace, the next step is to connect it to one or more sites. This tells WPGrip where to deploy the code and which branch to track.

## Prerequisites

- A repository already added to your workspace (see [Adding a Repository](/guide/adding-a-repository))
- At least one site connected to WPGrip
- The server's SSH deploy key added to your Git provider (GitHub or Bitbucket)
- **Pro plan** or higher

## Step 1: Open the Repository Detail Page

1. Navigate to **Repositories** in the sidebar
2. Click the repository you want to connect
3. Scroll down to the **Connected sites** section
4. Click **Connect a site**

## Step 2: Review the Deploy Key Reminder

At the top of the connect form, you'll see a blue info callout:

::: info Deploy Key Required
Before connecting, make sure the server's SSH public key is added as a **deploy key** on your Git provider. Without this, the server cannot pull code from the repository. See [Adding a Repository → Step 2](/guide/adding-a-repository#step-2-add-the-deploy-key-to-your-git-provider) for setup instructions.
:::

This is a reminder that the **server** (not WPGrip) needs its own SSH key registered with GitHub or Bitbucket. If you haven't done this yet, stop here and set it up first.

## Step 3: Fill in the Connection Form

The form has three fields:

| Field | Required | Description |
|-------|----------|-------------|
| **Site** | Yes | Dropdown of workspace sites not already connected to this repository |
| **Relative path to deploy** | Yes | Path inside the WordPress installation where the code should live |
| **Branch to use** | Yes | The Git branch to deploy (e.g., `main`, `master`, `production`) |

### Choosing the Deploy Path

The deploy path is **relative to the WordPress root directory** — the Dir Path you configured when adding the site.

::: warning The directory must not already exist
The deploy path directory will be created automatically on the first deploy using `git clone`. If the directory already exists, the initial clone will fail. Remove or rename the existing directory before the first deploy.
:::

Common deploy paths by repository type:

| Repository Type | Deploy Path Example | Full Path on Server |
|----------------|--------------------|---------|
| Theme | `wp-content/themes/your-theme` | `/var/www/html/wp-content/themes/your-theme` |
| Plugin | `wp-content/plugins/your-plugin` | `/var/www/html/wp-content/plugins/your-plugin` |
| Other | `wp-content/mu-plugins` | `/var/www/html/wp-content/mu-plugins` |

::: tip Match the directory name to your theme/plugin
The last segment of the deploy path becomes the directory name. For themes, this should match the theme's slug. For plugins, it should match the plugin's folder name.
:::

### Choosing the Branch

Enter the exact branch name from your Git repository. Common choices:

| Branch | Use case |
|--------|----------|
| `main` or `master` | Default branch — deploy the latest stable code |
| `production` | If you use a production-specific branch |
| `staging` | For staging sites tracking a pre-release branch |
| `develop` | For development sites tracking ongoing work |

::: info Any branch works
You can enter any existing branch name. WPGrip doesn't validate the branch at connection time — it's used during the first deploy when `git clone -b <branch>` is executed.
:::

## Step 4: Save the Connection

Click **Connect** to save. The site now appears in the **Connected sites** table on the repository detail page.

## The Connected Sites Table

After connecting one or more sites, the table displays:

| Column | Description |
|--------|-------------|
| **Site name** | The connected site with a link to its detail page |
| **Path + Branch** | Deploy path and active branch (e.g., `wp-content/themes/flavor → main`) |
| **Status** | Last deployment result: `Success`, `Failed`, or `Pending` |
| **Last status log** | Brief output from the last deployment (commit hash, error message, etc.) |
| **Push to Deploy** | Toggle to enable automatic deploys on `git push` |
| **Last activity** | Timestamp of the most recent deployment |

## Available Actions

Each connected site has a set of actions:

### Deploy

Manually trigger a deployment. WPGrip SSHes into the server and runs `git pull` in the deploy directory. See [Deploying with Git](/guide/deploying-with-git) for full details.

### Change Branch

Switch the connected site to a different branch. This updates the tracked branch — the next deploy (manual or automatic) will pull from the new branch. On the server, WPGrip runs `git checkout <new-branch> && git pull`.

::: warning Branch switching resets local changes
Changing branches discards any uncommitted changes in the deploy directory on the server. If you've made manual edits to deployed files, they will be lost.
:::

### Revert Commit

Roll back to a previous commit. You can select from the last 5 deployments or enter a custom commit hash. See [Deploying with Git → Reverting](/guide/deploying-with-git#reverting-a-deployment) for details.

### Detach

Remove the connection between this repository and the site. This does **not** delete the deployed files from the server — it only removes the link in WPGrip. Future pushes to the repository will no longer trigger deploys to this site.

::: tip Files remain on the server
Detaching a repository leaves the cloned code in place on the server. If you want to remove the files, SSH into the server and delete the directory manually.
:::

## Push to Deploy

The **Push to Deploy** toggle enables automatic deployments whenever you push to the tracked branch. When enabled:

1. WPGrip provides a **webhook URL** for the repository
2. You add this webhook to your Git provider
3. Every push to the tracked branch triggers an automatic deploy to the connected site

See [Deploying with Git → Webhook Setup](/guide/deploying-with-git#webhook-setup) for step-by-step webhook configuration.

::: info One webhook, multiple sites
If the same repository is connected to multiple sites with Push to Deploy enabled, a single push triggers deploys to **all** connected sites simultaneously.
:::

## Connecting Multiple Sites

You can connect the same repository to as many sites as you need. This is useful when:

- You maintain a custom theme used across multiple client sites
- You have a plugin deployed to production and staging environments
- You manage a network of sites sharing a common codebase

Each connection can have a **different branch**, so you can deploy `main` to production and `staging` to your staging site from the same repository.

## Troubleshooting

### "Directory already exists" on first deploy

The deploy path directory already exists on the server. Either:

- Rename/remove the existing directory: `mv wp-content/themes/my-theme wp-content/themes/my-theme-backup`
- Choose a different deploy path

### "Permission denied" during deploy

The server's deploy key is not registered with the Git provider. See [Adding a Repository → Step 2](/guide/adding-a-repository#step-2-add-the-deploy-key-to-your-git-provider).

### Site not appearing in the dropdown

A site won't appear in the connection dropdown if it's already connected to this repository. Each site can only be connected to a given repository once.

### Deploy succeeds but theme/plugin doesn't appear in WordPress

Check that the deploy path is correct. The most common mistake is an extra or missing directory level. SSH into the server and verify:

```bash
ls -la /var/www/html/wp-content/themes/your-theme/style.css   # for themes
ls -la /var/www/html/wp-content/plugins/your-plugin/your-plugin.php  # for plugins
```

If the main file is nested one level too deep, adjust the deploy path.
