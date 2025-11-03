# GitHub Actions Deployment Setup

This document explains how to configure GitHub Actions secrets for automatic deployment to production.

## Required GitHub Secrets

Navigate to your repository on GitHub:
1. Go to **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret** for each of the following:

### 1. SSH_PRIVATE_KEY
The private SSH key for authenticating with the production server.

**How to get it:**
```bash
# On your local machine or the production server
cat ~/.ssh/id_rsa
# Or if using a different key:
cat ~/.ssh/your_deploy_key
```

Copy the entire content including the `-----BEGIN` and `-----END` lines.

**Value:** The complete private key (multi-line)

---

### 2. SSH_HOST
The production server IP address.

**Value:** `62.113.231.168`

---

### 3. SSH_USER
The username for SSH connection.

**Value:** `web-user`

---

### 4. DEPLOY_PATH
The absolute path to the application on the production server.

**Value:** `~/docs.magento-opensource.com`

---

## Server Prerequisites

Ensure the production server has:

1. **Git repository initialized** in `~/docs.magento-opensource.com`
   ```bash
   cd ~/docs.magento-opensource.com
   git init
   git remote add origin https://github.com/magentoopensource/docs-website.git
   git pull origin main
   ```

2. **SSH key authorized** for `web-user`
   ```bash
   # Add the public key to authorized_keys
   echo "your-public-key-here" >> ~/.ssh/authorized_keys
   chmod 600 ~/.ssh/authorized_keys
   ```

3. **Required software installed:**
   - PHP 8.3+
   - Composer
   - Node.js 20+ (LTS)
   - npm
   - Git

4. **Proper permissions:**
   ```bash
   # Ensure web-user owns the deployment directory
   sudo chown -R web-user:web-user ~/docs.magento-opensource.com
   ```

5. **Environment file (.env) configured** with production settings

---

## Deployment Workflow

The deployment automatically triggers on every push to the `main` branch and performs:

1. ✅ Checkout code
2. ✅ Setup SSH connection
3. ✅ Pull latest changes from `main`
4. ✅ Install Composer dependencies (production mode)
5. ✅ Install NPM dependencies
6. ✅ Build production assets
7. ✅ Sync documentation from external repo
8. ✅ Clear Laravel caches
9. ✅ Optimize Laravel (cache config, routes, views)

---

## Testing the Deployment

After setting up the secrets:

1. Make a small change to any file
2. Commit and push to `main`
3. Go to **Actions** tab in GitHub to watch the deployment
4. Verify the changes are live on production

---

## Troubleshooting

### Deployment fails with "Permission denied (publickey)"
- Verify `SSH_PRIVATE_KEY` is correct and matches the public key on the server
- Ensure the public key is in `~/.ssh/authorized_keys` on the production server

### Deployment fails with "fatal: not a git repository"
- Initialize git repo on production server (see Server Prerequisites #1)

### Deployment fails during composer/npm install
- SSH into production and manually run the commands to see detailed errors
- Check that Composer and npm are installed and accessible

### Assets not updating after deployment
- Check that `npm run build` completed successfully in the Actions log
- Verify the `public/build` directory exists and has correct permissions

---

## Security Notes

- Never commit the private SSH key to the repository
- Keep GitHub secrets access restricted to trusted maintainers only
- Regularly rotate SSH keys
- Consider using a dedicated deploy key instead of a user's personal SSH key

---

## Manual Deployment (Fallback)

If GitHub Actions is unavailable, deploy manually:

```bash
ssh web-user@62.113.231.168
cd ~/docs.magento-opensource.com
git pull origin main
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm ci
npm run build
bash bin/checkout_latest_docs.sh
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
