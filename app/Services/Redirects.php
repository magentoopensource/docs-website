<?php

namespace App\Services;

/**
 * Data-driven permanent redirects (config/redirects.php).
 */
class Redirects
{
    /**
     * Destination for a request path, or null when the path is not redirected. Never returns the path itself.
     */
    public static function resolve(string $path): ?string
    {
        $path = '/' . ltrim(rawurldecode($path), '/');

        $to = config('redirects.exact', [])[$path] ?? null;

        if ($to === null) {
            foreach (config('redirects.patterns', []) as $pattern => $replacement) {
                $result = preg_replace($pattern, $replacement, $path, 1, $count);
                if ($count > 0 && $result !== null) {
                    $to = $result;
                    break;
                }
            }
        }

        return ($to === null || $to === $path) ? null : $to;
    }

    /**
     * The current path for a merchant guide path ("category/slug", no /merchant/ prefix): the redirect target when
     * the guide was renamed, otherwise the path itself. Lets old content (navigation.md, front matter) written
     * under the previous file names keep working while the content repository catches up with a rename.
     */
    public static function currentGuidePath(string $guidePath): string
    {
        $to = config('redirects.exact', [])['/merchant/' . ltrim($guidePath, '/')] ?? null;

        return is_string($to) && str_starts_with($to, '/merchant/') ? substr($to, strlen('/merchant/')) : $guidePath;
    }

    /**
     * Previous guide paths ("category/slug") that now redirect to the given current guide path.
     *
     * @return list<string>
     */
    public static function previousGuidePaths(string $guidePath): array
    {
        $target = '/merchant/' . ltrim($guidePath, '/');
        $old = [];
        foreach (config('redirects.exact', []) as $from => $to) {
            if ($to === $target && str_starts_with($from, '/merchant/')) {
                $old[] = substr($from, strlen('/merchant/'));
            }
        }

        return $old;
    }
}
