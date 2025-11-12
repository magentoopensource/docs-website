<?php

declare(strict_types=1);

namespace ContributorsWidget\Services;

use ContributorsWidget\Config\Configuration;
use ContributorsWidget\Utils\{Logger, RateLimiter};
use ContributorsWidget\Exceptions\{GitHubApiException, RateLimitException};
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * GitHub API Service
 *
 * Handles all interactions with GitHub REST API v3
 * Includes rate limiting, retry logic, and error handling
 *
 * @package ContributorsWidget\Services
 */
class GitHubApiService
{
    private Configuration $config;
    private Logger $logger;
    private RateLimiter $rateLimiter;
    private Client $client;
    private int $apiCallCount = 0;

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
        $this->rateLimiter = new RateLimiter($config, $logger);

        $this->initializeClient();
    }

    /**
     * Initialize Guzzle HTTP client
     *
     * @return void
     */
    private function initializeClient(): void
    {
        $repo = $this->config->getGithubRepo();

        $this->client = new Client([
            'base_uri' => $this->config->get('github.api_url'),
            'timeout' => $this->config->get('github.timeout', 30),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getGithubToken(),
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => $this->config->get('github.api_version'),
                'User-Agent' => 'GitHub-Contributors-Widget/1.0'
            ]
        ]);

        $this->logger->debug('GitHub API client initialized', [
            'base_uri' => $this->config->get('github.api_url'),
            'repo' => "{$repo['owner']}/{$repo['repo']}"
        ]);
    }

    /**
     * Fetch contributors from GitHub API
     *
     * @param int $perPage Number of contributors per page (max 100)
     * @param int $page Page number
     * @return array
     * @throws GitHubApiException
     * @throws RateLimitException
     */
    public function fetchContributors(int $perPage = 100, int $page = 1): array
    {
        $repo = $this->config->getGithubRepo();
        $endpoint = "/repos/{$repo['owner']}/{$repo['repo']}/contributors";

        $params = [
            'per_page' => min($perPage, 100), // GitHub max is 100
            'page' => $page
        ];

        $data = $this->makeRequest($endpoint, $params);

        $this->logger->info('Fetched contributors from GitHub', [
            'count' => count($data),
            'page' => $page,
            'per_page' => $perPage
        ]);

        return $data;
    }

    /**
     * Fetch contributor statistics from GitHub API
     *
     * Includes weekly breakdown of contributions
     *
     * @return array
     * @throws GitHubApiException
     * @throws RateLimitException
     */
    public function fetchContributorStats(): array
    {
        $repo = $this->config->getGithubRepo();
        $endpoint = "/repos/{$repo['owner']}/{$repo['repo']}/stats/contributors";

        // Stats endpoint may return 202 while GitHub computes stats
        $data = $this->makeRequestWithRetry($endpoint, [], 3, 2);

        $this->logger->info('Fetched contributor stats from GitHub', [
            'contributors_count' => count($data)
        ]);

        return $data;
    }

    /**
     * Check rate limit status
     *
     * @return array
     * @throws GitHubApiException
     */
    public function checkRateLimit(): array
    {
        $data = $this->makeRequest('/rate_limit');

        return $data['resources']['core'] ?? [
            'limit' => 5000,
            'remaining' => 5000,
            'reset' => time() + 3600
        ];
    }

    /**
     * Make HTTP request to GitHub API
     *
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @return array
     * @throws GitHubApiException
     * @throws RateLimitException
     */
    private function makeRequest(string $endpoint, array $params = []): array
    {
        // Check rate limit before request
        $this->rateLimiter->checkBeforeRequest();

        try {
            $this->logger->debug('Making GitHub API request', [
                'endpoint' => $endpoint,
                'params' => $params
            ]);

            $response = $this->client->get($endpoint, [
                'query' => $params
            ]);

            $this->apiCallCount++;

            // Update rate limit from response headers
            $this->rateLimiter->updateFromHeaders($response->getHeaders());

            // Parse response
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if ($data === null) {
                throw GitHubApiException::fromResponse(
                    $response->getStatusCode(),
                    'Invalid JSON response',
                    ['body' => substr($body, 0, 200)]
                );
            }

            return $data;

        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();

            $this->logger->error('GitHub API request failed', [
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'message' => $e->getMessage()
            ]);

            // Handle specific error codes
            if ($statusCode === 401) {
                throw GitHubApiException::invalidToken();
            }

            if ($statusCode === 403 || $statusCode === 429) {
                $resetTime = $this->rateLimiter->getResetTimestamp();
                throw new RateLimitException(
                    'GitHub API rate limit exceeded',
                    0,
                    $resetTime
                );
            }

            if ($statusCode === 404) {
                $repo = $this->config->getGithubRepo();
                throw GitHubApiException::repositoryNotFound($repo['owner'], $repo['repo']);
            }

            throw GitHubApiException::fromResponse(
                $statusCode,
                $e->getMessage()
            );
        }
    }

    /**
     * Make request with retry logic for 202 responses
     *
     * GitHub stats endpoints return 202 while computing data
     *
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @param int $maxRetries Maximum retry attempts
     * @param int $retryDelay Seconds to wait between retries
     * @return array
     * @throws GitHubApiException
     * @throws RateLimitException
     */
    private function makeRequestWithRetry(
        string $endpoint,
        array $params = [],
        int $maxRetries = 3,
        int $retryDelay = 2
    ): array {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = $this->client->get($endpoint, ['query' => $params]);

                $this->apiCallCount++;

                // Update rate limit
                $this->rateLimiter->updateFromHeaders($response->getHeaders());

                // Handle 202 Accepted (GitHub is computing stats)
                if ($response->getStatusCode() === 202) {
                    $attempt++;

                    if ($attempt < $maxRetries) {
                        $this->logger->info('GitHub computing stats, retrying', [
                            'endpoint' => $endpoint,
                            'attempt' => $attempt,
                            'retry_in' => $retryDelay
                        ]);

                        sleep($retryDelay);
                        continue;
                    }

                    throw GitHubApiException::fromResponse(
                        202,
                        'GitHub stats still computing after retries',
                        ['attempts' => $attempt]
                    );
                }

                // Success - parse response
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if ($data === null) {
                    throw GitHubApiException::fromResponse(
                        $response->getStatusCode(),
                        'Invalid JSON response'
                    );
                }

                return $data;

            } catch (GuzzleException $e) {
                if ($attempt >= $maxRetries - 1) {
                    throw GitHubApiException::fromResponse(
                        $e->getCode(),
                        $e->getMessage()
                    );
                }

                $attempt++;
                sleep($retryDelay);
            }
        }

        throw GitHubApiException::fromResponse(500, 'Max retries exceeded');
    }

    /**
     * Get total API calls made
     *
     * @return int
     */
    public function getApiCallCount(): int
    {
        return $this->apiCallCount;
    }

    /**
     * Get rate limiter instance
     *
     * @return RateLimiter
     */
    public function getRateLimiter(): RateLimiter
    {
        return $this->rateLimiter;
    }
}
