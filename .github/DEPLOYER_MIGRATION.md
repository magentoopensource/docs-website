# Migration Guide: Moving to Deployer-Based Deployments

This guide walks through the one-time migration process to switch from direct git pull deployments to Deployer's atomic deployment structure.

## Overview

Deployer uses a release-based directory structure with atomic symlinks, providing:
- ✅ Zero-downtime deployments
- ✅ Instant rollbacks
- ✅ Persistent shared files (logs, .env, external docs)
- ✅ Safe deployments (old version stays live until new one is ready)

## Current vs. New Structure

### Current (Simple Git Pull)
```
~/docs.magento-opensource.com/
├── app/
├── public/
├── resources/
├── .env
└── ...
```

### New (Deployer)
```
~/docs.magento-opensource.com/
├── .dep/              # Deployer metadata
├── releases/          # All releases
│   ├── 1/            # Old release
│   ├── 2/            # Old release
│   └── 3/            # Current release
├── shared/           # Shared across releases
│   ├── .env
│   ├── storage/
│   └── resources/docs/
└── current -> releases/3  # Symlink to active release
```

## Prerequisites

Before starting:
1. ✅ Deployer installed locally (already done via composer)
2. ✅ GitHub Actions secrets configured (SSH_PRIVATE_KEY)
3. ✅ Full backup of current production site
4. ✅ SSH access to production server as web-user

## Migration Steps

### Step 1: Backup Current Production

```bash
ssh web-user@62.113.231.168

# Create backup
cd ~
tar -czf docs-backup-$(date +%Y%m%d-%H%M%S).tar.gz docs.magento-opensource.com/
ls -lh docs-backup-*.tar.gz
```

### Step 2: Prepare Shared Files

```bash
# Still on production server
cd ~/docs.magento-opensource.com

# Create the new structure directories
mkdir -p ../docs-deployer-temp/shared/{storage,resources}

# Copy .env to shared
cp .env ../docs-deployer-temp/shared/.env

# Move storage to shared (preserving logs, cache, etc.)
cp -r storage ../docs-deployer-temp/shared/

# Move external docs to shared
if [ -d "resources/docs" ]; then
    cp -r resources/docs ../docs-deployer-temp/shared/resources/
fi
```

### Step 3: Rename Directories

```bash
# Still on production server
cd ~

# Move current directory out of the way
mv docs.magento-opensource.com docs.magento-opensource.com.old

# Rename temp directory to final name
mv docs-deployer-temp docs.magento-opensource.com

# Verify structure
ls -la docs.magento-opensource.com/
# Should see: shared/ directory
```

### Step 4: Initialize Deployer Structure

```bash
# Still on production server
cd ~/docs.magento-opensource.com

# Create releases directory
mkdir -p releases

# Create .dep directory for Deployer metadata
mkdir -p .dep
```

### Step 5: Run Initial Deployment

From your **local machine**:

```bash
# This will create the first release and set up the current symlink
vendor/bin/dep deploy production -vvv
```

This will:
1. Clone the repository into releases/1
2. Install composer dependencies
3. Install npm dependencies
4. Build assets
5. Create symlinks from releases/1 to shared files
6. Sync external documentation
7. Optimize Laravel caches
8. Create `current` symlink pointing to releases/1

### Step 6: Update Web Server Configuration

The web server (nginx/apache) needs to point to the `current/public` directory.

#### For Nginx:

```bash
ssh web-user@62.113.231.168
sudo nano /etc/nginx/sites-available/docs.magento-opensource.com
```

Change:
```nginx
root /home/web-user/docs.magento-opensource.com/public;
```

To:
```nginx
root /home/web-user/docs.magento-opensource.com/current/public;
```

Test and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

#### For Apache:

```bash
ssh web-user@62.113.231.168
sudo nano /etc/apache2/sites-available/docs.magento-opensource.com.conf
```

Change:
```apache
DocumentRoot /home/web-user/docs.magento-opensource.com/public
```

To:
```apache
DocumentRoot /home/web-user/docs.magento-opensource.com/current/public
```

Test and reload:
```bash
sudo apachectl configtest
sudo systemctl reload apache2
```

### Step 7: Verify Deployment

1. **Check the website loads**: Visit https://docs.magento-opensource.com
2. **Check logs**: `ls -la ~/docs.magento-opensource.com/shared/storage/logs/`
3. **Check structure**: 
   ```bash
   ssh web-user@62.113.231.168
   cd ~/docs.magento-opensource.com
   ls -la
   # Verify current -> releases/1 symlink exists
   ```

### Step 8: Test Rollback

```bash
# From local machine
vendor/bin/dep rollback production

# Site should still work (will point back to... well, release 1 is the only one)
# After a second deployment, rollback will be more meaningful
```

### Step 9: Cleanup Old Files (Optional)

Only after everything is working:

```bash
ssh web-user@62.113.231.168
cd ~
# Keep the backup, but can remove the old directory
rm -rf docs.magento-opensource.com.old
```

## Testing the Full Workflow

1. Make a small change to the codebase (e.g., update README)
2. Commit and push to main
3. Watch GitHub Actions run the deployment
4. Verify site updates automatically
5. Test rollback: `vendor/bin/dep rollback production`

## Deployment Commands Reference

### From Local Machine (for manual deploys)

```bash
# Full deployment
vendor/bin/dep deploy production

# Verbose output for debugging
vendor/bin/dep deploy production -vvv

# Rollback to previous release
vendor/bin/dep rollback production

# List all releases
vendor/bin/dep releases production

# SSH into production
vendor/bin/dep ssh production
```

### On Production Server (for manual operations)

```bash
ssh web-user@62.113.231.168
cd ~/docs.magento-opensource.com

# View releases
ls -la releases/

# View current release
ls -la current

# Check shared files
ls -la shared/

# Manually change current release (emergency only!)
ln -snf releases/2 current
```

## Troubleshooting

### Deployment fails with "Permission denied"
- Check SSH key is correctly added to GitHub secrets
- Verify web-user has correct permissions: `ls -la ~`

### Website shows 404 after migration
- Check nginx/apache config points to `current/public`
- Verify current symlink exists: `ls -la ~/docs.magento-opensource.com/current`

### External docs not syncing
- Check shared/resources/docs exists: `ls ~/docs.magento-opensource.com/shared/resources/docs/`
- Manually run sync: `cd ~/docs.magento-opensource.com/current && DEPLOYER_ROOT=~/docs.magento-opensource.com bash bin/checkout_latest_docs.sh`

### Need to access old files
- They're in the backup: `tar -tzf ~/docs-backup-*.tar.gz | less`
- Extract specific files: `tar -xzf ~/docs-backup-*.tar.gz path/to/file`

## Rollback Plan (If Migration Fails)

If something goes terribly wrong:

```bash
ssh web-user@62.113.231.168
cd ~

# Stop web server
sudo systemctl stop nginx  # or apache2

# Restore from backup
rm -rf docs.magento-opensource.com
tar -xzf docs-backup-*.tar.gz

# Revert web server config to old path (remove /current from path)
sudo nano /etc/nginx/sites-available/docs.magento-opensource.com

# Start web server
sudo systemctl start nginx

# Verify site is back
curl -I localhost
```

## Benefits After Migration

Once migrated, you get:

1. **Atomic deployments**: Site never in broken state during deploy
2. **Instant rollbacks**: `dep rollback production` (takes seconds)
3. **Zero downtime**: Users never see deployment happening
4. **Keep history**: Last 5 releases kept automatically
5. **Shared data persists**: Logs, docs, .env survive deployments
6. **Professional workflow**: Industry-standard deployment

## Need Help?

If you encounter issues during migration:
1. Don't panic - you have a backup
2. Check the troubleshooting section above
3. The old directory is at `~/docs.magento-opensource.com.old` until you delete it
4. Contact the team for assistance
