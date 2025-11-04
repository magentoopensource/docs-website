# Quick Start Guide

Get your GitHub Contributors Widget running in 5 minutes!

## Prerequisites Check

Before starting, verify you have:
- [ ] PHP 8.0+ installed (`php -v`)
- [ ] Composer installed (`composer -V`)
- [ ] MySQL/MariaDB running
- [ ] Your GitHub token ready

## Step-by-Step Setup

### Step 1: Install Dependencies (30 seconds)

```bash
cd /Users/carlsimpson/Documents/m2docs-widget
composer install
```

**Expected output:**
```
Installing dependencies from composer.json
- guzzlehttp/guzzle
- vlucas/phpdotenv
- monolog/monolog
...
```

### Step 2: Configure Environment (Already Done! ✅)

Your `.env` file is already configured with:
- ✅ GitHub Token: `github_pat_11AA57...`
- ✅ Repository: `ukmeds/magento2-docs`
- ✅ Database: `github_contributors`

**What you need to change:**
- Update `DB_PASSWORD` if you use a different password
- Update `ADMIN_EMAIL` if needed

### Step 3: Create Database (2 minutes)

```bash
# Option A: Quick setup (if you have root access)
mysql -u root -p << 'EOF'
CREATE DATABASE IF NOT EXISTS github_contributors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'widget_user'@'localhost' IDENTIFIED BY 'secure_password_change_this';
GRANT SELECT, INSERT, UPDATE, DELETE ON github_contributors.* TO 'widget_user'@'localhost';
FLUSH PRIVILEGES;
USE github_contributors;
SOURCE database/schema.sql;
EOF

# Option B: Step by step
mysql -u root -p

# Then run these commands:
CREATE DATABASE github_contributors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'widget_user'@'localhost' IDENTIFIED BY 'secure_password_change_this';
GRANT SELECT, INSERT, UPDATE, DELETE ON github_contributors.* TO 'widget_user'@'localhost';
FLUSH PRIVILEGES;
exit

# Import schema
mysql -u widget_user -p github_contributors < database/schema.sql
```

### Step 4: Create Log Directory (10 seconds)

```bash
mkdir -p storage/logs
chmod 755 storage/logs
```

### Step 5: Test Setup (1 minute)

```bash
# Test configuration and database
php demo/test-setup.php
```

**Expected output:**
```
════════════════════════════════════════════════════════════════
  GitHub Contributors Widget - Setup Test
════════════════════════════════════════════════════════════════

📋 Test 1: Environment Configuration
────────────────────────────────────────────────────────────────
✅ .env file exists

⚙️  Test 2: Configuration Loading
────────────────────────────────────────────────────────────────
✅ Configuration loaded successfully
   Repository: ukmeds/magento2-docs
   ...

🎉 All tests passed! Your setup is ready.
```

### Step 6: Test GitHub API (1 minute)

```bash
php demo/test-github-api.php
```

**Expected output:**
```
════════════════════════════════════════════════════════════════
  GitHub API Connection Test
════════════════════════════════════════════════════════════════

🔍 Test 1: Checking Rate Limit
────────────────────────────────────────────────────────────────
✅ Rate Limit Status:
   Limit: 5000 requests/hour
   Remaining: 4998 requests
   ...

👥 Test 3: Fetching Top 5 Contributors
────────────────────────────────────────────────────────────────
✅ Found 5 contributors:

   #1 john-doe
      Contributions: 1,234
      ...

✅ All GitHub API tests passed!
```

## Troubleshooting

### Error: "Dependencies not installed"

**Solution:**
```bash
composer install
```

### Error: "Database connection failed"

**Solution 1:** Check database exists
```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'github_contributors';"
```

**Solution 2:** Verify password in `.env` matches
```bash
# Test connection
mysql -u widget_user -p github_contributors -e "SELECT 1;"
```

**Solution 3:** Re-create user
```bash
mysql -u root -p << 'EOF'
DROP USER IF EXISTS 'widget_user'@'localhost';
CREATE USER 'widget_user'@'localhost' IDENTIFIED BY 'secure_password_change_this';
GRANT SELECT, INSERT, UPDATE, DELETE ON github_contributors.* TO 'widget_user'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### Error: "Invalid GitHub token format"

**Issue:** Token doesn't match expected format `ghp_*` or `ghs_*`

**Solution:**
1. Your token starts with `github_pat_` which is the classic format
2. Edit `.env` and verify the token is correct
3. If issues persist, generate a new fine-grained token:
   - Go to GitHub → Settings → Developer settings → Fine-grained tokens
   - Create new token with Metadata (Read) + Contents (Read)

### Error: "Log directory is not writable"

**Solution:**
```bash
mkdir -p storage/logs
chmod 755 storage/logs
```

### Error: "Missing tables"

**Solution:**
```bash
mysql -u widget_user -p github_contributors < database/schema.sql
```

## What's Next?

Once both tests pass (✅), you're ready to continue development!

### Current Status: Phase 2 - 30% Complete

**Completed:**
- ✅ Configuration layer
- ✅ Database layer
- ✅ Test suite

**Next Steps (in order):**

1. **Utilities Layer** (1 hour)
   - Logger (PSR-3 compliant)
   - Rate limiter
   - Lock manager

2. **Services Layer** (2 hours)
   - GitHub API service
   - Cache service
   - Contributor service

3. **Repository Layer** (1 hour)
   - Contributor repository
   - Period repository
   - Sync log repository

4. **Frontend Widget** (2 hours)
   - Tailwind CSS styling
   - HTML template
   - Responsive design

5. **Cron Job** (1 hour)
   - Monthly update script
   - Error handling
   - Email notifications

**Total Time to Complete:** ~7 hours

## Test Your Setup is Working

Run both tests and look for green checkmarks:

```bash
php demo/test-setup.php && php demo/test-github-api.php
```

If you see:
```
🎉 All tests passed!
✅ All GitHub API tests passed!
```

**You're ready to proceed!**

## Quick Commands Reference

```bash
# Test setup
php demo/test-setup.php

# Test GitHub API
php demo/test-github-api.php

# View logs
tail -f storage/logs/*.log

# Check database
mysql -u widget_user -p github_contributors -e "SHOW TABLES;"

# Test configuration
php -r "require 'vendor/autoload.php'; \$c = ContributorsWidget\Config\Configuration::getInstance(); echo 'OK';"
```

## Need Help?

1. **Check test output:** Run both test scripts and read the error messages
2. **Review logs:** Check `storage/logs/` for detailed error information
3. **Verify environment:** Ensure all values in `.env` are correct
4. **Database connection:** Test with `mysql` command line first
5. **GitHub token:** Verify it has correct permissions

---

**Ready?** Run the tests above and let's build this amazing widget! 🚀
