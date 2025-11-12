<?php

declare(strict_types=1);

namespace ContributorsWidget\Exceptions;

use RuntimeException;

/**
 * Rate Limit Exception
 *
 * Thrown when rate limit is exceeded or insufficient
 *
 * @package ContributorsWidget\Exceptions
 */
class RateLimitException extends RuntimeException
{
    private int $remaining;
    private int $resetTimestamp;

    /**
     * Create rate limit exception
     *
     * @param string $message Error message
     * @param int $remaining Remaining API calls
     * @param int $resetTimestamp When limit resets
     */
    public function __construct(string $message, int $remaining = 0, int $resetTimestamp = 0)
    {
        parent::__construct($message, 429);

        $this->remaining = $remaining;
        $this->resetTimestamp = $resetTimestamp;
    }

    /**
     * Get remaining API calls
     *
     * @return int
     */
    public function getRemaining(): int
    {
        return $this->remaining;
    }

    /**
     * Get reset timestamp
     *
     * @return int
     */
    public function getResetTimestamp(): int
    {
        return $this->resetTimestamp;
    }

    /**
     * Get reset time as formatted string
     *
     * @return string
     */
    public function getResetTime(): string
    {
        return date('Y-m-d H:i:s', $this->resetTimestamp) . ' UTC';
    }

    /**
     * Get seconds until reset
     *
     * @return int
     */
    public function getSecondsUntilReset(): int
    {
        return max(0, $this->resetTimestamp - time());
    }
}
