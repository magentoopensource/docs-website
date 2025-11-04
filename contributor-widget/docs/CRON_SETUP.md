# Cron Job Setup Guide

## Overview

The GitHub Contributors Widget includes an automated cron job that fetches and updates contributor data monthly. This guide will help you set up and manage the cron job.

## What the Cron Job Does

1. **Fetches contributor data** from GitHub API
2. **Updates the database** with new statistics
3. **Refreshes the cache** for fast widget loading
4. **Logs everything** with comprehensive detail
5. **Handles errors** gracefully with retry logic
6. **Prevents concurrent runs** using lock management
7. **Sends email alerts** on critical failures (optional)

## Quick Setup (5 Minutes)

### Step 1: Test Manually First

Before scheduling, test the cron job manually:

```bash
cd /Users/carlsimpson/Documents/m2docs-widget

# Option A: Use the helper script (recommended)
./cron/run-manual.sh

# Option B: Run directly
php cron/update-contributors.php
```

Expected output:
```
✅ SUCCESS

Contributors processed: 100
Duration: 2.5s
API calls: 2
Rate limit remaining: 4998

Check logs: storage/logs/github-widget.log
```

### Step 2: Schedule with Cron

Find your PHP path:
```bash
which php
# Output: /usr/bin/php  (or /opt/homebrew/bin/php on macOS)
```

Edit your crontab:
```bash
crontab -e
```

Add this line (replace paths with your actual paths):
```cron
# Run on 1st of every month at 2:00 AM UTC
0 2 1 * * /usr/bin/php /Users/carlsimpson/Documents/m2docs-widget/cron/update-contributors.php >> /Users/carlsimpson/Documents/m2docs-widget/storage/logs/cron.log 2>&1
```

Save and verify:
```bash
crontab -l
```

### Step 3: Monitor First Run

Wait for the cron to run, or test immediately:

```bash
# Watch the log file
tail -f storage/logs/github-widget.log

# Check cron execution log
tail -f storage/logs/cron.log
```

## Cron Schedule Options

Choose the schedule that fits your needs:

### Monthly (Recommended)
```cron
# 1st of month at 2:00 AM UTC
0 2 1 * * /usr/bin/php /path/to/cron/update-contributors.php >> /path/to/storage/logs/cron.log 2>&1
```
- **API Usage:** ~2-3 calls per month (0.04%)
- **Best for:** Production sites with stable contributor base

### Weekly
```cron
# Every Monday at 3:00 AM UTC
0 3 * * 1 /usr/bin/php /path/to/cron/update-contributors.php >> /path/to/storage/logs/cron.log 2>&1
```
- **API Usage:** ~8-12 calls per month (0.16%)
- **Best for:** Active projects with frequent contributors

### Daily
```cron
# Every day at 4:00 AM UTC
0 4 * * * /usr/bin/php /path/to/cron/update-contributors.php >> /path/to/storage/logs/cron.log 2>&1
```
- **API Usage:** ~60-90 calls per month (1.2%)
- **Best for:** Real-time leaderboards, high-traffic sites

### Custom Time
Adjust the time to fit your timezone:

```cron
# Format: minute hour day month weekday
0 2 1 * * command

# Examples:
0 14 1 * * ...  # 2:00 PM UTC (7:00 AM PST / 10:00 AM EST)
30 6 1 * * ...  # 6:30 AM UTC (11:30 PM PST previous day)
```

## Advanced Configuration

### Email Notifications on Errors

Set your admin email in `.env`:

```bash
# .env
ADMIN_EMAIL=admin@yourdomain.com
```

The cron job will automatically email you when errors occur.

### Custom Lock Timeout

If your repository has many contributors, increase the lock timeout:

Edit `cron/update-contributors.php`:
```php
const LOCK_MAX_AGE = 3600; // Change to 7200 for 2 hours
```

### Retry Configuration

Adjust retry behavior for flaky connections:

```php
const RETRY_ATTEMPTS = 3; // Increase for more retries
const RETRY_DELAY = 60;   // Seconds between retries
```

## Monitoring & Logs

### View Recent Activity

```bash
# Last 50 log entries
tail -n 50 storage/logs/github-widget.log

# Follow log in real-time
tail -f storage/logs/github-widget.log

# Search for errors
grep "ERROR" storage/logs/github-widget.log

# Search for cron runs
grep "CRON JOB" storage/logs/github-widget.log
```

### Successful Run Example

```
[2025-10-22 02:00:01] [INFO] === CRON JOB STARTED ===
[2025-10-22 02:00:01] [INFO] Lock acquired successfully
[2025-10-22 02:00:01] [INFO] Database connection established
[2025-10-22 02:00:01] [INFO] Services initialized successfully
[2025-10-22 02:00:02] [INFO] GitHub API rate limit status | Context: {"limit":5000,"remaining":4998}
[2025-10-22 02:00:02] [INFO] Fetching contributors data from GitHub...
[2025-10-22 02:00:03] [INFO] Contributors fetched successfully | Context: {"count":100,"api_calls":2}
[2025-10-22 02:00:03] [INFO] Updating database...
[2025-10-22 02:00:04] [INFO] Database updated successfully
[2025-10-22 02:00:04] [INFO] Updating cache...
[2025-10-22 02:00:04] [INFO] Top 5 contributors cached
[2025-10-22 02:00:04] [INFO] === CRON JOB COMPLETED SUCCESSFULLY === | Context: {"duration_seconds":3.2}
```

### Failed Run Example

```
[2025-10-22 02:00:01] [INFO] === CRON JOB STARTED ===
[2025-10-22 02:00:02] [ERROR] Failed to fetch contributors after 3 attempts
[2025-10-22 02:00:02] [CRITICAL] === CRON JOB FAILED === | Context: {"error":"GitHub API error [503]"}
```

### Check Database Sync History

```sql
-- View recent sync attempts
SELECT * FROM api_sync_log
ORDER BY started_at DESC
LIMIT 10;

-- Success rate (last 30 days)
SELECT
    status,
    COUNT(*) as count,
    AVG(duration_seconds) as avg_duration,
    AVG(api_calls_made) as avg_api_calls
FROM api_sync_log
WHERE started_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY status;
```

## Troubleshooting

### Issue: Cron job doesn't run

**Check 1:** Verify cron is running
```bash
# macOS
sudo launchctl list | grep cron

# Linux
systemctl status cron
# or
service cron status
```

**Check 2:** Verify crontab entry
```bash
crontab -l
```

**Check 3:** Test manually
```bash
./cron/run-manual.sh
```

**Check 4:** Check system logs
```bash
# macOS
tail -f /var/log/system.log | grep cron

# Linux
tail -f /var/log/syslog | grep cron
# or
journalctl -u cron -f
```

### Issue: Permission denied

**Solution:**
```bash
chmod +x cron/update-contributors.php
chmod +x cron/run-manual.sh
chmod 755 storage/logs
```

### Issue: Lock file exists

This means another instance is running (or crashed without cleanup).

**Check if process is running:**
```bash
ps aux | grep update-contributors
```

**Manually remove lock:**
```bash
rm -f storage/locks/monthly_contributors_update.lock
```

**Check lock info:**
```bash
cat storage/locks/monthly_contributors_update.lock
```

### Issue: Database connection failed

**Solution 1:** Verify credentials
```bash
mysql -u widget_user -p github_contributors -e "SELECT 1"
```

**Solution 2:** Check `.env` file
```bash
cat .env | grep DB_
```

**Solution 3:** Test services
```bash
php demo/test-services.php
```

### Issue: GitHub API rate limit exceeded

**Check current rate limit:**
```bash
php -r "
require 'vendor/autoload.php';
use ContributorsWidget\Config\Configuration;
use ContributorsWidget\Utils\Logger;
use ContributorsWidget\Services\GitHubApiService;

\$config = Configuration::getInstance();
\$logger = new Logger(\$config);
\$github = new GitHubApiService(\$config, \$logger);
\$limit = \$github->checkRateLimit();

echo 'Remaining: ' . \$limit['remaining'] . ' / ' . \$limit['limit'] . \"\\n\";
echo 'Resets at: ' . date('Y-m-d H:i:s', \$limit['reset']) . \"\\n\";
"
```

**Solution:** Wait for reset or reduce cron frequency

### Issue: Slow execution

**Check 1:** Enable OpCache
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
```

**Check 2:** Optimize MySQL
```sql
-- Check table sizes
SELECT
    TABLE_NAME,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'github_contributors'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Optimize tables
OPTIMIZE TABLE contributors;
OPTIMIZE TABLE contributor_stats;
OPTIMIZE TABLE widget_cache;
```

**Check 3:** Profile execution
```bash
# Run with profiling
time php cron/update-contributors.php
```

## Cron Job Features

### Lock Management

Prevents multiple instances from running simultaneously:

```php
// Automatic lock acquisition
if (!$lockManager->acquire('monthly_contributors_update')) {
    echo "Another instance is running\n";
    exit(0);
}

// Automatic release on completion or error
```

Lock files stored in: `storage/locks/`

### Retry Logic

Automatically retries transient failures:

```php
const RETRY_ATTEMPTS = 3;  // 3 attempts
const RETRY_DELAY = 60;    // 60 seconds between retries
```

Retries on:
- Temporary network issues
- GitHub 502/503 errors
- Database deadlocks

Does NOT retry on:
- Rate limit exceeded (waits for reset)
- Invalid credentials
- 404 errors

### Transaction Safety

All database updates are wrapped in transactions:

```php
$pdo->beginTransaction();
try {
    // Update contributors
    // Update statistics
    // Log sync activity
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack(); // Automatic rollback on error
    throw $e;
}
```

### Comprehensive Logging

Every action is logged with context:

```php
$logger->info('Contributors fetched successfully', [
    'count' => 100,
    'api_calls' => 2,
    'duration' => 1.5
]);
```

Log levels:
- **DEBUG:** Detailed debugging information
- **INFO:** Normal operation messages
- **WARNING:** Non-critical issues
- **ERROR:** Recoverable errors
- **CRITICAL:** Fatal errors requiring immediate attention

### Performance Metrics

Tracks and logs performance data:

```php
// Logged on completion:
- Duration (seconds)
- Memory usage (MB)
- API calls made
- Contributors processed
- Database operations
```

## Uninstalling

To remove the cron job:

```bash
# Edit crontab
crontab -e

# Delete the line containing update-contributors.php
# Save and exit

# Verify removal
crontab -l
```

## Best Practices

1. **Always test manually first** before scheduling
2. **Monitor the first few runs** to ensure stability
3. **Set up email notifications** for critical sites
4. **Review logs weekly** for the first month
5. **Document your cron schedule** in your deployment docs
6. **Use appropriate frequency** based on repository activity
7. **Keep logs for at least 30 days** for troubleshooting

## Security Considerations

1. **File permissions:**
   ```bash
   chmod 755 cron/update-contributors.php
   chmod 755 cron/run-manual.sh
   chmod 700 storage/locks
   chmod 755 storage/logs
   ```

2. **Log file rotation:**
   Logs are automatically rotated at 10MB (5 backups kept)

3. **Lock file security:**
   Lock files contain only PID and timestamp (no sensitive data)

4. **Error messages:**
   Errors are logged but don't expose sensitive information in cron output

## Performance Expectations

| Repository Size | Contributors | Duration | API Calls | Memory |
|----------------|--------------|----------|-----------|--------|
| Small (< 10)   | 5-10         | 0.5-1s   | 1-2       | 8 MB   |
| Medium (< 100) | 50-100       | 1-3s     | 2-3       | 16 MB  |
| Large (< 500)  | 200-500      | 3-8s     | 3-5       | 32 MB  |
| Huge (> 500)   | 500+         | 8-15s    | 5-10      | 64 MB  |

## Support

If you encounter issues:

1. Check the logs first: `storage/logs/github-widget.log`
2. Run manual test: `./cron/run-manual.sh`
3. Verify configuration: `php demo/test-setup.php`
4. Check GitHub API status: https://www.githubstatus.com/

---

**Next Steps:**
- Review `CRONTAB.example` for schedule options
- Set up monitoring/alerting for production
- Document your deployment schedule
