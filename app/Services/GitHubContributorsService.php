<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GitHub Contributors Service
 *
 * Fetches and caches GitHub contributors for the documentation contributor widget.
 *
 * By default it aggregates authorship across multiple repositories (the merchant
 * content repo and the docs-website site/theme repo that builds and presents the
 * docs), merged per GitHub login — so people who build and style the docs are
 * credited alongside those who write the content. The source list is configured in
 * config/services.php ('github.contributor_sources'), mirroring the developer-docs
 * generator's write_contributors_json(). When that config is empty it falls back to
 * the single owner/repo contributor list.
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
     * Get the top contributors for the widget, aggregated across configured sources.
     *
     * @param int $limit Number of contributors to return
     * @return array<int, array{login: string, avatar_url: string, html_url: string, contributions: int}>
     */
    public function getTopContributors(int $limit = 3): array
    {
        $sources = config('services.github.contributor_sources', []);

        $ttl = now()->addDays($this->cacheDays);

        return Cache::remember($this->cacheKey($limit), $ttl, function () use ($sources, $limit) {
            if (empty($sources)) {
                return $this->fetchContributorsFromApi($limit);
            }

            return $this->fetchAggregatedContributors($sources, $limit);
        });
    }

    /**
     * Aggregate contributors across multiple repositories, merged by login.
     *
     * @param array<int, array{type?: string, repo?: string, path?: string}> $sources
     * @return array<int, array{login: string, avatar_url: string, html_url: string, contributions: int}>
     */
    private function fetchAggregatedContributors(array $sources, int $limit): array
    {
        $aggregated = [];

        $add = function (?string $login, ?string $avatar, ?string $html, int $count) use (&$aggregated): void {
            if ($login === null || $login === '') {
                return;
            }

            if (! isset($aggregated[$login])) {
                $aggregated[$login] = [
                    'login' => $login,
                    'avatar_url' => $avatar ?? '',
                    'html_url' => $html ?? '#',
                    'contributions' => 0,
                ];
            }

            $aggregated[$login]['contributions'] += $count;

            if ($avatar && $aggregated[$login]['avatar_url'] === '') {
                $aggregated[$login]['avatar_url'] = $avatar;
            }

            if ($html && $aggregated[$login]['html_url'] === '#') {
                $aggregated[$login]['html_url'] = $html;
            }
        };

        try {
            foreach ($sources as $source) {
                $repo = $source['repo'] ?? '';

                if ($repo === '') {
                    continue;
                }

                if (($source['type'] ?? 'contributors') === 'commits') {
                    $query = ['per_page' => 100];

                    if (! empty($source['path'])) {
                        $query['path'] = $source['path'];
                    }

                    $commits = $this->apiGet("https://api.github.com/repos/{$repo}/commits", $query);

                    foreach ($commits as $commit) {
                        $author = $commit['author'] ?? null;

                        if (is_array($author)) {
                            $add(
                                $author['login'] ?? null,
                                $author['avatar_url'] ?? null,
                                $author['html_url'] ?? null,
                                1
                            );
                        }
                    }

                    continue;
                }

                $contributors = $this->apiGet(
                    "https://api.github.com/repos/{$repo}/contributors",
                    ['per_page' => 100]
                );

                foreach ($contributors as $contributor) {
                    if (($contributor['type'] ?? 'User') !== 'User') {
                        continue;
                    }

                    $add(
                        $contributor['login'] ?? null,
                        $contributor['avatar_url'] ?? null,
                        $contributor['html_url'] ?? null,
                        (int) ($contributor['contributions'] ?? 0)
                    );
                }
            }
        } catch (\Exception $e) {
            Log::warning('GitHub contributor aggregation failed', ['error' => $e->getMessage()]);

            return [];
        }

        $contributors = array_values($aggregated);

        usort($contributors, fn (array $a, array $b): int => $b['contributions'] <=> $a['contributions']);

        return array_slice($contributors, 0, $limit);
    }

    /**
     * Perform a GitHub API GET request, returning the decoded JSON array.
     *
     * @param array<string, mixed> $query
     * @return array<int, mixed>
     *
     * @throws \RuntimeException on a non-successful response
     */
    private function apiGet(string $url, array $query = []): array
    {
        $headers = [
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => 'MagentoOpenSource-Docs-Website',
        ];

        if ($this->token) {
            $headers['Authorization'] = "Bearer {$this->token}";
        }

        $response = Http::withHeaders($headers)->timeout(10)->get($url, $query);

        if (! $response->successful()) {
            throw new \RuntimeException("GitHub API request to {$url} failed with status {$response->status()}");
        }

        return $response->json() ?? [];
    }

    /**
     * Fetch contributors for the single configured owner/repo. Legacy fallback
     * used only when no aggregation sources are configured.
     *
     * @return array<int, array{login: string, avatar_url: string, html_url: string, contributions: int, type: string}>
     */
    private function fetchContributorsFromApi(int $limit): array
    {
        try {
            $contributors = $this->apiGet(
                "https://api.github.com/repos/{$this->owner}/{$this->repo}/contributors",
                ['per_page' => $limit, 'page' => 1]
            );

            return array_map(function ($contributor) {
                return [
                    'login' => $contributor['login'] ?? 'Unknown',
                    'avatar_url' => $contributor['avatar_url'] ?? '',
                    'html_url' => $contributor['html_url'] ?? '#',
                    'contributions' => $contributor['contributions'] ?? 0,
                    'type' => $contributor['type'] ?? 'User',
                ];
            }, $contributors);
        } catch (\Exception $e) {
            Log::error('Failed to fetch GitHub contributors', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Build the cache key for the current source configuration and limit.
     */
    private function cacheKey(int $limit): string
    {
        $sources = config('services.github.contributor_sources', []);

        if (empty($sources)) {
            return "github_contributors_{$this->owner}_{$this->repo}_{$limit}";
        }

        return 'github_contributors_agg_' . md5((string) json_encode($sources)) . "_{$limit}";
    }

    /**
     * Clear the contributors cache
     */
    public function clearCache(int $limit = 3): void
    {
        Cache::forget($this->cacheKey($limit));
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
