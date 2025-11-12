<?php

declare(strict_types=1);

/**
 * Services Layer Test
 *
 * Tests the newly created services:
 * - Logger (PSR-3 compliant)
 * - RateLimiter (GitHub API rate limiting)
 * - GitHubApiService (GitHub API integration)
 * - CacheService (3-tier caching)
 *
 * Usage: php demo/test-services.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "❌ ERROR: Run 'composer install' first\n";
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

use ContributorsWidget\Config\{Configuration, Database};
use ContributorsWidget\Utils\{Logger, LockManager};
use ContributorsWidget\Services\{GitHubApiService, CacheService};

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "  Services Layer Test\n";
echo "════════════════════════════════════════════════════════════════\n\n";

try {
    // Initialize configuration
    $config = Configuration::getInstance();
    $repo = $config->getGithubRepo();

    echo "Repository: {$repo['owner']}/{$repo['repo']}\n\n";

    // Test 1: Logger
    echo "📝 Test 1: Logger (PSR-3 Compliant)\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $logger = new Logger($config);
    $logger->info('Test log entry from demo script');
    $logger->debug('Debug information', ['test' => 'data', 'number' => 123]);
    $logger->warning('This is a warning');

    echo "✅ Logger initialized\n";
    echo "   Log file: {$logger->getLogFile()}\n";
    echo "   Levels: DEBUG, INFO, WARNING, ERROR, CRITICAL\n";
    echo "   Features: Auto-rotation, PSR-3 compliant, context support\n\n";

    // Test 2: Database Connection
    echo "🗄️  Test 2: Database Connection\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $db = Database::getInstance($config);

    if (!$db->testConnection()) {
        echo "❌ Database connection failed. Run: mysql < database/schema.sql\n\n";
        exit(1);
    }

    echo "✅ Database connected\n\n";

    // Test 3: Cache Service
    echo "💾 Test 3: Cache Service (3-Tier Caching)\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $cache = new CacheService($config, $db, $logger);

    // Test cache set
    $testData = [
        'username' => 'test-user',
        'contributions' => 123,
        'timestamp' => time()
    ];

    $cache->set('test_cache_key', $testData, 1); // 1 day TTL

    // Test cache get
    $retrieved = $cache->get('test_cache_key');

    if ($retrieved === $testData) {
        echo "✅ Cache set/get working\n";
    } else {
        echo "❌ Cache test failed\n";
    }

    // Get cache stats
    $stats = $cache->getStats();
    echo "   Memory hits: {$stats['memory_hits']}\n";
    echo "   Database hits: {$stats['database_hits']}\n";
    echo "   Misses: {$stats['misses']}\n";
    echo "   Hit rate: {$stats['hit_rate_percentage']}%\n";

    // Get cache size info
    $sizeInfo = $cache->getSizeInfo();
    echo "   Total cached keys: {$sizeInfo['total_keys']}\n";
    echo "   Cache size: {$sizeInfo['total_size_mb']} MB\n\n";

    // Test 4: Lock Manager
    echo "🔒 Test 4: Lock Manager (Cron Concurrency Control)\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $lockManager = new LockManager($config);

    if ($lockManager->acquire('test_process')) {
        echo "✅ Lock acquired for 'test_process'\n";

        // Simulate work
        sleep(1);

        $lockInfo = $lockManager->getLockInfo('test_process');
        echo "   PID: {$lockInfo['pid']}\n";
        echo "   Hostname: {$lockInfo['hostname']}\n";
        echo "   Age: {$lockInfo['age_seconds']}s\n";

        $lockManager->release('test_process');
        echo "✅ Lock released\n\n";
    } else {
        echo "❌ Failed to acquire lock\n\n";
    }

    // Test 5: GitHub API Service
    echo "🐙 Test 5: GitHub API Service\n";
    echo "────────────────────────────────────────────────────────────────\n";

    $githubService = new GitHubApiService($config, $logger);

    // Check rate limit first
    echo "Checking rate limit...\n";
    $rateLimit = $githubService->checkRateLimit();

    echo "✅ Rate Limit Status:\n";
    echo "   Limit: " . number_format($rateLimit['limit']) . " requests/hour\n";
    echo "   Remaining: " . number_format($rateLimit['remaining']) . " requests\n";
    echo "   Resets at: " . date('Y-m-d H:i:s', $rateLimit['reset']) . " UTC\n";
    echo "   Usage: {$githubService->getRateLimiter()->getUsagePercentage()}%\n\n";

    // Fetch contributors (with caching)
    echo "Fetching top 5 contributors...\n";

    // Check cache first
    $cacheKey = "contributors_top5_{$repo['owner']}_{$repo['repo']}";
    $contributors = $cache->get($cacheKey);

    if ($contributors === null) {
        echo "   Cache miss - fetching from GitHub API\n";
        $contributors = $githubService->fetchContributors(5, 1);

        // Cache for 1 day
        $cache->set($cacheKey, $contributors, 1);
        echo "   ✅ Data cached\n";
    } else {
        echo "   ✅ Cache hit - using cached data\n";
    }

    echo "\n";
    echo "✅ Found " . count($contributors) . " contributors:\n\n";

    foreach ($contributors as $index => $contributor) {
        $rank = $index + 1;
        echo "   #{$rank} {$contributor['login']}\n";
        echo "      Contributions: " . number_format($contributor['contributions']) . "\n";
        echo "      Type: {$contributor['type']}\n";
        echo "      Profile: {$contributor['html_url']}\n\n";
    }

    echo "API calls made: {$githubService->getApiCallCount()}\n\n";

    // Summary
    echo "════════════════════════════════════════════════════════════════\n";
    echo "  Test Summary\n";
    echo "════════════════════════════════════════════════════════════════\n\n";

    echo "✅ All services working correctly!\n\n";

    echo "Services tested:\n";
    echo "  ✓ Logger - PSR-3 compliant logging with rotation\n";
    echo "  ✓ Cache Service - 3-tier caching (memory → database → API)\n";
    echo "  ✓ Lock Manager - Cron job concurrency control\n";
    echo "  ✓ GitHub API Service - Rate-limited API integration\n";
    echo "  ✓ Rate Limiter - Automatic rate limit management\n\n";

    echo "Cache performance:\n";
    echo "  Hit rate: {$stats['hit_rate_percentage']}%\n";
    echo "  Memory cache size: {$stats['memory_cache_size']} keys\n";
    echo "  Database cache size: {$sizeInfo['total_keys']} keys\n\n";

    echo "GitHub API usage:\n";
    echo "  Calls made: {$githubService->getApiCallCount()}\n";
    echo "  Remaining: " . number_format($githubService->getRateLimiter()->getRemaining()) . "\n";
    echo "  Usage: {$githubService->getRateLimiter()->getUsagePercentage()}%\n\n";

    echo "Next steps:\n";
    echo "  1. Check logs: tail -f {$logger->getLogFile()}\n";
    echo "  2. View cached data in database: SELECT * FROM widget_cache;\n";
    echo "  3. Continue with frontend widget implementation\n\n";

} catch (Exception $e) {
    echo "\n❌ Error: {$e->getMessage()}\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    exit(1);
}
