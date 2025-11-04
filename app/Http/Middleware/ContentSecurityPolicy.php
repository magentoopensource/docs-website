<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request and add Content Security Policy headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add CSP to HTML responses
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }

        $csp = $this->buildCspPolicy();

        // Add CSP header
        $response->headers->set('Content-Security-Policy', $csp);

        // Add additional security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }

    /**
     * Build the Content Security Policy string.
     */
    protected function buildCspPolicy(): string
    {
        $algoliaId = config('algolia.connections.main.id', '');

        // Allow Vite dev server in local development
        $viteDevServer = app()->environment('local') ? ' http://127.0.0.1:5173 http://127.0.0.1:5174 http://127.0.0.1:5175 ws://127.0.0.1:5173 ws://127.0.0.1:5174 ws://127.0.0.1:5175' : '';

        $policy = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'{$viteDevServer} https://www.googletagmanager.com https://www.google-analytics.com https://{$algoliaId}-dsn.algolia.net",
            "style-src 'self' 'unsafe-inline'{$viteDevServer} https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'{$viteDevServer} https://www.google-analytics.com https://{$algoliaId}-dsn.algolia.net https://*.algolia.net https://*.algolianet.com",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        return implode('; ', $policy);
    }

    /**
     * Check if the response is HTML.
     */
    protected function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }
}
