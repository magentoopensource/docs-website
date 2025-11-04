#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * GitHub Contributors - Monthly Update Cron Job
 *
 * Automatically fetches and updates contributor statistics from GitHub API
 * Runs monthly on the 1st at 2 AM UTC via cron
 *
 * Crontab entry:
 * 0 2 1 * * /usr/bin/php /path/to/cron/update-contributors.php >> /path/to/storage/logs/cron.log 2>&1
 *
 * Features:
 * - Lock management (prevents concurrent runs)
 * - Comprehensive error handling
 * - Detailed logging (PSR-3 compliant)
 * - Rate limit awareness
 * - Transaction support
 * - Email notifications on failure
 * - Performance metrics
 *
 * @author GitHub Contributors Widget
 * @version 1.0.0
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Don't display errors (they go to log)
ini_set('log_errors', '1');

// Set timezone to UTC
date_default_timezone_set('UTC');

// Bootstrap
require_once __DIR__ . '/../vendor/autoload.php';

use ContributorsWidget\Config\{Configuration, Database};
use ContributorsWidget\Utils\{Logger, LockManager};
use ContributorsWidget\Services\{GitHubApiService, CacheService};
use ContributorsWidget\Exceptions\{GitHubApiException, RateLimitException};

// ============================================================================
// Configuration
// ============================================================================

const LOCK_NAME = 'monthly_contributors_update';
const LOCK_MAX_AGE = 3600; // 1 hour - if lock is older, consider it stale
const EMAIL_ON_ERROR = true; // Send email notification on critical errors
const RETRY_ATTEMPTS = 3; // Number of retry attempts on transient failures
const RETRY_DELAY = 60; // Seconds to wait between retries

// ============================================================================
// Main Execution
// ============================================================================

$startTime = microtime(true);
$logger = null;
$lockManager = null;
$exitCode = 0;

try {
    // Initialize configuration
    $config = Configuration::getInstance();

    // Initialize logger
    $logger = new Logger($config);
    $logger->info('=== CRON JOB STARTED ===', [
        'script' => basename(__FILE__),
        'pid' => getmypid(),
        'user' => get_current_user(),
        'hostname' => gethostname(),
        'timestamp' => date('Y-m-d H:i:s T')
    ]);

    // Initialize lock manager
    $lockManager = new LockManager($config);

    // Try to acquire lock
    $logger->info('Attempting to acquire lock', ['lock_name' => LOCK_NAME]);

    if (!$lockManager->acquire(LOCK_NAME, LOCK_MAX_AGE)) {
        $lockInfo = $lockManager->getLockInfo(LOCK_NAME);

        $logger->warning('Another instance is already running', [
            'lock_name' => LOCK_NAME,
            'lock_info' => $lockInfo
        ]);

        echo "Another instance is already running (PID: {$lockInfo['pid']})\n";
        echo "Lock acquired at: {$lockInfo['acquired_at']}\n";
        echo "Lock age: {$lockInfo['age_seconds']}s\n";

        exit(0); // Exit gracefully - not an error
    }

    $logger->info('Lock acquired successfully', ['lock_name' => LOCK_NAME]);

    // Register shutdown function to ensure lock is released
    register_shutdown_function(function() use ($lockManager, $logger) {
        if ($lockManager && $lockManager->isLocked(LOCK_NAME)) {
            $lockManager->release(LOCK_NAME);
            if ($logger) {
                $logger->info('Lock released in shutdown function');
            }
        }
    });

    // Initialize database
    $db = Database::getInstance($config);

    if (!$db->testConnection()) {
        throw new \RuntimeException('Database connection failed');
    }

    $logger->info('Database connection established');

    // Initialize services
    $cache = new CacheService($config, $db, $logger);
    $github = new GitHubApiService($config, $logger);

    $logger->info('Services initialized successfully');

    // Get repository info
    $repo = $config->getGithubRepo();
    $logger->info('Target repository', [
        'owner' => $repo['owner'],
        'repo' => $repo['repo']
    ]);

    // ========================================================================
    // Pre-flight Checks
    // ========================================================================

    $logger->info('Running pre-flight checks...');

    // Check rate limit before starting
    $rateLimit = $github->checkRateLimit();
    $logger->info('GitHub API rate limit status', [
        'limit' => $rateLimit['limit'],
        'remaining' => $rateLimit['remaining'],
        'reset_at' => date('Y-m-d H:i:s', $rateLimit['reset']),
        'usage_percentage' => $github->getRateLimiter()->getUsagePercentage()
    ]);

    // Check if we have enough API calls (need at least 10 for safe operation)
    if ($rateLimit['remaining'] < 10) {
        $resetIn = $rateLimit['reset'] - time();
        throw new RateLimitException(
            "Insufficient API calls remaining. Reset in {$resetIn} seconds",
            $rateLimit['remaining'],
            $rateLimit['reset']
        );
    }

    $logger->info('Pre-flight checks passed');

    // ========================================================================
    // Fetch Contributors Data
    // ========================================================================

    $logger->info('Fetching contributors data from GitHub...');

    $contributors = null;
    $attempt = 0;
    $lastError = null;

    // Retry logic for transient failures
    while ($attempt < RETRY_ATTEMPTS) {
        $attempt++;

        try {
            $logger->info("Fetch attempt {$attempt}/{RETRY_ATTEMPTS}");

            // Fetch top 100 contributors (paginated if needed)
            $contributors = $github->fetchContributors(100, 1);

            $logger->info('Contributors fetched successfully', [
                'count' => count($contributors),
                'api_calls' => $github->getApiCallCount()
            ]);

            break; // Success - exit retry loop

        } catch (RateLimitException $e) {
            // Rate limit hit - can't retry immediately
            $logger->error('Rate limit exception', [
                'remaining' => $e->getRemaining(),
                'reset_at' => $e->getResetTime(),
                'seconds_until_reset' => $e->getSecondsUntilReset()
            ]);

            throw $e; // Re-throw - can't recover from rate limit

        } catch (GitHubApiException $e) {
            $lastError = $e;

            $logger->warning("Fetch attempt {$attempt} failed", [
                'error' => $e->getMessage(),
                'status_code' => $e->getCode()
            ]);

            if ($attempt < RETRY_ATTEMPTS) {
                $logger->info("Waiting {RETRY_DELAY} seconds before retry...");
                sleep(RETRY_DELAY);
            }
        }
    }

    // If all retries failed, throw last error
    if ($contributors === null) {
        throw new \RuntimeException(
            'Failed to fetch contributors after ' . RETRY_ATTEMPTS . ' attempts: ' .
            ($lastError ? $lastError->getMessage() : 'Unknown error')
        );
    }

    // ========================================================================
    // Update Database
    // ========================================================================

    $logger->info('Updating database...');

    $pdo = $db->getConnection();
    $pdo->beginTransaction();

    try {
        // Get current period
        $currentPeriod = getCurrentPeriod($pdo, $logger);

        if (!$currentPeriod) {
            throw new \RuntimeException('Failed to get or create current period');
        }

        $logger->info('Current period', [
            'period_id' => $currentPeriod['id'],
            'type' => $currentPeriod['period_type'],
            'start_date' => $currentPeriod['start_date'],
            'end_date' => $currentPeriod['end_date']
        ]);

        // Update contributors and stats
        $stats = updateContributorsData($pdo, $contributors, $currentPeriod, $logger);

        // Log sync information
        logSyncActivity($pdo, 'monthly_update', 'success', $stats, $github, $logger);

        // Commit transaction
        $pdo->commit();

        $logger->info('Database updated successfully', $stats);

    } catch (\Exception $e) {
        $pdo->rollBack();

        $logger->error('Database update failed - transaction rolled back', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Log failed sync
        try {
            logSyncActivity($pdo, 'monthly_update', 'failed', [], $github, $logger, $e->getMessage());
        } catch (\Exception $logError) {
            $logger->error('Failed to log sync activity', [
                'error' => $logError->getMessage()
            ]);
        }

        throw $e;
    }

    // ========================================================================
    // Update Cache
    // ========================================================================

    $logger->info('Updating cache...');

    // Clear old cache entries
    $clearedCount = $cache->clear('contributors_*');
    $logger->info('Old cache entries cleared', ['count' => $clearedCount]);

    // Cache top 5 contributors for widget
    $top5 = array_slice($contributors, 0, 5);
    $cacheKey = "contributors_top5_{$repo['owner']}_{$repo['repo']}";

    if ($cache->set($cacheKey, $top5, 30)) { // 30 days TTL
        $logger->info('Top 5 contributors cached', [
            'cache_key' => $cacheKey,
            'ttl_days' => 30
        ]);
    } else {
        $logger->warning('Failed to cache top 5 contributors');
    }

    // Cache all contributors for other uses
    $cacheKeyAll = "contributors_all_{$repo['owner']}_{$repo['repo']}";

    if ($cache->set($cacheKeyAll, $contributors, 30)) {
        $logger->info('All contributors cached', [
            'cache_key' => $cacheKeyAll,
            'count' => count($contributors),
            'ttl_days' => 30
        ]);
    }

    // Clean up expired cache entries
    $expiredCount = $cache->cleanup();
    $logger->info('Expired cache entries removed', ['count' => $expiredCount]);

    // ========================================================================
    // Success Summary
    // ========================================================================

    $duration = round(microtime(true) - $startTime, 2);

    $logger->info('=== CRON JOB COMPLETED SUCCESSFULLY ===', [
        'duration_seconds' => $duration,
        'contributors_processed' => count($contributors),
        'api_calls_made' => $github->getApiCallCount(),
        'rate_limit_remaining' => $github->getRateLimiter()->getRemaining(),
        'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
    ]);

    echo "\n✅ SUCCESS\n\n";
    echo "Contributors processed: " . count($contributors) . "\n";
    echo "Duration: {$duration}s\n";
    echo "API calls: " . $github->getApiCallCount() . "\n";
    echo "Rate limit remaining: " . $github->getRateLimiter()->getRemaining() . "\n";
    echo "\nCheck logs: storage/logs/github-widget.log\n\n";

} catch (\Exception $e) {
    $exitCode = 1;
    $duration = round(microtime(true) - $startTime, 2);

    if ($logger) {
        $logger->critical('=== CRON JOB FAILED ===', [
            'duration_seconds' => $duration,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    echo "\n❌ ERROR\n\n";
    echo "Message: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}:{$e->getLine()}\n";
    echo "Duration: {$duration}s\n";
    echo "\nCheck logs: storage/logs/github-widget.log\n\n";

    // Send email notification on error
    if (EMAIL_ON_ERROR && $config) {
        sendErrorNotification($config, $e, $duration, $logger);
    }

} finally {
    // Always release lock
    if ($lockManager && $lockManager->isLocked(LOCK_NAME)) {
        $lockManager->release(LOCK_NAME);

        if ($logger) {
            $logger->info('Lock released', ['lock_name' => LOCK_NAME]);
        }
    }
}

exit($exitCode);

// ============================================================================
// Helper Functions
// ============================================================================

/**
 * Get or create current monthly period
 */
function getCurrentPeriod(\PDO $pdo, Logger $logger): ?array
{
    // Check if current period exists
    $stmt = $pdo->prepare("
        SELECT * FROM contribution_periods
        WHERE period_type = 'monthly'
        AND is_current = 1
        AND CURDATE() BETWEEN start_date AND end_date
        LIMIT 1
    ");

    $stmt->execute();
    $period = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($period) {
        $logger->debug('Found existing current period', ['period_id' => $period['id']]);
        return $period;
    }

    // Create new period for current month
    $logger->info('Creating new period for current month');

    $year = (int) date('Y');
    $month = (int) date('m');
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');

    // Mark previous periods as not current
    $pdo->exec("UPDATE contribution_periods SET is_current = 0 WHERE period_type = 'monthly'");

    // Insert new period
    $stmt = $pdo->prepare("
        INSERT INTO contribution_periods
        (period_type, start_date, end_date, year, month, is_current, created_at)
        VALUES ('monthly', ?, ?, ?, ?, 1, NOW())
    ");

    $stmt->execute([$startDate, $endDate, $year, $month]);

    $periodId = $pdo->lastInsertId();

    $logger->info('New period created', [
        'period_id' => $periodId,
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);

    return [
        'id' => $periodId,
        'period_type' => 'monthly',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'year' => $year,
        'month' => $month,
        'is_current' => 1
    ];
}

/**
 * Update contributors and their statistics
 */
function updateContributorsData(\PDO $pdo, array $contributors, array $period, Logger $logger): array
{
    $stats = [
        'contributors_updated' => 0,
        'contributors_new' => 0,
        'stats_updated' => 0
    ];

    foreach ($contributors as $index => $contributor) {
        $rank = $index + 1;

        // Insert or update contributor
        $stmt = $pdo->prepare("
            INSERT INTO contributors
            (github_id, username, avatar_url, profile_url, type, is_active, last_synced_at, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                username = VALUES(username),
                avatar_url = VALUES(avatar_url),
                profile_url = VALUES(profile_url),
                type = VALUES(type),
                is_active = 1,
                last_synced_at = NOW(),
                updated_at = NOW()
        ");

        $stmt->execute([
            $contributor['id'] ?? 0,
            $contributor['login'] ?? 'unknown',
            $contributor['avatar_url'] ?? '',
            $contributor['html_url'] ?? '',
            $contributor['type'] ?? 'User'
        ]);

        if ($stmt->rowCount() > 0) {
            $isNew = ($pdo->lastInsertId() !== '0');

            if ($isNew) {
                $stats['contributors_new']++;
            } else {
                $stats['contributors_updated']++;
            }
        }

        // Get contributor ID
        $contributorId = $pdo->lastInsertId() ?: $pdo->query("
            SELECT id FROM contributors WHERE github_id = {$contributor['id']}
        ")->fetchColumn();

        // Insert or update contributor stats for this period
        $stmt = $pdo->prepare("
            INSERT INTO contributor_stats
            (contributor_id, period_id, contribution_count, commits, rank_position, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                contribution_count = VALUES(contribution_count),
                commits = VALUES(commits),
                rank_position = VALUES(rank_position),
                updated_at = NOW()
        ");

        $stmt->execute([
            $contributorId,
            $period['id'],
            $contributor['contributions'] ?? 0,
            $contributor['contributions'] ?? 0, // Assume contributions are commits
            $rank
        ]);

        if ($stmt->rowCount() > 0) {
            $stats['stats_updated']++;
        }
    }

    return $stats;
}

/**
 * Log sync activity to database
 */
function logSyncActivity(
    \PDO $pdo,
    string $syncType,
    string $status,
    array $stats,
    GitHubApiService $github,
    Logger $logger,
    ?string $errorMessage = null
): void
{
    try {
        $stmt = $pdo->prepare("
            INSERT INTO api_sync_log
            (sync_type, status, contributors_fetched, api_calls_made, duration_seconds, error_message, started_at, completed_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $syncType,
            $status,
            $stats['contributors_new'] ?? 0 + $stats['contributors_updated'] ?? 0,
            $github->getApiCallCount(),
            0, // Duration will be calculated by database
            $errorMessage
        ]);

        $logger->debug('Sync activity logged', [
            'sync_type' => $syncType,
            'status' => $status
        ]);

    } catch (\Exception $e) {
        $logger->error('Failed to log sync activity', [
            'error' => $e->getMessage()
        ]);
        // Don't throw - this is not critical
    }
}

/**
 * Send error notification email
 */
function sendErrorNotification(Configuration $config, \Exception $e, float $duration, ?Logger $logger): void
{
    try {
        $adminEmail = $config->get('ADMIN_EMAIL');

        if (!$adminEmail) {
            if ($logger) {
                $logger->debug('Admin email not configured - skipping notification');
            }
            return;
        }

        $subject = '[GitHub Contributors Widget] Cron Job Failed';

        $body = "GitHub Contributors Widget - Cron Job Failure\n\n";
        $body .= "Timestamp: " . date('Y-m-d H:i:s T') . "\n";
        $body .= "Hostname: " . gethostname() . "\n";
        $body .= "Duration: {$duration}s\n\n";
        $body .= "Error Message:\n{$e->getMessage()}\n\n";
        $body .= "File: {$e->getFile()}:{$e->getLine()}\n\n";
        $body .= "Stack Trace:\n{$e->getTraceAsString()}\n\n";
        $body .= "Please check the logs for more details:\n";
        $body .= "storage/logs/github-widget.log\n";

        $headers = "From: noreply@" . gethostname() . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $sent = mail($adminEmail, $subject, $body, $headers);

        if ($sent && $logger) {
            $logger->info('Error notification email sent', ['to' => $adminEmail]);
        } elseif ($logger) {
            $logger->warning('Failed to send error notification email');
        }

    } catch (\Exception $emailError) {
        if ($logger) {
            $logger->error('Error sending notification email', [
                'error' => $emailError->getMessage()
            ]);
        }
    }
}
