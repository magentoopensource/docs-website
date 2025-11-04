<?php

declare(strict_types=1);

namespace ContributorsWidget\Exceptions;

use RuntimeException;

/**
 * Cache Exception
 *
 * Thrown when cache operations fail
 *
 * @package ContributorsWidget\Exceptions
 */
class CacheException extends RuntimeException
{
    /**
     * Create exception for cache write failure
     *
     * @param string $key Cache key
     * @param string $reason Failure reason
     * @return self
     */
    public static function writeFailed(string $key, string $reason = ''): self
    {
        $message = "Failed to write cache key: {$key}";

        if ($reason) {
            $message .= " | Reason: {$reason}";
        }

        return new self($message);
    }

    /**
     * Create exception for cache read failure
     *
     * @param string $key Cache key
     * @param string $reason Failure reason
     * @return self
     */
    public static function readFailed(string $key, string $reason = ''): self
    {
        $message = "Failed to read cache key: {$key}";

        if ($reason) {
            $message .= " | Reason: {$reason}";
        }

        return new self($message);
    }
}
