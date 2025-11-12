# GitHub Contributors Widget - Laravel Integration Context

**Document Type:** AI Assistant Integration Guide
**Target:** Claude Code, GitHub Copilot, or similar AI coding assistants
**Purpose:** Complete context for integrating the standalone GitHub Contributors Widget into a Laravel 12 documentation website
**Audience:** David Lambauer (integrator)

---

## Executive Summary

This widget displays the top 5 GitHub contributors for a repository with production-grade performance and security. It was built as a standalone PHP 8.0+ application and needs to be integrated into an existing Laravel 12 documentation website at `/var/www/html/docs-website/`.

**Key Performance Metrics:**
- Widget load time: < 50ms (from cache)
- GitHub API calls: 2-3 per month (0.04% of rate limit)
- Cache hit rate: 99%+
- Database queries: 1 per page load

**Tech Stack:**
- PHP 8.3.22 (Laravel site) / PHP 8.0+ (widget)
- MySQL for data persistence
- Tailwind CSS v3 (frontend)
- Alpine.js v3 (optional interactivity)
- GitHub REST API v3

---

## What Was Built

### Standalone Components

Located in `/var/www/html/docs-website/contributor-widget/`:

#### 1. Database Schema (`database/schema.sql`)
A normalized (3NF) database schema with 6 tables:

- **`contributors`** - GitHub contributor master data (id, github_id, username, avatar_url, profile_url)
- **`contribution_periods`** - Time periods for tracking (monthly, weekly, yearly)
- **`contributor_stats`** - Contribution counts per contributor per period
- **`api_sync_log`** - Audit trail for API syncs
- **`api_rate_limits`** - GitHub API rate limit tracking
- **`widget_cache`** - Key-value cache table

**Key indexes:**
- `contributors.github_id` (UNIQUE)
- `contributor_stats(period_id, rank_position)` - Widget query optimization
- `contribution_periods.is_current` - Current period filter

#### 2. Services (`src/Services/`)

**GitHubApiService.php**
- Handles all GitHub API interactions
- Implements rate limiting and retry logic
- Supports ETag conditional requests
- Methods: `fetchContributors()`, `fetchContributorStats()`, `checkRateLimit()`

**CacheService.php**
- 3-tier caching: Memory (1h) → Database (30d) → API
- Methods: `get()`, `set()`, `delete()`, `clear()`, `cleanup()`
- Tracks cache statistics (hits, misses, hit rate)

#### 3. Configuration (`src/Config/`)

**Configuration.php**
- Singleton pattern for app configuration
- Loads from `.env` file via vlucas/phpdotenv
- Validates GitHub token format
- Provides typed getters for config values

**Database.php**
- PDO-based database connection
- Prepared statements only (SQL injection prevention)
- Transaction support

#### 4. Utilities (`src/Utils/`)

**Logger.php** - PSR-3 compliant logging
**RateLimiter.php** - GitHub API rate limit management
**LockManager.php** - Prevents concurrent cron execution

#### 5. Cron Job (`cron/update-contributors.php`)
- Fetches contributors monthly (1st at 2 AM UTC)
- Implements locking to prevent concurrent runs
- Comprehensive error handling and logging
- Updates database with latest contributor data

#### 6. Frontend (Planned in `public/`)
- Tailwind CSS widget with responsive design
- Displays top 5 contributors with avatars
- Hover effects and smooth animations
- Mobile-first approach

---

## Integration Approach for Laravel 12

### Architecture Decision

Instead of running the widget as a standalone app, integrate it as native Laravel components:

1. **Service Layer** - Wrap existing services in Laravel service classes
2. **Eloquent Models** - Optional: Create models for contributors, periods, stats
3. **Blade Components** - Reusable UI components for rendering
4. **Artisan Commands** - Replace cron jobs with Laravel scheduler
5. **Cache Integration** - Use Laravel's Cache facade
6. **Configuration** - Add to `config/services.php`

---

## Step-by-Step Integration

### 1. Database Migration

**Create migration:**
```bash
php artisan make:migration create_github_contributors_tables
```

**Convert SQL to Laravel Schema Builder:**

The schema in `contributor-widget/database/schema.sql` needs to be converted to Laravel migration syntax. Key tables to create:

```php
// In the up() method:
Schema::create('contributors', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('github_id')->unique();
    $table->string('username');
    $table->string('avatar_url', 500)->nullable();
    $table->string('profile_url', 500)->nullable();
    $table->enum('contributor_type', ['User', 'Bot', 'Organization'])->default('User');
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('username');
    $table->index('github_id');
    $table->index('is_active');
});

Schema::create('contribution_periods', function (Blueprint $table) {
    $table->id();
    $table->enum('period_type', ['weekly', 'monthly', 'yearly']);
    $table->date('start_date');
    $table->date('end_date');
    $table->year('year');
    $table->tinyInteger('month')->nullable()->comment('1-12');
    $table->tinyInteger('week')->nullable()->comment('1-53');
    $table->boolean('is_current')->default(false);
    $table->timestamp('created_at')->useCurrent();

    $table->unique(['period_type', 'start_date']);
    $table->index('period_type');
    $table->index(['year', 'month']);
    $table->index('is_current');
    $table->index(['start_date', 'end_date']);
});

Schema::create('contributor_stats', function (Blueprint $table) {
    $table->id();
    $table->foreignId('contributor_id')->constrained()->onDelete('cascade');
    $table->foreignId('period_id')->constrained('contribution_periods')->onDelete('cascade');
    $table->unsignedInteger('contribution_count')->default(0);
    $table->unsignedInteger('commits')->default(0);
    $table->unsignedInteger('pull_requests')->default(0);
    $table->unsignedInteger('issues')->default(0);
    $table->unsignedInteger('code_reviews')->default(0);
    $table->unsignedInteger('additions')->default(0);
    $table->unsignedInteger('deletions')->default(0);
    $table->tinyInteger('rank_position')->nullable();
    $table->timestamps();

    $table->unique(['contributor_id', 'period_id']);
    $table->index(['period_id', 'rank_position']);
    $table->index('contribution_count');
});

// Also create: api_sync_log, api_rate_limits, widget_cache
// (see schema.sql for complete definitions)
```

**Run migration:**
```bash
php artisan migrate
```

---

### 2. Laravel Service Class

**Create service:**
```bash
php artisan make:class Services/GitHubContributorsService
```

**Service location:** `app/Services/GitHubContributorsService.php`

**Purpose:** Wraps the standalone widget's services and provides Laravel-friendly API

**Key responsibilities:**
- Fetch top N contributors from cache or database
- Integrate with Laravel's Cache facade
- Use Laravel's DB facade for queries
- Return data formatted for Blade components

**Example structure:**
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubContributorsService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('services.github');
    }

    /**
     * Get top contributors with 3-tier caching
     */
    public function getTopContributors(int $limit = 5): array
    {
        // Tier 1: Memory cache (1 hour)
        return Cache::remember('github_contributors_top_5', 3600, function () use ($limit) {

            // Tier 2: Database cache (30 days) + query
            return $this->fetchFromDatabase($limit);
        });
    }

    /**
     * Fetch contributors from database
     */
    private function fetchFromDatabase(int $limit): array
    {
        return DB::table('contributor_stats as cs')
            ->join('contributors as c', 'cs.contributor_id', '=', 'c.id')
            ->join('contribution_periods as cp', 'cs.period_id', '=', 'cp.id')
            ->where('cp.is_current', true)
            ->where('cp.period_type', 'monthly')
            ->where('cs.rank_position', '<=', $limit)
            ->orderBy('cs.rank_position')
            ->limit($limit)
            ->select([
                'c.username',
                'c.avatar_url',
                'c.profile_url',
                'cs.contribution_count',
                'cs.rank_position'
            ])
            ->get()
            ->toArray();
    }

    /**
     * Update contributors from GitHub API
     * Called by scheduled command
     */
    public function updateFromGitHub(): bool
    {
        // Implement GitHub API fetch logic
        // Based on contributor-widget/src/Services/GitHubApiService.php

        // Check rate limit
        $rateLimit = $this->checkRateLimit();
        if ($rateLimit['remaining'] < 100) {
            Log::warning('GitHub API rate limit low', $rateLimit);
            return false;
        }

        // Fetch contributors
        $contributors = $this->fetchContributorsFromAPI();

        // Store in database
        $this->storeContributors($contributors);

        // Clear cache
        Cache::forget('github_contributors_top_5');

        return true;
    }

    private function fetchContributorsFromAPI(): array
    {
        $response = Http::withToken($this->config['token'])
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->get("https://api.github.com/repos/{$this->config['repo_owner']}/{$this->config['repo_name']}/contributors", [
                'per_page' => 100,
                'page' => 1,
            ]);

        if ($response->failed()) {
            Log::error('GitHub API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('GitHub API request failed');
        }

        return $response->json();
    }

    private function checkRateLimit(): array
    {
        $response = Http::withToken($this->config['token'])
            ->get('https://api.github.com/rate_limit');

        return $response->json()['resources']['core'] ?? [
            'limit' => 5000,
            'remaining' => 5000,
            'reset' => time() + 3600,
        ];
    }

    private function storeContributors(array $contributors): void
    {
        // Implementation: Store contributors in database
        // Create period if not exists
        // Update contributor_stats with ranks
        // This logic should mirror cron/update-contributors.php
    }
}
```

---

### 3. Configuration Setup

**Add to `config/services.php`:**

```php
'github' => [
    'token' => env('GITHUB_API_TOKEN'),
    'repo_owner' => env('GITHUB_REPO_OWNER', 'mage-os'),
    'repo_name' => env('GITHUB_REPO_NAME', 'magento2'),
    'api_url' => 'https://api.github.com',
    'api_version' => '2022-11-28',
    'timeout' => 30,
    'cache_ttl_days' => 30,
],
```

**Add to `.env`:**

```bash
GITHUB_API_TOKEN=ghp_provided_by_magento_association
GITHUB_REPO_OWNER=mage-os
GITHUB_REPO_NAME=magento2
```

**Important:** The GitHub API token will be provided by the Magento Association. Do not create a new token or include token creation instructions.

---

### 4. Blade Component

**Create component:**
```bash
php artisan make:component GitHubContributors
```

**Files created:**
- `app/View/Components/GitHubContributors.php` (component class)
- `resources/views/components/github-contributors.blade.php` (template)

**Component class (`app/View/Components/GitHubContributors.php`):**

```php
<?php

namespace App\View\Components;

use App\Services\GitHubContributorsService;
use Illuminate\View\Component;

class GitHubContributors extends Component
{
    public array $contributors;
    public int $limit;

    public function __construct(
        GitHubContributorsService $service,
        int $limit = 5
    ) {
        $this->limit = $limit;
        $this->contributors = $service->getTopContributors($limit);
    }

    public function render()
    {
        return view('components.github-contributors');
    }
}
```

**Blade template (`resources/views/components/github-contributors.blade.php`):**

Reference the frontend design from `contributor-widget/public/` or `contributor-widget/docs/TECHNICAL_SPECIFICATION.md` section 9.

Example structure:
```blade
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <h3 class="text-2xl font-semibold text-gray-800 mb-2">Top Contributors</h3>
    <p class="text-sm text-gray-500 mb-6">This Month</p>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach ($contributors as $contributor)
            <a href="{{ $contributor->profile_url }}"
               class="group text-center transition-transform hover:-translate-y-2"
               target="_blank"
               rel="noopener noreferrer">
                <div class="relative inline-block mb-3">
                    <img src="{{ $contributor->avatar_url }}"
                         alt="{{ $contributor->username }}"
                         class="w-20 h-20 rounded-full border-4 border-gray-200 group-hover:border-blue-500 transition-colors"
                         loading="lazy"
                         width="80"
                         height="80">
                    <span class="absolute -bottom-1 -right-1 bg-gradient-to-br from-purple-600 to-indigo-600 text-white text-xs font-bold rounded-full w-7 h-7 flex items-center justify-center shadow-lg">
                        #{{ $contributor->rank_position }}
                    </span>
                </div>
                <div class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                    {{ $contributor->username }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ number_format($contributor->contribution_count) }} contributions
                </div>
            </a>
        @endforeach
    </div>

    @if (empty($contributors))
        <div class="text-center text-gray-500 py-8">
            <p>No contributor data available yet.</p>
            <p class="text-sm mt-2">Run <code class="bg-gray-100 px-2 py-1 rounded">php artisan github:update-contributors</code></p>
        </div>
    @endif
</div>
```

**Usage in views:**
```blade
<x-github-contributors :limit="5" />
```

---

### 5. Artisan Commands

Replace the standalone cron job with Laravel Artisan commands.

**Create command:**
```bash
php artisan make:command UpdateGitHubContributors
```

**Command location:** `app/Console/Commands/UpdateGitHubContributors.php`

**Command structure:**
```php
<?php

namespace App\Console\Commands;

use App\Services\GitHubContributorsService;
use Illuminate\Console\Command;

class UpdateGitHubContributors extends Command
{
    protected $signature = 'github:update-contributors';
    protected $description = 'Update GitHub contributors data from API';

    public function __construct(
        private GitHubContributorsService $service
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Updating GitHub contributors...');

        try {
            $success = $this->service->updateFromGitHub();

            if ($success) {
                $this->info('Contributors updated successfully!');
                return Command::SUCCESS;
            }

            $this->error('Failed to update contributors');
            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
```

**Create cleanup command:**
```bash
php artisan make:command CleanupGitHubCache
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupGitHubCache extends Command
{
    protected $signature = 'github:cleanup-cache';
    protected $description = 'Clean up expired GitHub cache entries';

    public function handle(): int
    {
        $deleted = DB::table('widget_cache')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Cleaned up {$deleted} expired cache entries");

        return Command::SUCCESS;
    }
}
```

---

### 6. Laravel Scheduler Configuration

**Add to `app/Console/Kernel.php`:**

```php
protected function schedule(Schedule $schedule): void
{
    // Update contributors monthly on 1st at 2 AM UTC
    $schedule->command('github:update-contributors')
        ->monthlyOn(1, '02:00')
        ->timezone('UTC')
        ->onFailure(function () {
            // Send notification on failure
            Log::error('GitHub contributors update failed');
        })
        ->onSuccess(function () {
            Log::info('GitHub contributors update completed');
        });

    // Cleanup expired cache daily at 4 AM UTC
    $schedule->command('github:cleanup-cache')
        ->dailyAt('04:00')
        ->timezone('UTC');
}
```

**Ensure Laravel scheduler is running:**

The scheduler needs a cron entry to run:
```bash
* * * * * cd /var/www/html/docs-website && php artisan schedule:run >> /dev/null 2>&1
```

---

### 7. Cache Integration with Laravel

**Use Laravel's Cache facade instead of standalone CacheService:**

The standalone widget uses a custom `CacheService` class. In Laravel, replace this with the Cache facade:

**Memory cache (1 hour):**
```php
use Illuminate\Support\Facades\Cache;

$contributors = Cache::remember('github_contributors_top_5', 3600, function () {
    // Fetch from database
});
```

**Database cache driver:**

Laravel's cache system supports database caching. Configure in `config/cache.php`:

```php
'stores' => [
    'database' => [
        'driver' => 'database',
        'table' => 'cache',
        'connection' => null,
        'lock_connection' => null,
    ],
],
```

Run cache table migration:
```bash
php artisan cache:table
php artisan migrate
```

**Note:** The widget's `widget_cache` table can be used as a fallback or deprecated in favor of Laravel's `cache` table.

---

## File Mapping Reference

| Standalone Widget File | Laravel Integration Target |
|------------------------|----------------------------|
| `database/schema.sql` | Laravel migration (convert to Schema Builder) |
| `src/Services/GitHubApiService.php` | `app/Services/GitHubContributorsService.php` (integrate logic) |
| `src/Services/CacheService.php` | Laravel Cache facade (replace) |
| `src/Config/Configuration.php` | `config/services.php` + `.env` (replace) |
| `src/Config/Database.php` | Laravel DB facade (replace) |
| `cron/update-contributors.php` | `app/Console/Commands/UpdateGitHubContributors.php` |
| `public/` (frontend) | `resources/views/components/github-contributors.blade.php` |
| `.env.example` | Add to existing `.env` |

---

## Testing Integration

After integration, test each component:

### 1. Database Migration
```bash
php artisan migrate
php artisan tinker
>>> DB::table('contributors')->count(); // Should be 0 initially
```

### 2. Service Layer
```bash
php artisan tinker
>>> $service = app(\App\Services\GitHubContributorsService::class);
>>> $contributors = $service->getTopContributors(5);
>>> dd($contributors);
```

### 3. Update Command
```bash
php artisan github:update-contributors
# Should fetch from GitHub API and populate database
```

### 4. Blade Component
Create a test route:
```php
Route::get('/test-contributors', function () {
    return view('test-contributors');
});
```

Create view `resources/views/test-contributors.blade.php`:
```blade
<x-github-contributors :limit="5" />
```

Visit `/test-contributors` and verify widget renders.

### 5. Cache Verification
```bash
php artisan tinker
>>> Cache::has('github_contributors_top_5'); // Should be true after first load
>>> Cache::forget('github_contributors_top_5');
>>> Cache::has('github_contributors_top_5'); // Should be false
```

### 6. Scheduled Tasks
```bash
# Test scheduler (runs all due commands)
php artisan schedule:run

# Test specific command
php artisan schedule:test
```

---

## Performance Optimization Checklist

- [ ] Database indexes created (especially `contributor_stats.period_id + rank_position`)
- [ ] Cache warming implemented (pre-populate cache after update)
- [ ] Query optimization verified with `EXPLAIN`
- [ ] Lazy loading enabled for contributor avatars
- [ ] Browser caching headers set for avatar images
- [ ] Memory cache enabled in Laravel (array or redis driver)
- [ ] Database query count verified (should be 1 per page load)

---

## Security Checklist

- [ ] GitHub API token stored in `.env` (never in code)
- [ ] All database queries use parameter binding (no raw SQL)
- [ ] Output escaping in Blade templates (automatic with `{{ }}`)
- [ ] HTTPS enforced for avatar image URLs
- [ ] Rate limit checks before API calls
- [ ] Error messages don't expose sensitive data
- [ ] Token format validation on load
- [ ] CORS headers configured if widget used via API

---

## Common Integration Issues

### Issue: "Table 'contributors' doesn't exist"
**Solution:** Run migrations: `php artisan migrate`

### Issue: "No contributors returned"
**Solution:** Run update command: `php artisan github:update-contributors`

### Issue: "GitHub API rate limit exceeded"
**Solution:** Check last sync time. Wait for rate limit reset or use cached data.

### Issue: "Call to undefined method"
**Solution:** Ensure service is registered in container or use dependency injection in constructor

### Issue: "Cache not working"
**Solution:** Verify cache driver configured: `php artisan config:cache` and check `CACHE_DRIVER` in `.env`

---

## GitHub API Notes

**Important:** The GitHub API token comes from the Magento Association website. It is already configured and should not be recreated.

**API Endpoints Used:**
- `GET /repos/{owner}/{repo}/contributors` - Fetch contributors list
- `GET /rate_limit` - Check rate limit status

**Rate Limits:**
- Authenticated: 5,000 requests/hour
- Widget usage: 2-3 requests/month (0.04%)

**Best Practices:**
- Always check rate limit before expensive operations
- Use ETags for conditional requests (304 Not Modified)
- Implement exponential backoff on failures
- Cache aggressively (30 days in database)
- Fall back to cached data on API errors

---

## Deployment Checklist

- [ ] Migration files created and tested
- [ ] Environment variables added to `.env`
- [ ] Service class implemented and registered
- [ ] Artisan commands created and tested
- [ ] Blade component created and styled
- [ ] Scheduler configured in Kernel.php
- [ ] Cron entry added for Laravel scheduler
- [ ] Initial data fetch completed
- [ ] Cache warming implemented
- [ ] Error logging configured
- [ ] Monitoring alerts set up (optional)

---

## Monitoring Recommendations

**Key metrics to track:**
- Cache hit rate (target: 99%+)
- Widget load time (target: <50ms)
- GitHub API calls per month (target: 2-3)
- Update command success rate
- Database query performance

**Laravel logging:**
```php
Log::info('GitHub contributors updated', [
    'count' => count($contributors),
    'cache_cleared' => true,
    'execution_time_ms' => $executionTime,
]);
```

---

## Additional Resources

**Standalone Widget Documentation:**
- `contributor-widget/README.md` - Project overview
- `contributor-widget/docs/TECHNICAL_SPECIFICATION.md` - Complete technical specs
- `contributor-widget/docs/QUICKSTART.md` - Setup guide
- `contributor-widget/.env.example` - Environment variables template

**Laravel Documentation:**
- Task Scheduling: https://laravel.com/docs/12.x/scheduling
- Cache: https://laravel.com/docs/12.x/cache
- HTTP Client: https://laravel.com/docs/12.x/http-client
- Blade Components: https://laravel.com/docs/12.x/blade#components

---

## Summary

This integration transforms a standalone GitHub contributors widget into native Laravel components:

1. **Database**: Migrate SQL schema to Laravel migrations
2. **Service**: Wrap widget logic in `GitHubContributorsService`
3. **UI**: Create Blade component for rendering
4. **Scheduling**: Replace cron with Laravel scheduler
5. **Caching**: Use Laravel Cache facade
6. **Configuration**: Add to `config/services.php`

**Result:** A fully integrated, production-ready widget that displays GitHub contributors with excellent performance and minimal API usage.

**Integration Time Estimate:** 4-6 hours for experienced Laravel developer

---

**Document Version:** 1.0
**Created:** 2025-11-04
**Last Updated:** 2025-11-04
**For:** David Lambauer
**Project:** Laravel 12 Documentation Website at `/var/www/html/docs-website/`
