# Git Deployments

WPGrip supports push-to-deploy from GitHub, GitLab, and Bitbucket. Connect a repository, link it to one or more sites, and deploy on push or on demand.

## Requirements

- A **Pro** plan or higher (Git deployments are not available on Basic)
- A Git repository on GitHub, GitLab, or Bitbucket
- SSH write access on the target server(s)

## Setting Up Deployments

### 1. Connect a Repository

1. Go to **Repositories** in the sidebar
2. Click **Add Repository**
3. Enter the repository’s SSH clone URL (e.g., `git@github.com:org/theme.git`)
4. Select the branch to deploy (e.g., `main`)

### 2. Link to Sites

1. Open the repository
2. Click **Connect Site**
3. Select one or more sites to deploy to
4. Set the deploy path on each site (where the repo should be pulled)

### 3. Configure Webhooks

WPGrip provides a unique webhook URL for each repository. Add it to your Git provider:

**GitHub:**
1. Go to repo **Settings → Webhooks**
2. Add the WPGrip webhook URL
3. Set content type to `application/json`
4. Select “Just the push event”

**Bitbucket:**
1. Go to repo **Settings → Webhooks**
2. Add the WPGrip webhook URL
3. Select the **Repository push** trigger

**GitLab:**
1. Go to repo **Settings → Webhooks**
2. Add the WPGrip webhook URL
3. Select the **Push events** trigger

## How Deployment Works

1. You push to the configured branch
2. Your Git provider sends a webhook to WPGrip
3. WPGrip SSHs into each linked site
4. Runs `git pull` in the deploy path
5. Records the deployment result (success/failure, commit SHA, duration)

## Deployment History

Each repository shows a full deployment log:

- Commit SHA and message
- Deploy status (success / failed)
- Duration
- Which sites were deployed to
- Timestamp

The last 50 deployments per site-repository connection are retained.

## Manual Deploy

You can trigger a deployment manually from the repository page without pushing a commit. This pulls the latest commit from the configured branch.

## Reverting

If a deployment breaks something, you can revert to a previous commit from the deployment log.

::: warning
Git deployments require the SSH user to have write access to the deploy path on the target server.
:::
