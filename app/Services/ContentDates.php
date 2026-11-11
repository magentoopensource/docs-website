<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * "Last updated" dates for merchant guides: the git commit date of each source Markdown file in the
 * content clone (resources/docs/main), read once and cached, never per request.
 *
 * The map is path => ISO 8601 commit date, where path is the guide path without extension
 * (for example "getting-started/how-to-configure-ssl-and-security"). It is rebuilt after every
 * docs sync (WebhookController::syncDocs) and lazily on a cache miss, which is also what happens after
 * a deploy clears the cache. When git or the clone is unavailable the map is empty, nothing throws, and
 * the pages simply omit the date.
 *
 * History caveat: the content repository's history was rewritten on 2025-10-21, so older edits show that date.
 */
class ContentDates
{
    private const CACHE_KEY = 'docs.content_dates';

    /** An empty result is cached briefly so a missing git does not shell out on every request. */
    private const EMPTY_TTL = 300;

    private const MAP_TTL = 86400;

    public function __construct(protected Cache $cache)
    {
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        $map = $this->cache->get(self::CACHE_KEY);

        return is_array($map) ? $map : $this->refresh();
    }

    /**
     * ISO 8601 commit date for a guide path, or null when unknown.
     */
    public function forPath(string $path): ?string
    {
        $map = $this->all();
        $path = trim($path, '/');

        // A renamed guide keeps its date from the previous file name until the content repository carries the rename.
        foreach (array_merge([$path], Redirects::previousGuidePaths($path)) as $candidate) {
            if (isset($map[$candidate])) {
                return $map[$candidate];
            }
        }

        return null;
    }

    /**
     * Newest date among guides under a prefix such as "grow-store/" (a hub), or among all guides when the
     * prefix is empty (home pages).
     */
    public function newest(string $prefix = ''): ?string
    {
        $dates = [];
        foreach ($this->all() as $path => $date) {
            if ($prefix === '' || str_starts_with($path, $prefix)) {
                $dates[] = $date;
            }
        }

        // ISO dates from one clone share an offset in practice, but compare as instants to be safe.
        usort($dates, fn ($a, $b) => strtotime($b) <=> strtotime($a));

        return $dates[0] ?? null;
    }

    /**
     * Put "Last updated <date>" straight after the first </h1>. HTML is returned unchanged when there is no H1
     * or the date cannot be read.
     */
    public static function insertAfterHeading(string $html, string $iso): string
    {
        $day = substr($iso, 0, 10);
        $time = \DateTimeImmutable::createFromFormat('!Y-m-d', $day);
        if (!$time || $time->format('Y-m-d') !== $day) {
            return $html;
        }

        $line = '<p class="docs-last-updated">Last updated <time datetime="' . $day . '">'
            . $time->format('j F Y') . '</time></p>';

        $result = preg_replace('#</h1>#i', '</h1>' . "\n" . $line, $html, 1, $count);

        return $count > 0 && $result !== null ? $result : $html;
    }

    /**
     * Rebuild the map from git and store it.
     *
     * @return array<string, string>
     */
    public function refresh(): array
    {
        $map = $this->readFromGit(resource_path('docs/main'));
        $this->cache->put(self::CACHE_KEY, $map, $map === [] ? self::EMPTY_TTL : self::MAP_TTL);

        return $map;
    }

    /**
     * One git invocation for the whole clone: every commit with the files it touched, newest first, so the
     * first time a file is seen is its last-changed date.
     *
     * @return array<string, string>
     */
    protected function readFromGit(string $dir): array
    {
        if (!is_dir($dir . '/.git') && !is_file($dir . '/.git')) {
            return [];
        }

        try {
            $process = new Process(
                ['git', 'log', '--no-renames', '--name-only', '--format=@@%cI', '--', '*.md'],
                $dir
            );
            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('ContentDates: git log failed', ['stderr' => $process->getErrorOutput()]);

                return [];
            }
        } catch (\Throwable $e) {
            Log::warning('ContentDates: git unavailable', ['error' => $e->getMessage()]);

            return [];
        }

        $map = [];
        $date = null;
        foreach (preg_split('/\R/', $process->getOutput()) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_starts_with($line, '@@')) {
                $date = substr($line, 2);
                continue;
            }
            if ($date !== null && str_ends_with($line, '.md')) {
                $key = substr($line, 0, -3);
                if (!isset($map[$key])) {
                    $map[$key] = $date;
                }
            }
        }

        return $map;
    }
}
