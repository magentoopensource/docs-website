<?php

declare(strict_types=1);

namespace ContributorsWidget\Services;

use ContributorsWidget\Config\{Configuration, Database};
use ContributorsWidget\Utils\Logger;
use ContributorsWidget\Exceptions\CacheException;
use PDO;

/**
 * Multi-Tier Cache Service
 *
 * Implements 3-tier caching strategy:
 * 1. Memory cache (PHP array) - 1 hour TTL
 * 2. Database cache (MySQL table) - 30 days TTL
 * 3. GitHub API (external) - fallback
 *
 * @package ContributorsWidget\Services
 */
class CacheService
{
    private Configuration $config;
    private Database $db;
    private Logger $logger;
    private array $memoryCache = [];

    // Cache durations
    private const MEMORY_TTL_SECONDS = 3600; // 1 hour
    private int $dbTtlDays;

    // Cache statistics
    private array $stats = [
        'memory_hits' => 0,
        'database_hits' => 0,
        'misses' => 0,
    ];

    /**
     * Constructor
     *
     * @param Configuration $config Application configuration
     * @param Database $db Database connection
     * @param Logger $logger Logger instance
     */
    public function __construct(Configuration $config, Database $db, Logger $logger)
    {
        $this->config = $config;
        $this->db = $db;
        $this->logger = $logger;
        $this->dbTtlDays = $config->get('cache.duration_days', 30);
    }

    /**
     * Get data from cache (tries all tiers)
     *
     * @param string $key Cache key
     * @return mixed|null Returns cached data or null if not found
     */
    public function get(string $key)
    {
        // Tier 1: Check memory cache
        if (isset($this->memoryCache[$key])) {
            $cached = $this->memoryCache[$key];

            if ($cached['expires_at'] > time()) {
                $this->stats['memory_hits']++;
                $this->logger->debug('Cache hit (memory)', ['key' => $key]);
                return $cached['data'];
            }

            // Expired - remove it
            unset($this->memoryCache[$key]);
        }

        // Tier 2: Check database cache
        $dbCached = $this->getFromDatabase($key);

        if ($dbCached !== null) {
            // Promote to memory cache
            $this->storeInMemory($key, $dbCached);
            $this->stats['database_hits']++;
            $this->logger->debug('Cache hit (database)', ['key' => $key]);
            return $dbCached;
        }

        // Cache miss
        $this->stats['misses']++;
        $this->logger->debug('Cache miss', ['key' => $key]);

        return null;
    }

    /**
     * Store data in cache (all tiers)
     *
     * @param string $key Cache key
     * @param mixed $data Data to cache
     * @param int|null $ttlDays Time to live in days (null = use default)
     * @return bool Success status
     */
    public function set(string $key, $data, ?int $ttlDays = null): bool
    {
        $ttlDays = $ttlDays ?? $this->dbTtlDays;

        // Store in memory cache
        $this->storeInMemory($key, $data);

        // Store in database cache
        $result = $this->storeInDatabase($key, $data, $ttlDays);

        if ($result) {
            $this->logger->debug('Cache set', [
                'key' => $key,
                'ttl_days' => $ttlDays
            ]);
        } else {
            $this->logger->warning('Cache set failed', ['key' => $key]);
        }

        return $result;
    }

    /**
     * Delete cache entry
     *
     * @param string $key Cache key
     * @return bool
     */
    public function delete(string $key): bool
    {
        // Remove from memory cache
        unset($this->memoryCache[$key]);

        // Remove from database cache
        try {
            $stmt = $this->db->execute(
                "DELETE FROM widget_cache WHERE cache_key = :key",
                ['key' => $key]
            );

            $deleted = $stmt->rowCount() > 0;

            if ($deleted) {
                $this->logger->debug('Cache deleted', ['key' => $key]);
            }

            return $deleted;
        } catch (\Exception $e) {
            $this->logger->error('Cache delete failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Clear all cache (both tiers)
     *
     * @param string|null $pattern Optional pattern to match keys (SQL LIKE syntax)
     * @return int Number of keys cleared
     */
    public function clear(?string $pattern = null): int
    {
        $count = 0;

        // Clear memory cache
        if ($pattern === null) {
            $count += count($this->memoryCache);
            $this->memoryCache = [];
        } else {
            foreach (array_keys($this->memoryCache) as $key) {
                if ($this->matchesPattern($key, $pattern)) {
                    unset($this->memoryCache[$key]);
                    $count++;
                }
            }
        }

        // Clear database cache
        try {
            if ($pattern === null) {
                $stmt = $this->db->getConnection()->query("DELETE FROM widget_cache");
            } else {
                $stmt = $this->db->execute(
                    "DELETE FROM widget_cache WHERE cache_key LIKE :pattern",
                    ['pattern' => $pattern]
                );
            }

            $count += $stmt->rowCount();

            $this->logger->info('Cache cleared', [
                'pattern' => $pattern ?? '*',
                'keys_cleared' => $count
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Cache clear failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }

        return $count;
    }

    /**
     * Clean up expired cache entries
     *
     * @return int Number of expired entries removed
     */
    public function cleanup(): int
    {
        try {
            $stmt = $this->db->getConnection()->query(
                "DELETE FROM widget_cache WHERE expires_at < NOW()"
            );

            $count = $stmt->rowCount();

            if ($count > 0) {
                $this->logger->info('Cache cleanup completed', [
                    'expired_entries' => $count
                ]);
            }

            return $count;
        } catch (\Exception $e) {
            $this->logger->error('Cache cleanup failed', [
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        $totalRequests = array_sum($this->stats);
        $hitRate = $totalRequests > 0
            ? round((($this->stats['memory_hits'] + $this->stats['database_hits']) / $totalRequests) * 100, 2)
            : 0;

        return array_merge($this->stats, [
            'total_requests' => $totalRequests,
            'hit_rate_percentage' => $hitRate,
            'memory_cache_size' => count($this->memoryCache),
            'memory_usage_bytes' => memory_get_usage(true),
        ]);
    }

    /**
     * Store data in memory cache
     *
     * @param string $key Cache key
     * @param mixed $data Data to store
     * @return void
     */
    private function storeInMemory(string $key, $data): void
    {
        $this->memoryCache[$key] = [
            'data' => $data,
            'expires_at' => time() + self::MEMORY_TTL_SECONDS,
            'created_at' => time(),
        ];
    }

    /**
     * Get data from database cache
     *
     * @param string $key Cache key
     * @return mixed|null
     */
    private function getFromDatabase(string $key)
    {
        try {
            $stmt = $this->db->execute(
                "SELECT cache_value, expires_at
                 FROM widget_cache
                 WHERE cache_key = :key
                 AND expires_at > NOW()",
                ['key' => $key]
            );

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            $data = json_decode($result['cache_value'], true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->warning('Invalid JSON in cache', [
                    'key' => $key,
                    'error' => json_last_error_msg()
                ]);

                return null;
            }

            return $data;
        } catch (\Exception $e) {
            $this->logger->error('Database cache read failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Store data in database cache
     *
     * @param string $key Cache key
     * @param mixed $data Data to store
     * @param int $ttlDays Time to live in days
     * @return bool
     */
    private function storeInDatabase(string $key, $data, int $ttlDays): bool
    {
        try {
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$ttlDays} days"));
            $jsonData = json_encode($data);

            if ($jsonData === false) {
                throw CacheException::writeFailed($key, 'JSON encoding failed');
            }

            $stmt = $this->db->execute(
                "INSERT INTO widget_cache (cache_key, cache_value, expires_at)
                 VALUES (:key, :value, :expires_at)
                 ON DUPLICATE KEY UPDATE
                    cache_value = :value,
                    expires_at = :expires_at",
                [
                    'key' => $key,
                    'value' => $jsonData,
                    'expires_at' => $expiresAt
                ]
            );

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Database cache write failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Check if key matches pattern
     *
     * @param string $key Key to check
     * @param string $pattern Pattern (supports * wildcard)
     * @return bool
     */
    private function matchesPattern(string $key, string $pattern): bool
    {
        $regex = '/^' . str_replace(['*', '?'], ['.*', '.'], preg_quote($pattern, '/')) . '$/';

        return preg_match($regex, $key) === 1;
    }

    /**
     * Get cache size information
     *
     * @return array
     */
    public function getSizeInfo(): array
    {
        try {
            $stmt = $this->db->getConnection()->query(
                "SELECT
                    COUNT(*) as total_keys,
                    SUM(LENGTH(cache_value)) as total_size_bytes,
                    COUNT(CASE WHEN expires_at < NOW() THEN 1 END) as expired_keys
                 FROM widget_cache"
            );

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total_keys' => (int) $result['total_keys'],
                'total_size_bytes' => (int) $result['total_size_bytes'],
                'total_size_mb' => round((int) $result['total_size_bytes'] / 1048576, 2),
                'expired_keys' => (int) $result['expired_keys'],
            ];
        } catch (\Exception $e) {
            return [
                'total_keys' => 0,
                'total_size_bytes' => 0,
                'total_size_mb' => 0,
                'expired_keys' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
