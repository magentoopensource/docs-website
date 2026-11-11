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
}
