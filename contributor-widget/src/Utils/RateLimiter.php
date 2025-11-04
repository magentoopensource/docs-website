<?php

declare(strict_types=1);

namespace ContributorsWidget\Utils;

use ContributorsWidget\Config\Configuration;
use ContributorsWidget\Exceptions\RateLimitException;

/**
 * GitHub API Rate Limit Manager
 *
 * Manages GitHub API rate limiting with pre-flight checks
 * and automatic wait/retry logic
 *
 * @package ContributorsWidget\Utils
 */
class RateLimiter
{
    private Configuration $config;
    private Logger $logger;
    private int $minRemaining = 100;
    private ?array $currentLimit = null;

    /**
     * Constructor
     *
     * @param Configuration $config Application configuration
     * @param Logger $logger Logger instance
     */
    public function __construct(Configuration $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Check if we have sufficient rate limit remaining
     *
     * @param int $required Minimum required remaining calls
     * @return bool
     */
    public function hasSufficientLimit(int $required = 1): bool
    {
        if ($this->currentLimit === null) {
            return true; // Optimistic, will check on first API call
        }

        return $this->currentLimit['remaining'] >= $required;
    }

    /**
     * Update rate limit from API response headers
     *
     * @param array $headers Response headers
     * @return void
     */
    public function updateFromHeaders(array $headers): void
    {
        // GitHub rate limit headers
        $limit = $headers['X-RateLimit-Limit'][0] ?? null;
        $remaining = $headers['X-RateLimit-Remaining'][0] ?? null;
        $reset = $headers['X-RateLimit-Reset'][0] ?? null;

        if ($limit !== null && $remaining !== null && $reset !== null) {
            $this->currentLimit = [
                'limit' => (int) $limit,
                'remaining' => (int) $remaining,
                'reset' => (int) $reset,
                'reset_time' => date('Y-m-d H:i:s', (int) $reset),
            ];

            $this->logger->debug('Rate limit updated', $this->currentLimit);

            // Warn if getting low
            if ($this->currentLimit['remaining'] < $this->minRemaining) {
                $this->logger->warning('GitHub API rate limit low', [
                    'remaining' => $this->currentLimit['remaining'],
                    'resets_at' => $this->currentLimit['reset_time']
                ]);
            }
        }
    }

    /**
     * Check rate limit before making API call
     *
     * @param int $required Minimum required remaining calls
     * @return void
     * @throws RateLimitException If insufficient rate limit
     */
    public function checkBeforeRequest(int $required = 1): void
    {
        if ($this->currentLimit === null) {
            return; // First call, will check after
        }

        if ($this->currentLimit['remaining'] < $required) {
            throw new RateLimitException(
                "Insufficient rate limit. Required: {$required}, Remaining: {$this->currentLimit['remaining']}",
                $this->currentLimit['remaining'],
                $this->currentLimit['reset']
            );
        }
    }

    /**
     * Wait until rate limit resets
     *
     * @return void
     */
    public function waitUntilReset(): void
    {
        if ($this->currentLimit === null) {
            return;
        }

        $waitTime = $this->currentLimit['reset'] - time();

        if ($waitTime > 0) {
            $this->logger->info('Waiting for rate limit reset', [
                'wait_seconds' => $waitTime,
                'reset_time' => $this->currentLimit['reset_time']
            ]);

            sleep($waitTime + 1); // Add 1 second buffer
        }
    }

    /**
     * Get current rate limit status
     *
     * @return array|null
     */
    public function getCurrentLimit(): ?array
    {
        return $this->currentLimit;
    }

    /**
     * Get remaining API calls
     *
     * @return int
     */
    public function getRemaining(): int
    {
        return $this->currentLimit['remaining'] ?? 5000;
    }

    /**
     * Get rate limit reset timestamp
     *
     * @return int
     */
    public function getResetTimestamp(): int
    {
        return $this->currentLimit['reset'] ?? (time() + 3600);
    }

    /**
     * Calculate percentage of rate limit used
     *
     * @return float
     */
    public function getUsagePercentage(): float
    {
        if ($this->currentLimit === null) {
            return 0.0;
        }

        $used = $this->currentLimit['limit'] - $this->currentLimit['remaining'];

        return round(($used / $this->currentLimit['limit']) * 100, 2);
    }

    /**
     * Check if rate limit is critical (< 100 remaining)
     *
     * @return bool
     */
    public function isCritical(): bool
    {
        return $this->getRemaining() < $this->minRemaining;
    }
}
