# Deployment Runbook

## Overview

This document provides comprehensive guidance for deploying the Merchant Documentation website to production.

## Deployment Architecture

**Production Server:** 62.113.231.168
**Domain:** https://docs.magento-opensource.com/
**Deployment Tool:** Deployer (PHP)
**Asset Build:** Vite (Node.js)
**Web Server:** Nginx + PHP-FPM 8.3

## Deployment Flow

When code is pushed to `main` branch, GitHub Actions automatically:

1. ✅ Checks out code
2. ✅ Installs Composer dependencies
3. ✅ Establishes SSH connection
4. ✅ Triggers Deployer

Deployer then executes:

1. ✅ **Prepare** - Creates new release directory
2. ✅ **Install Vendors** - Runs `composer install`
3. ✅ **Install npm** - Runs `npm ci`
4. ✅ **Build Assets** - Runs `npm run build`
5. ✅ **Verify Assets** - Confirms manifest.json and CSS/JS files exist
6. ✅ **Symlink** - Updates `current` symlink to new release
7. ✅ **Sync Docs** - Pulls latest documentation content
8. ✅ **Optimize** - Clears and caches Laravel configs/routes/views
9. ✅ **Clear OPcache** - Resets PHP opcode cache
10. ✅ **Health Check** - Verifies site loads and CSS is accessible

## Prerequisites

### For Developers
- Git access to repository
- Node.js 20+ installed locally
- PHP 8.3+ installed locally
- Composer installed locally

### For Production Server
- SSH access as `web-user`
- Node.js 20+ installed
- PHP 8.3 with FPM
- Nginx configured
- Composer installed

## Manual Deployment

If automatic deployment fails or you need to deploy manually:

```bash
# From your local machine
vendor/bin/dep deploy production -vvv
```

## Common Issues & Solutions

### Issue 1: CSS Not Loading After Deployment

**Symptoms:**
- Site loads but has no styling
- Browser console shows 404 for CSS files
- Assets have different hashes than expected

**Root Cause:**
- PHP OPcache holding onto old manifest data
- Stale Laravel view cache

**Solution:**
```bash
# SSH into production
ssh mageos.production

# Navigate to current release
cd docs.magento-opensource.com/current

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Clear OPcache via HTTP request
echo '<?php opcache_reset(); echo "Cleared"; ?>' > public/clear-opcache.php
curl https://docs.magento-opensource.com/clear-opcache.php
rm public/clear-opcache.php

# Verify assets exist
ls -la public/build/assets/app-*.css
```

### Issue 2: Build Fails on Production

**Symptoms:**
- Deployment fails during `deploy:build-assets` task
- "manifest.json not found" error

**Root Cause:**
- npm dependencies not installed
- Node.js version mismatch
- Out of disk space

**Solution:**
```bash
# Check Node.js version (should be 20+)
node --version

# Check disk space
df -h

# Manually build assets
cd ~/docs.magento-opensource.com/current
npm ci --production=false
npm run build

# Verify build succeeded
ls -la public/build/manifest.json
```

### Issue 3: Old Release Still Serving

**Symptoms:**
- New code deployed but old content still shows
- `current` symlink points to correct release but old code runs

**Root Cause:**
- PHP-FPM workers still running old code
- Need to reload PHP-FPM

**Solution:**
```bash
# Check current symlink
readlink ~/docs.magento-opensource.com/current

# If you have sudo access (usually not)
sudo systemctl reload php8.3-fpm

# Alternative: Clear OPcache (this usually works)
cd ~/docs.magento-opensource.com/current
echo '<?php opcache_reset(); echo "Cleared"; ?>' > public/clear-opcache.php
curl https://docs.magento-opensource.com/clear-opcache.php
rm public/clear-opcache.php
```

### Issue 4: Permission Errors

**Symptoms:**
- "Permission denied" errors during deployment
- Cannot write to storage or cache directories

**Root Cause:**
- Incorrect file permissions on shared directories

**Solution:**
```bash
# Fix permissions on storage
cd ~/docs.magento-opensource.com
chmod -R 775 shared/storage
chmod -R 775 current/bootstrap/cache

# Verify web server user can write
sudo -u www-data touch shared/storage/test.txt
rm shared/storage/test.txt
```

## Rollback Procedure

If a deployment causes critical issues:

```bash
# SSH into production
ssh mageos.production
cd ~/docs.magento-opensource.com

# List available releases
ls -la releases/

# Identify previous working release (e.g., release 29 if 30 is broken)
# Update symlink to previous release
ln -sfn releases/29 current

# Clear caches
cd current
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Clear OPcache
echo '<?php opcache_reset(); echo "Cleared"; ?>' > public/clear-opcache.php
curl https://docs.magento-opensource.com/clear-opcache.php
rm public/clear-opcache.php

# Verify site works
curl -I https://docs.magento-opensource.com/
```

## Deployment Checklist

Before deploying:
- [ ] All tests pass locally
- [ ] Code reviewed and approved
- [ ] No console errors in development
- [ ] Assets build successfully locally (`npm run build`)

After deploying:
- [ ] GitHub Actions workflow completed successfully
- [ ] All Deployer tasks completed without errors
- [ ] Site loads at https://docs.magento-opensource.com/
- [ ] CSS and JavaScript are loading (check browser DevTools)
- [ ] No console errors in browser
- [ ] Navigation works correctly
- [ ] Search functionality works

## Monitoring

After deployment, monitor:

1. **Application Logs**
   ```bash
   tail -f ~/docs.magento-opensource.com/shared/storage/logs/laravel.log
   ```

2. **Web Server Logs**
   ```bash
   tail -f ~/logs/docs.magento-opensource.com.access_log
   tail -f ~/logs/docs.magento-opensource.com.error_log
   ```

3. **PHP-FPM Logs**
   ```bash
   tail -f ~/logs/php8.3-fpm.log
   ```

## Emergency Contacts

- **Repository:** https://github.com/magentoopensource/docs-website
- **Server Admin:** web-user@62.113.231.168
- **Documentation Issues:** Create issue in GitHub repository

## Configuration Files

### Key Files
- **deploy.php** - Deployer configuration
- **.github/workflows/deploy.yml** - GitHub Actions workflow
- **vite.config.js** - Asset build configuration
- **resources/views/partials/layout.blade.php** - Main template with `@vite` directive

### Environment Variables
Set in `~/docs.magento-opensource.com/shared/.env`:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://docs.magento-opensource.com`
- See `.env.example` for full list

## Best Practices

1. **Never** commit built assets (`public/build/`) to git
2. **Always** verify deployment succeeded via health checks
3. **Monitor** logs after deployment for at least 5 minutes
4. **Test** major features after deployment
5. **Keep** at least 5 releases for quick rollback (default setting)
6. **Document** any manual fixes applied in production

## Deployment History

Track significant deployments:

| Date | Release | Changes | Issues | Resolution |
|------|---------|---------|--------|------------|
| 2025-11-25 | 30 | Improved deployment process | CSS not loading | Cleared OPcache manually |

## Troubleshooting Commands

Quick reference for common diagnostic commands:

```bash
# Check current release
readlink ~/docs.magento-opensource.com/current

# List all releases
ls -lat ~/docs.magento-opensource.com/releases/

# Check if assets exist in current release
ls -la ~/docs.magento-opensource.com/current/public/build/assets/app-*.{css,js}

# View manifest content
cat ~/docs.magento-opensource.com/current/public/build/manifest.json | jq .

# Test if homepage loads
curl -I https://docs.magento-opensource.com/

# Check for PHP errors
php -l ~/docs.magento-opensource.com/current/public/index.php

# View Laravel configuration
cd ~/docs.magento-opensource.com/current && php artisan config:show

# Check PHP-FPM status
ps aux | grep php-fpm | grep docs.magento-opensource.com
```

## Additional Resources

- [Deployer Documentation](https://deployer.org/docs/7.x)
- [Laravel Deployment Docs](https://laravel.com/docs/12.x/deployment)
- [Vite Build Guide](https://vitejs.dev/guide/build.html)
