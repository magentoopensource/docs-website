# How to Run Tests

## Prerequisites

Make sure you have:
- ✅ PHP 8.0+ installed
- ✅ Composer installed
- ✅ MySQL/MariaDB running
- ✅ Database created and schema imported

## Quick Test (5 Minutes)

### Step 1: Navigate to Project Directory

```bash
cd /Users/carlsimpson/Documents/m2docs-widget
```

### Step 2: Install Dependencies (if not done)

```bash
composer install
```

Expected output:
```
Installing dependencies from composer.json
- Installing guzzlehttp/guzzle
- Installing vlucas/phpdotenv
- Installing monolog/monolog
...
Generating autoload files
```

### Step 3: Create Database (if not done)

```bash
# Create database and user
mysql -u root -p << 'EOF'
CREATE DATABASE IF NOT EXISTS github_contributors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'widget_user'@'localhost' IDENTIFIED BY 'secure_password_change_this';
GRANT SELECT, INSERT, UPDATE, DELETE ON github_contributors.* TO 'widget_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Import schema
mysql -u widget_user -p github_contributors < database/schema.sql
```

### Step 4: Run Installation Check

```bash
./install-check.sh
```

Expected output:
```
════════════════════════════════════════════════════════════════
  GitHub Contributors Widget - Installation Check
════════════════════════════════════════════════════════════════

🔍 Checking PHP...
✅ PHP 8.1.0 found

🔍 Checking Composer...
✅ Composer 2.5.0 found

🔍 Checking MySQL...
✅ MySQL 8.0.30 found

🎉 Perfect! All checks passed.
```

### Step 5: Run Services Test

```bash
php demo/test-services.php
```

## What to Expect

### Test Output

```
════════════════════════════════════════════════════════════════
  Services Layer Test
════════════════════════════════════════════════════════════════

Repository: ukmeds/magento2-docs

📝 Test 1: Logger (PSR-3 Compliant)
────────────────────────────────────────────────────────────────
✅ Logger initialized
   Log file: storage/logs/github-widget.log
   Levels: DEBUG, INFO, WARNING, ERROR, CRITICAL
   Features: Auto-rotation, PSR-3 compliant, context support

🗄️  Test 2: Database Connection
────────────────────────────────────────────────────────────────
✅ Database connected

💾 Test 3: Cache Service (3-Tier Caching)
────────────────────────────────────────────────────────────────
✅ Cache set/get working
   Memory hits: 1
   Database hits: 0
   Misses: 0
   Hit rate: 100%
   Total cached keys: 1
   Cache size: 0.00 MB

🔒 Test 4: Lock Manager (Cron Concurrency Control)
────────────────────────────────────────────────────────────────
✅ Lock acquired for 'test_process'
   PID: 12345
   Hostname: your-machine
   Age: 1s
✅ Lock released

🐙 Test 5: GitHub API Service
────────────────────────────────────────────────────────────────
Checking rate limit...
✅ Rate Limit Status:
   Limit: 5,000 requests/hour
   Remaining: 4,998 requests
   Resets at: 2025-10-22 15:30:00 UTC
   Usage: 0.04%

Fetching top 5 contributors...
   Cache miss - fetching from GitHub API
   ✅ Data cached

✅ Found 5 contributors:

   #1 john-doe
      Contributions: 1,234
      Type: User
      Profile: https://github.com/john-doe

   #2 jane-smith
      Contributions: 987
      Type: User
      Profile: https://github.com/jane-smith

   #3 contributor-name
      Contributions: 654
      Type: User
      Profile: https://github.com/contributor-name

   #4 another-user
      Contributions: 321
      Type: User
      Profile: https://github.com/another-user

   #5 final-contributor
      Contributions: 123
      Type: User
      Profile: https://github.com/final-contributor

API calls made: 2

════════════════════════════════════════════════════════════════
  Test Summary
════════════════════════════════════════════════════════════════

✅ All services working correctly!

Services tested:
  ✓ Logger - PSR-3 compliant logging with rotation
  ✓ Cache Service - 3-tier caching (memory → database → API)
  ✓ Lock Manager - Cron job concurrency control
  ✓ GitHub API Service - Rate-limited API integration
  ✓ Rate Limiter - Automatic rate limit management

Cache performance:
  Hit rate: 100%
  Memory cache size: 1 keys
  Database cache size: 1 keys

GitHub API usage:
  Calls made: 2
  Remaining: 4,998
  Usage: 0.04%

Next steps:
  1. Check logs: tail -f storage/logs/github-widget.log
  2. View cached data in database: SELECT * FROM widget_cache;
  3. Continue with frontend widget implementation
```

## Troubleshooting

### Error: "Dependencies not installed"

**Solution:**
```bash
composer install
```

### Error: "Database connection failed"

**Solution 1:** Check if MySQL is running
```bash
# macOS
brew services list | grep mysql

# Linux
systemctl status mysql
```

**Solution 2:** Verify database exists
```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'github_contributors';"
```

**Solution 3:** Check credentials in `.env`
```bash
# Make sure these match your MySQL setup
DB_HOST=localhost
DB_USER=widget_user
DB_PASSWORD=secure_password_change_this
DB_NAME=github_contributors
```

**Solution 4:** Re-create database user
```bash
mysql -u root -p << 'EOF'
DROP USER IF EXISTS 'widget_user'@'localhost';
CREATE USER 'widget_user'@'localhost' IDENTIFIED BY 'secure_password_change_this';
GRANT SELECT, INSERT, UPDATE, DELETE ON github_contributors.* TO 'widget_user'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### Error: "Invalid GitHub token format"

**Solution:**
Your token should start with `github_pat_` or `ghp_` and be at least 40 characters.

Check your `.env` file:
```bash
cat .env | grep GITHUB_API_TOKEN
```

### Error: "GitHub API error [404]"

**Solution:**
Repository not found or no access. Check:
```bash
# Verify these in .env
GITHUB_REPO_OWNER=ukmeds
GITHUB_REPO_NAME=magento2-docs
```

### Error: "Missing tables"

**Solution:**
Import database schema:
```bash
mysql -u widget_user -p github_contributors < database/schema.sql

# Verify tables exist
mysql -u widget_user -p github_contributors -e "SHOW TABLES;"
```

Expected tables:
- api_rate_limits
- api_sync_log
- contribution_periods
- contributor_stats
- contributors
- widget_cache

### Error: "Log directory is not writable"

**Solution:**
```bash
mkdir -p storage/logs
chmod 755 storage/logs
```

## After Tests Pass

### 1. Check the Logs

```bash
tail -f storage/logs/github-widget.log
```

You should see:
```
[2025-10-22 14:30:00] [INFO] Test log entry from demo script
[2025-10-22 14:30:00] [DEBUG] Debug information | Context: {"test":"data","number":123}
[2025-10-22 14:30:00] [WARNING] This is a warning
[2025-10-22 14:30:01] [INFO] Rate limit updated | Context: {"limit":5000,"remaining":4998,...}
```

### 2. View Cached Data

```bash
mysql -u widget_user -p github_contributors -e "SELECT cache_key, LENGTH(cache_value) as size, expires_at FROM widget_cache;"
```

Expected output:
```
+------------------------------------------+------+---------------------+
| cache_key                                | size | expires_at          |
+------------------------------------------+------+---------------------+
| test_cache_key                           |  123 | 2025-10-23 14:30:00 |
| contributors_top5_ukmeds_magento2-docs   | 2456 | 2025-10-23 14:30:00 |
+------------------------------------------+------+---------------------+
```

### 3. Test Cache Hit Rate

Run the test again:
```bash
php demo/test-services.php
```

Second run should show:
```
💾 Test 3: Cache Service (3-Tier Caching)
────────────────────────────────────────────────────────────────
✅ Cache set/get working
   Memory hits: 1        ← Now hitting memory cache
   Database hits: 0
   Misses: 0
   Hit rate: 100%

🐙 Test 5: GitHub API Service
────────────────────────────────────────────────────────────────
Fetching top 5 contributors...
   ✅ Cache hit - using cached data   ← No API call!

API calls made: 1   ← Only 1 API call (rate limit check)
```

## Quick Commands Reference

```bash
# Run all tests in order
./install-check.sh && php demo/test-services.php

# Check PHP version
php -v

# Check Composer
composer -V

# Check MySQL
mysql -V

# View logs (live)
tail -f storage/logs/github-widget.log

# View cached data
mysql -u widget_user -p github_contributors -e "SELECT * FROM widget_cache\G"

# Clear cache
mysql -u widget_user -p github_contributors -e "DELETE FROM widget_cache;"

# Check database
mysql -u widget_user -p github_contributors -e "SHOW TABLES;"

# Test configuration only
php -r "require 'vendor/autoload.php'; \$c = ContributorsWidget\Config\Configuration::getInstance(); echo 'OK';"
```

## Success Indicators

✅ **All tests passed** if you see:
- Green checkmarks (✅) for all tests
- No red X marks (❌)
- "All services working correctly!"
- Real contributor data from your repository
- Cache hit rate of 100% on second run
- API usage < 1%

## Next Steps After Tests Pass

1. **Review the logs:**
   ```bash
   cat storage/logs/github-widget.log
   ```

2. **Check database cache:**
   ```bash
   mysql -u widget_user -p github_contributors -e "SELECT * FROM widget_cache\G"
   ```

3. **Understand what was tested:**
   - ✅ Logger (PSR-3 compliant)
   - ✅ Cache Service (3-tier)
   - ✅ Lock Manager (concurrency control)
   - ✅ GitHub API Service (with rate limiting)
   - ✅ Real API calls to GitHub
   - ✅ Real database operations

4. **Ready for next phase:**
   - Frontend widget (Tailwind CSS)
   - Cron job implementation
   - Complete integration

---

**Having issues?** Check the troubleshooting section above or review the error messages carefully.

**Tests passing?** Congratulations! Your backend is fully functional and ready for the frontend! 🎉
