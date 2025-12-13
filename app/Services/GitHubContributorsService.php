<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GitHub Contributors Service
 *
 * Fetches and caches GitHub repository contributors for display
 * Uses Laravel's cache and HTTP client for simplified integration
 */
class GitHubContributorsService
{
    private string $owner;
    private string $repo;
    private ?string $token;
    private int $cacheDays;

    public function __construct(
        ?string $owner = null,
        ?string $repo = null,
        ?string $token = null,
        int $cacheDays = 7
    ) {
        $this->owner = $owner ?? config('services.github.owner', 'magentoopensource');
        $this->repo = $repo ?? config('services.github.repo', 'docs');
        $this->token = $token ?? config('services.github.token');
        $this->cacheDays = $cacheDays;
    }

    /**
     * Get top contributors for the repository
     *
     * @param int $limit Number of contributors to return
     * @return array<int, array{login: string, avatar_url: string, html_url: string, contributions: int}>
     */
    public function getTopContributors(int $limit = 3): array
    {
        $cacheKey = "github_contributors_{$this->owner}_{$this->repo}_{$limit}";

        return Cache::remember($cacheKey, now()->addDays($this->cacheDays), function () use ($limit) {
            return $this->fetchContributorsFromApi($limit);
        });
    }

    /**
     * Fetch contributors from GitHub API
     */
    private function fetchContributorsFromApi(int $limit): array
    {
        try {
            $headers = [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'MagentoOpenSource-Docs-Website',
            ];

            if ($this->token) {
                $headers['Authorization'] = "Bearer {$this->token}";
            }

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get("https://api.github.com/repos/{$this->owner}/{$this->repo}/contributors", [
                    'per_page' => $limit,
                    'page' => 1,
                ]);

            if ($response->successful()) {
                $contributors = $response->json();

                return array_map(function ($contributor) {
                    return [
                        'login' => $contributor['login'] ?? 'Unknown',
                        'avatar_url' => $contributor['avatar_url'] ?? '',
                        'html_url' => $contributor['html_url'] ?? '#',
                        'contributions' => $contributor['contributions'] ?? 0,
                        'type' => $contributor['type'] ?? 'User',
                    ];
                }, $contributors);
            }

            Log::warning('GitHub API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch GitHub contributors', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Clear the contributors cache
     */
    public function clearCache(int $limit = 3): void
    {
        $cacheKey = "github_contributors_{$this->owner}_{$this->repo}_{$limit}";
        Cache::forget($cacheKey);
    }

    /**
     * Get the repository URL
     */
    public function getRepositoryUrl(): string
    {
        return "https://github.com/{$this->owner}/{$this->repo}";
    }

    /**
     * Get the contributors page URL
     */
    public function getContributorsUrl(): string
    {
        return "https://github.com/{$this->owner}/{$this->repo}/graphs/contributors";
    }
}
