# Deploying with Git

WPGrip lets you deploy code from GitHub or Bitbucket repositories directly to your WordPress sites over SSH. This guide covers every deployment method — manual, automatic, bulk — plus reverting, branch switching, and webhook configuration.

## Prerequisites

- A repository [added to your workspace](/guide/adding-a-repository)
- The repository [connected to at least one site](/guide/connecting-repository-to-site)
- The server's deploy key registered with your Git provider
- **Pro plan** or higher

## How Deployment Works

Every deployment follows the same sequence regardless of how it's triggered:

1. WPGrip opens an SSH connection to your server using the workspace key
2. WPGrip navigates to the deploy path inside your WordPress installation
3. If this is the **first deploy**, WPGrip runs `git clone -b <branch> <remote-url> <path>`
4. For **subsequent deploys**, WPGrip runs `git pull origin <branch>`
5. WPGrip records the result — commit hash, message, status, and duration
6. The SSH connection is closed

::: info WPGrip never touches your remote repository
Deployments are **pull-only**. WPGrip pulls code from your Git provider to your server. It never pushes, modifies branches, or writes to your repository.
:::

## Manual Deployment

The simplest way to deploy — trigger it yourself with one click.

1. Navigate to **Repositories** in the sidebar
2. Click the repository you want to deploy
3. In the **Connected sites** table, find the target site
4. Click the **Deploy** button

WPGrip runs the deployment in the background. The **Status** column updates to show the result:

| Status | Meaning |
|--------|---------|
| **Success** | `git pull` completed without errors |
| **Failed** | An error occurred — check the status log for details |
| **Pending** | Deployment is queued and will run momentarily |

The **Last status log** column shows a summary including the new commit hash and commit message.

::: tip First deploy takes longer
The first deployment to a site performs a full `git clone`, which downloads the entire repository history. Subsequent deploys run `git pull` and only download new changes — these are much faster.
:::

## Automatic Deployment (Push to Deploy)

Push to Deploy triggers a deployment automatically every time you push to the tracked branch. No manual intervention needed.

### Enabling Push to Deploy

1. Go to the repository detail page
2. In the **Connected sites** table, find the site
3. Toggle **Push to Deploy** to on

Once enabled, WPGrip generates a **webhook URL** for the repository. You need to register this webhook with your Git provider so it can notify WPGrip of new pushes.

### Webhook Setup

After enabling Push to Deploy, copy the webhook URL from WPGrip and add it to your Git provider.

#### GitHub

1. Go to your repository on GitHub
2. Click **Settings** → **Webhooks** → **Add webhook**
3. Configure the webhook:

| Field | Value |
|-------|-------|
| **Payload URL** | Paste the webhook URL from WPGrip |
| **Content type** | `application/json` |
| **Secret** | Leave empty |
| **Which events?** | Select **Just the push event** |
| **Active** | Checked |

4. Click **Add webhook**

GitHub will send a test ping. You can verify delivery in the **Recent Deliveries** tab.

#### Bitbucket

1. Go to your repository on Bitbucket
2. Click **Repository settings** → **Webhooks** → **Add webhook**
3. Configure the webhook:

| Field | Value |
|-------|-------|
| **Title** | `WPGrip Deploy` (or any name) |
| **URL** | Paste the webhook URL from WPGrip |
| **Triggers** | Select **Repository push** |

4. Click **Save**

::: warning Only the tracked branch triggers deploys
Push to Deploy only fires when the push targets the branch configured for that site connection. Pushing to other branches is ignored. For example, if a site tracks `main`, pushes to `develop` will not trigger a deploy.
:::

### Push to Deploy with Multiple Sites

If the same repository is connected to multiple sites with Push to Deploy enabled, a single push to the tracked branch triggers deployments to **all** of those sites simultaneously. Each site deploys independently — a failure on one site does not affect the others.

## Bulk Deployment

When a repository is connected to many sites, you can deploy to several at once instead of clicking Deploy on each row individually.

1. Go to the repository detail page
2. In the **Connected sites** table, select the checkboxes next to the sites you want to deploy to
3. Click the **Deploy selected** button above the table

WPGrip queues a deployment job for each selected site. Deployments run in parallel and each reports its own status independently.

::: tip Use bulk deploy for coordinated rollouts
If you maintain a plugin used across 20 client sites, bulk deploy lets you push the update to all of them in one action after verifying the code on a staging site.
:::

## Deployment Logs

Every deployment is logged with full details. To view the history:

1. Go to the repository detail page
2. Check the **Connected sites** table — each row shows the last deployment status
3. Click a site row to see the full deployment history for that connection

Each log entry includes:

| Field | Description |
|-------|-------------|
| **Commit hash** | The short SHA of the deployed commit |
| **Commit message** | The message from the deployed commit |
| **Status** | `Success` or `Failed` |
| **Duration** | How long the deployment took |
| **Timestamp** | When the deployment ran |
| **Trigger** | `Manual`, `Push to Deploy`, or `Bulk` |

::: info Failed deployment logs
When a deployment fails, the status log contains the Git error output. Common errors include `Permission denied` (deploy key issue), `branch not found`, or merge conflicts from manual edits on the server.
:::

## Reverting a Deployment

If a deployment introduces a problem, you can revert to a previous commit directly from WPGrip.

1. Go to the repository detail page
2. In the **Connected sites** table, click **Revert Commit** for the affected site
3. Choose a target:
   - **Recent deployments** — select from the last 5 deployed commits
   - **Custom hash** — paste any full or short commit hash from the repository
4. Click **Revert**

### What Happens During a Revert

WPGrip SSHes into the server and runs a `git reset --hard <commit>` in the deploy directory. This:

- Moves the deploy directory to the exact state of that commit
- Discards any local changes on the server
- Does **not** affect the remote repository — your Git history is untouched

::: danger Revert is a hard reset
Reverting uses `git reset --hard`, which is destructive to the local working directory. Any files that were modified directly on the server (outside of Git) will be lost. The remote repository is never affected.
:::

::: warning Push to Deploy after reverting
If Push to Deploy is enabled and you revert, the next push to the tracked branch will deploy the latest commit again — potentially re-introducing the code you just reverted. Disable Push to Deploy temporarily while you fix the issue in your repository, then re-enable it once the fix is pushed.
:::

## Changing the Deployed Branch

You can switch which branch is deployed to a site without detaching and reconnecting.

1. Go to the repository detail page
2. In the **Connected sites** table, click **Change Branch** for the target site
3. Enter the new branch name (e.g., switch from `main` to `hotfix/urgent-fix`)
4. Click **Save**

On the next deployment (manual or automatic), WPGrip runs `git checkout <new-branch> && git pull` on the server.

::: tip Common workflow: deploy a hotfix branch
1. Create a `hotfix/issue-123` branch in your repository
2. Change the production site's branch to `hotfix/issue-123`
3. Deploy manually to push the fix live
4. Once verified, merge the hotfix into `main` in your repository
5. Change the site's branch back to `main`
6. Deploy again to resume normal tracking
:::

## Deployment Best Practices

### Use staging sites to verify before production

Connect the same repository to both a staging and production site. Track the `develop` or `staging` branch on staging, and `main` on production. Test changes on staging before merging to `main`.

### Keep deploy directories clean

Do not manually edit files in the deploy directory on the server. Git tracks the state of these files — manual edits create conflicts that cause `git pull` to fail.

```bash
# If you need to reset a dirty deploy directory
cd /var/www/html/wp-content/themes/your-theme
git reset --hard HEAD
git clean -fd
```

### Monitor deployment logs

Check deployment status after each release, especially for Push to Deploy setups. A silent failure can leave a site running stale code.

## Troubleshooting

### "Permission denied (publickey)" during deploy

The server cannot authenticate with the Git provider. The server's deploy key is missing or incorrect. See [Adding a Repository → Step 2](/guide/adding-a-repository#step-2-add-the-deploy-key-to-your-git-provider).

### "Merge conflict" or "Your local changes would be overwritten"

Someone (or something) modified files in the deploy directory directly on the server. Reset the working directory:

```bash
ssh deploy@your-server.com
cd /var/www/html/wp-content/themes/your-theme
git reset --hard HEAD
git clean -fd
```

Then retry the deployment from WPGrip.

### Push to Deploy not firing

Verify the webhook is configured correctly:

1. **GitHub**: Go to Settings → Webhooks — check for recent delivery failures
2. **Bitbucket**: Go to Settings → Webhooks — check that the trigger is "Repository push"
3. Confirm the Push to Deploy toggle is **on** in WPGrip
4. Confirm you pushed to the **correct branch** (the one the site tracks)

### Deploy succeeds but site shows old code

This is usually a caching issue, not a deployment issue. Clear your:

- **WordPress object cache**: `wp cache flush`
- **Page cache plugin**: Purge from the plugin's settings
- **Server-level cache**: OPcache, Varnish, Nginx FastCGI cache
- **CDN cache**: Cloudflare, Fastly, etc.

::: tip OPcache specifically
PHP's OPcache caches compiled PHP files. After deploying updated `.php` files, OPcache may still serve the old versions. Restart PHP-FPM or clear OPcache:
```bash
sudo systemctl restart php8.2-fpm
```
:::
