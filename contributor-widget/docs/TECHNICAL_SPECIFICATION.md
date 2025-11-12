# GitHub Contributors Widget - Technical Specification v1.0

**Project:** Production-Grade GitHub Contributors Widget
**Target Platform:** Magento 2 Documentation Website
**Date:** 2025-10-22
**Status:** Phase 1 Complete - Ready for Implementation

---

## Executive Summary

This document provides the complete technical specification for a production-ready GitHub contributors widget designed for a Magento 2 documentation website. The solution prioritizes security, performance, and maintainability while delivering a beautiful user experience.

**Key Metrics:**
- API Calls: 2-3 per month (0.04% of GitHub's rate limit)
- Widget Load Time: < 50ms (from cache)
- Database Queries: 1 per page load
- Cache Hit Rate: > 99%
- Security Level: Zero SQL injection vulnerabilities, XSS prevention

---

## 1. Technology Stack

### Core Technologies
- **Backend:** PHP 8.x with strict types
- **Database:** MySQL 8.0+ / MariaDB 10.5+
- **Frontend:** Tailwind CSS (utility-first styling)
- **API:** GitHub REST API v3
- **Standards:** PSR-12 coding style, PSR-3 logging

### Dependencies
- **Guzzle** (HTTP client for API calls)
- **vlucas/phpdotenv** (Environment variable management)
- **monolog/monolog** (PSR-3 compliant logging)

---

## 2. API Strategy

### Decision: GitHub REST API v3

**Rationale:**
1. GraphQL API lacks contributors endpoint
2. REST endpoint returns pre-sorted data
3. Simple HTTP caching with ETags
4. Well-documented and stable
5. Minimal API calls needed

### API Endpoints

#### Primary Endpoint
```
GET https://api.github.com/repos/{owner}/{repo}/contributors
Parameters:
  - per_page=100 (maximum allowed)
  - page=1 (top contributors in first page)

Response:
  - login: GitHub username
  - avatar_url: User avatar image
  - html_url: Profile URL
  - contributions: Total contribution count
  - type: User|Bot|Organization
```

#### Supplementary Endpoint
```
GET https://api.github.com/repos/{owner}/{repo}/stats/contributors

Response:
  - author: { login, avatar_url }
  - total: Total commits
  - weeks: [ { w: timestamp, a: additions, d: deletions, c: commits } ]

Use Case: Time-based filtering (last week/month)
```

#### Rate Limit Endpoint
```
GET https://api.github.com/rate_limit

Response:
  - rate: { limit, remaining, reset }

Use Case: Pre-flight check before expensive operations
```

### Authentication

**Token Type:** Fine-Grained Personal Access Token

**Required Permissions (Public Repos):**
- Metadata: Read
- Contents: Read

**Token Configuration:**
```bash
# .env file
GITHUB_API_TOKEN=your_github_personal_access_token_here
GITHUB_REPO_OWNER=your_organization
GITHUB_REPO_NAME=your_repository
```

**Security Requirements:**
- NEVER hardcode tokens
- Use environment variables or secrets manager
- Set token expiration (90 days recommended)
- Scope to specific repository only
- Monitor token usage via logging

### Rate Limit Management

**GitHub Limits:**
- Unauthenticated: 60 requests/hour
- Authenticated: 5,000 requests/hour

**Our Strategy (2-3 API calls/month):**

1. **30-Day Caching**
   - Database cache expires in 30 days
   - Refresh only via monthly cron job

2. **ETag Conditional Requests**
   ```php
   $headers = ['If-None-Match' => $cachedETag];
   // Returns 304 Not Modified if data unchanged (saves API call)
   ```

3. **Pre-flight Rate Limit Check**
   ```php
   if ($rateLimit['remaining'] < 100) {
       throw new InsufficientRateLimitException();
   }
   ```

4. **Retry with Exponential Backoff**
   - Max 3 retries
   - Delays: 2s, 4s, 8s
   - Handle 202 (computing) for stats endpoint

5. **Fallback to Cached Data**
   - On API failure, use last successful fetch
   - Never display empty widget if cache available

---

## 3. Database Architecture

### Schema Design Principles

- **3NF Normalization:** No transient dependencies
- **Referential Integrity:** Foreign keys with CASCADE
- **Indexing Strategy:** Optimized for read-heavy queries
- **UTF-8 Support:** utf8mb4 for emoji support
- **Timestamps:** UTC timezone, consistent formatting

### Entity Relationship Diagram

```
┌─────────────────┐
│  contributors   │
│  - id (PK)      │◄─────┐
│  - github_id    │      │
│  - username     │      │ (FK)
│  - avatar_url   │      │
│  - profile_url  │      │
└─────────────────┘      │
                         │
┌─────────────────────┐  │
│ contribution_periods│  │
│ - id (PK)           │  │
│ - period_type       │  │
│ - start_date        │  │ (FK)
│ - end_date          │  │
│ - is_current        │  │
└─────────────────────┘  │
         │               │
         └───────┐  ┌────┘
                 ▼  ▼
        ┌──────────────────┐
        │ contributor_stats│
        │ - id (PK)        │
        │ - contributor_id │
        │ - period_id      │
        │ - contributions  │
        │ - commits        │
        │ - rank_position  │
        └──────────────────┘
```

### Performance Optimization

**Optimized Query for Widget Display:**
```sql
SELECT
    c.username,
    c.avatar_url,
    c.profile_url,
    cs.contribution_count,
    cs.rank_position
FROM contributor_stats cs
INNER JOIN contributors c ON cs.contributor_id = c.id
INNER JOIN contribution_periods cp ON cs.period_id = cp.id
WHERE cp.is_current = TRUE
  AND cp.period_type = 'monthly'
  AND cs.rank_position <= 5
ORDER BY cs.rank_position ASC
LIMIT 5;

-- Execution time: ~5-10ms
-- Uses indexes: idx_is_current, idx_period_rank, PRIMARY
```

**Index Strategy:**
- `contributors.github_id` (UNIQUE) - Fast lookup
- `contributor_stats.period_id + rank_position` - Widget query
- `contribution_periods.is_current` - Current period filter
- All foreign keys indexed automatically

---

## 4. Caching Strategy

### Three-Tier Architecture

```
┌────────────────────────────────────┐
│  Tier 1: Memory Cache (PHP Array) │
│  TTL: 1 hour                       │
│  Hit Rate: ~95%                    │
│  Access Time: < 1ms                │
└────────────────────────────────────┘
              ↓ (miss)
┌────────────────────────────────────┐
│  Tier 2: Database Cache Table     │
│  TTL: 30 days                      │
│  Hit Rate: ~4.9%                   │
│  Access Time: ~5-10ms              │
└────────────────────────────────────┘
              ↓ (miss)
┌────────────────────────────────────┐
│  Tier 3: GitHub API (External)    │
│  Frequency: Monthly                │
│  Hit Rate: ~0.1%                   │
│  Access Time: ~500-1000ms          │
└────────────────────────────────────┘
```

### Cache Invalidation Rules

1. **Monthly Cron Job:** Full refresh on 1st of month
2. **Manual Trigger:** Via admin command
3. **API Failure:** Keep using stale cache (graceful degradation)
4. **Cache Warmup:** Pre-populate on deployment

### ETag Optimization

```php
// First request
$response = $github->get('/repos/{owner}/{repo}/contributors');
$etag = $response->getHeader('ETag')[0];
cache()->set('github_etag', $etag);

// Subsequent requests
$response = $github->get('/repos/{owner}/{repo}/contributors', [
    'headers' => ['If-None-Match' => cache()->get('github_etag')]
]);

if ($response->getStatusCode() === 304) {
    // Not Modified - use cached data (saves API call!)
    return cache()->get('github_contributors');
}
```

---

## 5. Cron Job Implementation

### Schedule

```bash
# Monthly update - 1st of every month at 2 AM UTC
0 2 1 * * /usr/bin/php /path/to/cron/monthly-update.php >> /var/log/github-widget/cron.log 2>&1

# Optional: Weekly quick update - Sundays at 3 AM UTC
0 3 * * 0 /usr/bin/php /path/to/cron/weekly-update.php >> /var/log/github-widget/cron-weekly.log 2>&1

# Daily cleanup - 4 AM UTC
0 4 * * * /usr/bin/php /path/to/cron/cleanup.php >> /var/log/github-widget/cleanup.log 2>&1
```

### Why 2 AM UTC on the 1st?

- **Low Traffic:** Minimal user impact
- **Complete Month:** Full previous month data available
- **Resource Availability:** Server resources free
- **Global Coverage:** 2 AM UTC = 9 PM EST, 6 PM PST (still low traffic)
- **No DST Issues:** UTC avoids daylight saving complications

### Locking Mechanism

```php
class LockManager {
    public function acquire(string $processName, int $maxAge = 3600): bool {
        $lockFile = "/tmp/github-widget-{$processName}.lock";

        if (file_exists($lockFile)) {
            $age = time() - filemtime($lockFile);
            if ($age < $maxAge) {
                return false; // Another process running
            }
            unlink($lockFile); // Stale lock, remove it
        }

        file_put_contents($lockFile, json_encode([
            'pid' => getmypid(),
            'timestamp' => time()
        ]));

        return true;
    }
}
```

### Error Handling

**Failure Scenarios:**

| Failure Type | Detection | Retry Strategy | Fallback |
|-------------|-----------|----------------|----------|
| Rate Limit Hit | 403 response | Wait until reset | Use cached data |
| Network Timeout | Connection exception | 3 retries with backoff | Use cached data |
| Invalid Token | 401 unauthorized | No retry, notify admin | Use cached data |
| Database Error | PDO exception | 1 retry after 5s | Critical notification |
| Lock Held | Acquisition fails | Exit gracefully | Log warning |

**Email Notifications:**
- Success: Summary email (optional)
- Failure: Immediate critical alert
- Rate Limit Low: Warning notification

---

## 6. PHP Architecture

### Directory Structure

```
project-root/
├── .env                          # Environment variables (NOT committed)
├── .env.example                  # Template for .env
├── .gitignore                    # Excludes .env, vendor/
├── composer.json                 # Dependencies
├── composer.lock                 # Lock file
├── config/
│   ├── app.php                   # Application config
│   ├── database.php              # Database config
│   └── github.php                # GitHub API config
├── database/
│   ├── schema.sql                # Database schema
│   └── migrations/
│       ├── 001_create_tables.sql # Migration scripts
│       └── 002_add_indexes.sql
├── src/
│   ├── Config/
│   │   ├── Configuration.php     # Config singleton
│   │   └── Database.php          # DB connection
│   ├── Services/
│   │   ├── GitHubApiService.php  # API integration
│   │   ├── ContributorService.php# Business logic
│   │   └── CacheService.php      # Multi-tier caching
│   ├── Repositories/
│   │   ├── ContributorRepository.php  # Data access
│   │   ├── PeriodRepository.php
│   │   └── SyncLogRepository.php
│   ├── Models/
│   │   ├── Contributor.php       # Entity model
│   │   ├── ContributionPeriod.php
│   │   └── ContributorStat.php
│   ├── Utils/
│   │   ├── Logger.php            # PSR-3 logging
│   │   ├── RateLimiter.php       # Rate limit handler
│   │   └── LockManager.php       # Cron locking
│   └── Exceptions/
│       ├── GitHubApiException.php
│       ├── RateLimitException.php
│       └── CacheException.php
├── public/
│   ├── widget-loader.php         # Public entry point
│   ├── css/
│   │   └── github-contributors.css
│   └── js/
│       └── github-contributors.js (optional)
├── cron/
│   ├── monthly-update.php        # Monthly cron job
│   ├── weekly-update.php         # Optional weekly
│   └── cleanup.php               # Daily cleanup
├── storage/
│   └── logs/                     # Application logs
└── docs/
    ├── ARCHITECTURE.md           # System design
    ├── API.md                    # API documentation
    └── README.md                 # Installation guide
```

### Code Quality Standards

**PSR-12 Compliance:**
- 4 spaces for indentation
- Opening braces on new line
- Declare statements at top of file
- One class per file

**Type Safety:**
```php
<?php
declare(strict_types=1);

namespace ContributorsWidget\Services;

class GitHubApiService
{
    public function fetchContributors(): array
    {
        // Type hints enforced
    }
}
```

**Documentation:**
```php
/**
 * Fetch top contributors from GitHub API
 *
 * @param int $limit Maximum number of contributors to fetch
 * @return array<int, Contributor> Array of contributor objects
 * @throws GitHubApiException If API request fails
 * @throws RateLimitException If rate limit exceeded
 */
public function getTopContributors(int $limit = 100): array
```

---

## 7. Security Implementation

### SQL Injection Prevention

**✅ ALWAYS use prepared statements:**
```php
// CORRECT
$stmt = $pdo->prepare("SELECT * FROM contributors WHERE id = :id");
$stmt->execute(['id' => $userId]);

// WRONG - NEVER DO THIS
$query = "SELECT * FROM contributors WHERE id = " . $userId;
```

### XSS Prevention

**✅ Escape all output:**
```php
// In HTML
<div><?= htmlspecialchars($contributor['username'], ENT_QUOTES, 'UTF-8') ?></div>

// In URL
<a href="<?= htmlspecialchars($contributor['profile_url'], ENT_QUOTES, 'UTF-8') ?>">

// In JavaScript (if needed)
<script>
const name = <?= json_encode($contributor['username']) ?>;
</script>
```

### API Token Security

**Environment Variable Storage:**
```php
// Load from environment
$token = $_ENV['GITHUB_API_TOKEN'] ?? throw new RuntimeException('Token missing');

// Validate format
if (!preg_match('/^gh[ps]_[a-zA-Z0-9]{36,}$/', $token)) {
    throw new RuntimeException('Invalid token format');
}

// Never log token
$logger->info('API request made', ['endpoint' => '/contributors']); // No token!
```

### Input Validation

```php
class RequestValidator
{
    public function validatePeriod(string $period): string
    {
        $allowedPeriods = ['weekly', 'monthly', 'all_time'];

        if (!in_array($period, $allowedPeriods, true)) {
            throw new InvalidArgumentException('Invalid period');
        }

        return $period;
    }

    public function validateLimit(int $limit): int
    {
        if ($limit < 1 || $limit > 100) {
            throw new InvalidArgumentException('Limit must be 1-100');
        }

        return $limit;
    }
}
```

### HTTPS Enforcement

```php
// In public/widget-loader.php
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if (php_sapi_name() !== 'cli') {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}
```

---

## 8. Performance Targets

### Benchmarks

| Metric | Target | Method |
|--------|--------|--------|
| Widget Load Time | < 50ms | Cache-first architecture |
| Database Queries | 1 per load | Single optimized query |
| API Calls/Month | 2-3 | Aggressive caching |
| Cache Hit Rate | > 99% | 30-day TTL |
| Time to First Byte | < 100ms | CDN + caching |

### Optimization Techniques

1. **Database Query Optimization**
   - EXPLAIN analysis on all queries
   - Covering indexes where possible
   - Query result caching

2. **Image Optimization**
   - Lazy loading for avatars
   - Serve from GitHub CDN (avatars already optimized)
   - Width/height attributes to prevent layout shift

3. **HTTP Caching**
   ```php
   header('Cache-Control: public, max-age=3600');
   header('ETag: ' . md5($widgetHtml));
   ```

4. **Minimal JavaScript**
   - No JavaScript for basic widget
   - Optional progressive enhancement
   - Async loading if needed

---

## 9. Frontend Widget Design

### Design Principles

- **GitHub Aesthetic:** Clean, modern, professional
- **Responsive:** Mobile-first approach
- **Accessible:** WCAG 2.1 AA compliant
- **Fast:** < 50ms load time from cache
- **Beautiful:** Smooth animations, hover effects

### Tailwind CSS Implementation

**Component Structure:**
```html
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <h3 class="text-2xl font-semibold text-gray-800 mb-2">
        Top Contributors
    </h3>
    <p class="text-sm text-gray-500 mb-6">This Month</p>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <!-- Contributor Card -->
        <a href="#" class="group text-center transition-transform hover:-translate-y-2">
            <div class="relative inline-block mb-3">
                <img src="avatar.jpg"
                     alt="Username"
                     class="w-20 h-20 rounded-full border-4 border-gray-200 group-hover:border-blue-500 transition-colors"
                     loading="lazy">
                <span class="absolute -bottom-1 -right-1 bg-gradient-to-br from-purple-600 to-indigo-600 text-white text-xs font-bold rounded-full w-7 h-7 flex items-center justify-center shadow-lg">
                    #1
                </span>
            </div>
            <div class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                username
            </div>
            <div class="text-sm text-gray-500">
                123 contributions
            </div>
        </a>
    </div>

    <!-- Footer -->
    <div class="mt-6 pt-4 border-t border-gray-200 text-center">
        <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">
            View All Contributors →
        </a>
    </div>
</div>
```

### Responsive Breakpoints

- **Mobile:** 320px+ (1 column)
- **Tablet:** 768px+ (3 columns)
- **Desktop:** 1024px+ (5 columns)

### Accessibility Features

- Semantic HTML5
- ARIA labels for screen readers
- Keyboard navigation support
- High contrast ratios (WCAG AA)
- Focus indicators

---

## 10. Deployment Checklist

### Pre-Deployment

- [ ] Environment variables configured
- [ ] Database schema created
- [ ] Indexes added and verified
- [ ] Dedicated database user created
- [ ] GitHub token generated with correct permissions
- [ ] Token expiration set (90 days)
- [ ] .env added to .gitignore
- [ ] Dependencies installed via Composer
- [ ] File permissions set correctly (755 directories, 644 files)
- [ ] Log directory writable (storage/logs/)

### Deployment

- [ ] Upload files to server
- [ ] Copy .env.example to .env
- [ ] Configure .env with production values
- [ ] Run database migration
- [ ] Test database connection
- [ ] Test GitHub API connectivity
- [ ] Verify cache directory permissions
- [ ] Add cron jobs to crontab
- [ ] Test cron job manually
- [ ] Configure log rotation

### Post-Deployment

- [ ] Run initial data fetch
- [ ] Verify widget displays correctly
- [ ] Test responsive design on mobile
- [ ] Check browser console for errors
- [ ] Monitor logs for issues
- [ ] Set up monitoring alerts
- [ ] Document any custom configuration
- [ ] Create backup strategy
- [ ] Test cache invalidation
- [ ] Performance test with production data

---

## 11. Monitoring & Maintenance

### Key Metrics to Monitor

1. **API Rate Limit Usage**
   - Track remaining calls
   - Alert if < 1000 remaining
   - Daily usage report

2. **Cron Job Success Rate**
   - Track success/failure
   - Alert on failures
   - Average execution time

3. **Cache Hit Rate**
   - Target: > 99%
   - Alert if < 95%

4. **Widget Load Time**
   - Target: < 50ms
   - P95 latency monitoring

5. **Database Query Performance**
   - Slow query log
   - Query execution times

### Log Files

```bash
/var/log/github-widget/
├── application.log      # General application logs
├── cron.log            # Monthly cron job output
├── cron-weekly.log     # Weekly cron (if enabled)
├── cleanup.log         # Daily cleanup script
├── api-requests.log    # All API requests/responses
└── errors.log          # Error-only log
```

### Backup Strategy

- **Database:** Daily automated backups
- **Configuration:** Version controlled in Git
- **Logs:** Rotate weekly, keep 4 weeks
- **Cache:** No backup needed (regenerable)

---

## 12. Success Metrics

This project is considered successful when it achieves:

### Technical Excellence
- ✅ Zero SQL injection vulnerabilities
- ✅ Zero XSS vulnerabilities
- ✅ PSR-12 compliant codebase
- ✅ < 50ms widget load time
- ✅ > 99% cache hit rate
- ✅ 2-3 API calls per month

### Code Quality
- ✅ Comprehensive PHPDoc blocks
- ✅ Type hints on all methods
- ✅ Separation of concerns (SOLID)
- ✅ DRY principles followed
- ✅ No code smells

### Security
- ✅ No sensitive data in version control
- ✅ Environment-based configuration
- ✅ Token validation and rotation
- ✅ Prepared statements exclusively
- ✅ Output escaping everywhere

### Performance
- ✅ Efficient database queries
- ✅ Optimized indexes
- ✅ Multi-tier caching
- ✅ Lazy image loading
- ✅ Minimal HTTP requests

### User Experience
- ✅ Beautiful, professional design
- ✅ Responsive on all devices
- ✅ WCAG 2.1 AA compliant
- ✅ Fast loading
- ✅ Graceful error handling

### Maintainability
- ✅ Comprehensive documentation
- ✅ Clear code structure
- ✅ Easy to understand
- ✅ Simple to modify
- ✅ Future-proof architecture

---

## Appendix A: Environment Configuration

### .env.example
```bash
# GitHub API Configuration
GITHUB_API_TOKEN=your_github_token_here
GITHUB_REPO_OWNER=your_organization
GITHUB_REPO_NAME=your_repository

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=github_contributors
DB_USER=widget_user
DB_PASSWORD=secure_password_here
DB_CHARSET=utf8mb4

# Application Configuration
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://docs.example.com
ADMIN_EMAIL=admin@example.com

# Cache Configuration
CACHE_DURATION_DAYS=30
CACHE_ENABLED=true

# Logging Configuration
LOG_LEVEL=error
LOG_PATH=/var/log/github-widget

# Performance Configuration
ENABLE_QUERY_CACHE=true
LAZY_LOAD_IMAGES=true
```

---

## Appendix B: Useful Commands

### Database
```bash
# Create database
mysql -u root -p < database/schema.sql

# Backup database
mysqldump -u widget_user -p github_contributors > backup.sql

# Restore database
mysql -u widget_user -p github_contributors < backup.sql
```

### Cron Testing
```bash
# Test monthly update
php cron/monthly-update.php

# Test with verbose output
php cron/monthly-update.php --verbose

# View cron logs
tail -f /var/log/github-widget/cron.log
```

### Cache Management
```bash
# Clear all cache
php scripts/clear-cache.php

# View cache statistics
php scripts/cache-stats.php
```

### Monitoring
```bash
# Check API rate limit
curl -H "Authorization: Bearer TOKEN" https://api.github.com/rate_limit

# View last sync status
mysql -u widget_user -p github_contributors -e "SELECT * FROM api_sync_log ORDER BY started_at DESC LIMIT 5;"
```

---

**Document Version:** 1.0
**Last Updated:** 2025-10-22
**Next Review:** Before Phase 2 Implementation
