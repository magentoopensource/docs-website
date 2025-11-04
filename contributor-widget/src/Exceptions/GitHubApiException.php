<?php

declare(strict_types=1);

namespace ContributorsWidget\Exceptions;

use RuntimeException;

/**
 * GitHub API Exception
 *
 * Thrown when GitHub API requests fail
 *
 * @package ContributorsWidget\Exceptions
 */
class GitHubApiException extends RuntimeException
{
    /**
     * Create exception from API response
     *
     * @param int $statusCode HTTP status code
     * @param string $message Error message
     * @param array $context Additional context
     * @return self
     */
    public static function fromResponse(int $statusCode, string $message, array $context = []): self
    {
        $fullMessage = sprintf(
            'GitHub API error [%d]: %s',
            $statusCode,
            $message
        );

        if (!empty($context)) {
            $fullMessage .= ' | Context: ' . json_encode($context);
        }

        return new self($fullMessage, $statusCode);
    }

    /**
     * Create exception for rate limit exceeded
     *
     * @param int $resetTimestamp When rate limit resets
     * @return self
     */
    public static function rateLimitExceeded(int $resetTimestamp): self
    {
        $resetTime = date('Y-m-d H:i:s', $resetTimestamp);

        return new self(
            "GitHub API rate limit exceeded. Resets at: {$resetTime} UTC",
            429
        );
    }

    /**
     * Create exception for invalid token
     *
     * @return self
     */
    public static function invalidToken(): self
    {
        return new self('Invalid or expired GitHub API token', 401);
    }

    /**
     * Create exception for repository not found
     *
     * @param string $owner Repository owner
     * @param string $repo Repository name
     * @return self
     */
    public static function repositoryNotFound(string $owner, string $repo): self
    {
        return new self(
            "Repository not found or no access: {$owner}/{$repo}",
            404
        );
    }
}
