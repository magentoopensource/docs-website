# Deployer Setup Guide - Fresh Production Deployment

This guide walks through setting up production deployment with Deployer from scratch.

## What You Get

Deployer provides:
- ✅ **Atomic deployments** - Site never in a broken state
- ✅ **Zero-downtime** - Old version serves until new one is ready
- ✅ **Instant rollbacks** - One command to revert bad deploys
- ✅ **Persistent shared files** - Logs, .env, external docs survive deploys
- ✅ **Release history** - Keep last 5 releases automatically

## Directory Structure

Deployer creates this structure on production:

```
~/docs.magento-opensource.com/
├── .dep/              # Deployer metadata
├── releases/          # All releases
│   ├── 1/            # First deployment
│   ├── 2/            # Second deployment
│   └── 3/            # Third deployment (current)
├── shared/           # Shared across releases
│   ├── .env          # Environment config
│   ├── storage/      # Logs, cache, sessions
│   └── resources/docs/  # External documentation
└── current -> releases/3  # Symlink to active release
```

Web server points to: `~/docs.magento-opensource.com/current/public`

## Prerequisites

1. ✅ SSH access to production as `web-user@62.113.231.168`
2. ✅ SSH key pair for authentication
3. ✅ Node.js 20+ (LTS) installed on production
4. ✅ PHP 8.3+ installed on production
5. ✅ Composer installed on production
6. ✅ Git installed on production
7. ✅ Production `.env` file ready with all configuration

## Step 1: Add SSH Key to GitHub Secrets

1. Go to your GitHub repository
2. Navigate to **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add `SSH_PRIVATE_KEY` with your private key content:

```bash
# Get your private key
cat ~/.ssh/id_rsa
# Or if using a specific deploy key:
cat ~/.ssh/deploy_key
```

Copy the entire content including `-----BEGIN` and `-----END` lines.

## Step 2: Prepare Production Server

SSH into production and create the deployment directory structure:

```bash
ssh web-user@62.113.231.168

# Create main deployment directory
mkdir -p ~/docs.magento-opensource.com

# Create shared directory structure
mkdir -p ~/docs.magento-opensource.com/shared/storage/{app,framework,logs}
mkdir -p ~/docs.magento-opensource.com/shared/storage/framework/{cache,sessions,views}
mkdir -p ~/docs.magento-opensource.com/shared/storage/framework/cache/data
mkdir -p ~/docs.magento-opensource.com/shared/resources

# Set proper permissions
chmod -R 755 ~/docs.magento-opensource.com/shared/storage
chmod -R 755 ~/docs.magento-opensource.com/shared/storage/framework/cache

# Create the production .env file
nano ~/docs.magento-opensource.com/shared/.env
```

### Example Production .env

```env
APP_NAME="Magento Merchant Documentation"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://docs.magento-opensource.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Algolia Search (if used)
ALGOLIA_APP_ID=your_algolia_id
ALGOLIA_SECRET=your_algolia_secret
ALGOLIA_SEARCH_KEY=your_search_key

# Torchlight (for syntax highlighting)
TORCHLIGHT_TOKEN=your_torchlight_token

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
```

## Step 3: Configure Web Server

### For Nginx:

```bash
sudo nano /etc/nginx/sites-available/docs.magento-opensource.com
```

```nginx
server {
    listen 80;
    server_name docs.magento-opensource.com;
    
    root /home/web-user/docs.magento-opensource.com/current/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable and test:

```bash
sudo ln -s /etc/nginx/sites-available/docs.magento-opensource.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### For Apache:

```bash
sudo nano /etc/apache2/sites-available/docs.magento-opensource.com.conf
```

```apache
<VirtualHost *:80>
    ServerName docs.magento-opensource.com
    DocumentRoot /home/web-user/docs.magento-opensource.com/current/public

    <Directory /home/web-user/docs.magento-opensource.com/current/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/docs-error.log
    CustomLog ${APACHE_LOG_DIR}/docs-access.log combined
</VirtualHost>
```

Enable and test:

```bash
sudo a2ensite docs.magento-opensource.com
sudo apachectl configtest
sudo systemctl reload apache2
```

## Step 4: Run First Deployment

From your **local machine** (where you have this repo checked out):

```bash
# Deploy with verbose output
vendor/bin/dep deploy production -vvv
```

This will:
1. ✅ Create `releases/1` directory
2. ✅ Clone the repository
3. ✅ Install Composer dependencies (production mode)
4. ✅ Install npm dependencies
5. ✅ Build production assets
6. ✅ Create symlinks from release to shared files
7. ✅ Sync external documentation
8. ✅ Clear and optimize Laravel caches
9. ✅ Create `current` symlink pointing to `releases/1`

## Step 5: Verify Deployment

1. **Visit the site**: https://docs.magento-opensource.com
2. **Check the structure**:
   ```bash
   ssh web-user@62.113.231.168
   cd ~/docs.magento-opensource.com
   ls -la
   # You should see: current -> releases/1
   ```
3. **Check logs**:
   ```bash
   ls -la ~/docs.magento-opensource.com/shared/storage/logs/
   ```

## Step 6: Enable GitHub Actions Auto-Deploy

Once the first manual deployment succeeds, GitHub Actions will handle future deployments automatically on every push to `main`.

The workflow is already configured and will:
- Trigger on push to main
- Use Deployer to deploy automatically
- Show deployment status in GitHub Actions tab

## Deployment Commands Reference

### From Local Machine

```bash
# Deploy to production
vendor/bin/dep deploy production

# Deploy with verbose output (for debugging)
vendor/bin/dep deploy production -vvv

# Rollback to previous release
vendor/bin/dep rollback production

# List all releases
vendor/bin/dep releases production

# SSH into production
vendor/bin/dep ssh production
```

### On Production Server

```bash
ssh web-user@62.113.231.168
cd ~/docs.magento-opensource.com

# View all releases
ls -la releases/

# View current release
readlink current

# Check shared files
ls -la shared/

# View logs
tail -f shared/storage/logs/laravel.log
```

## Testing the Full Workflow

1. Make a small change (e.g., update README)
2. Commit and push to main
3. Watch GitHub Actions deploy automatically
4. Verify the change is live
5. Test rollback:
   ```bash
   vendor/bin/dep rollback production
   # Verify site still works (shows previous version)
   
   # Deploy again to get back to latest
   vendor/bin/dep deploy production
   ```

## Troubleshooting

### Deployment fails with "Permission denied (publickey)"
**Solution**: Verify SSH key is added to GitHub secrets and matches the public key on production server:
```bash
ssh web-user@62.113.231.168
cat ~/.ssh/authorized_keys
```

### Website shows "File not found" or 404
**Solutions**:
1. Check web server config points to `current/public`
2. Verify current symlink exists: `ls -la ~/docs.magento-opensource.com/current`
3. Check permissions: `ls -la ~/docs.magento-opensource.com/`

### Assets not loading (CSS/JS 404)
**Solutions**:
1. Check npm build completed: Look for `public/build/` in the current release
2. Run manual build: `cd ~/docs.magento-opensource.com/current && npm run build`
3. Check Vite manifest exists: `ls current/public/build/manifest.json`

### External docs not showing up
**Solutions**:
1. Check shared docs exist: `ls ~/docs.magento-opensource.com/shared/resources/docs/`
2. Verify symlink: `ls -la ~/docs.magento-opensource.com/current/resources/docs`
3. Manually sync: `cd ~/docs.magento-opensource.com/current && DEPLOYER_ROOT=~/docs.magento-opensource.com bash bin/checkout_latest_docs.sh`

### "composer: command not found" during deploy
**Solution**: Ensure Composer is in web-user's PATH:
```bash
ssh web-user@62.113.231.168
which composer
# If not found, install or add to PATH
```

### "npm: command not found" during deploy
**Solution**: Ensure Node.js and npm are installed:
```bash
ssh web-user@62.113.231.168
node --version  # Should show v20+
npm --version
```

## Deployment Flow Explained

Every time you push to main or run `dep deploy production`:

1. **Prepare**: Create new release directory `releases/N`
2. **Clone**: Clone repository into the release
3. **Dependencies**: Install composer packages (production mode)
4. **Build**: Install npm packages and build assets
5. **Symlink Shared**: Link `.env`, `storage/`, `resources/docs/` from shared
6. **Docs Sync**: Update external documentation
7. **Optimize**: Clear and cache Laravel configs/routes/views
8. **Go Live**: Atomic symlink swap `current -> releases/N`
9. **Cleanup**: Remove old releases (keeps last 5)

If anything fails during steps 1-7, the current release keeps serving traffic (zero downtime).

## Managing Releases

Deployer keeps the last 5 releases by default. To change this, edit `deploy.php`:

```php
set('keep_releases', 10); // Keep last 10 releases
```

View releases:
```bash
ssh web-user@62.113.231.168
cd ~/docs.magento-opensource.com/releases
ls -la
```

Manually clean old releases:
```bash
vendor/bin/dep releases:cleanup production
```

## Emergency Manual Deployment

If GitHub Actions is down and you need to deploy manually:

```bash
# From your local machine
vendor/bin/dep deploy production -vvv
```

Or if Deployer isn't working, manual fallback:

```bash
ssh web-user@62.113.231.168
cd ~/docs.magento-opensource.com/current
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
DEPLOYER_ROOT=~/docs.magento-opensource.com bash bin/checkout_latest_docs.sh
php artisan optimize
```

## Security Notes

- Never commit `.env` to the repository
- Keep SSH keys secure and rotated regularly
- Restrict GitHub Actions secrets access to trusted maintainers
- Consider using a dedicated deploy SSH key (not personal key)
- Enable 2FA on GitHub for all maintainers

## Need Help?

If you encounter issues:
1. Check the troubleshooting section above
2. Run deployment with verbose output: `vendor/bin/dep deploy production -vvv`
3. Check production logs: `ssh web-user@62.113.231.168 tail -f ~/docs.magento-opensource.com/shared/storage/logs/laravel.log`
4. Contact the team with the error messages

---

🎉 **You're all set!** Future pushes to main will deploy automatically with zero downtime.
