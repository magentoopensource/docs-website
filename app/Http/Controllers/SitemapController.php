<?php

namespace App\Http\Controllers;

use App\Services\ContentDates;
use App\Services\NavigationParser;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Developer pages that exist on disk but must not be listed: unlinked or scratch pages. Pages that
     * declare noindex, or a canonical pointing at another URL, are left out automatically (see staticPages()).
     */
    private const DEVELOPER_EXCLUDE = ['docs-landing.html', 'hexagon.html', 'hero-options.html'];

    /** How long the scan of the static pages is kept; they only change on deploy or docs sync. */
    private const STATIC_TTL = 600;

    public function __construct(
        protected NavigationParser $navigation,
        protected ContentDates $dates,
        protected Cache $cache
    ) {
    }

    /**
     * /sitemap.xml, built from the site's own data on each request: the merchant navigation and the
     * developer and the-core HTML files. robots.txt has always pointed here, but production never had a
     * file, so the search crawler never found the merchant docs.
     */
    public function index(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        // url() trims trailing slashes ("/developer/" would become a 301 to itself), so join host and path here.
        $root = rtrim(url('/'), '/');
        foreach ($this->entries() as $path => $lastmod) {
            $xml .= '  <url><loc>' . htmlspecialchars($root . $path, ENT_XML1) . '</loc>'
                . ($lastmod ? '<lastmod>' . htmlspecialchars($lastmod, ENT_XML1) . '</lastmod>' : '')
                . '</url>' . "\n";
        }
        $xml .= '</urlset>' . "\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * /llms.txt: a plain-text map of the three sections and their key pages for AI assistants. Built from the
     * merchant navigation and the sitemap, so it follows the site instead of going stale.
     */
    public function llms(): Response
    {
        $root = rtrim(url('/'), '/');
        $entries = $this->entries();
        $areas = count($this->navigation->getCategories());
        $lines = [
            '# Magento Open Source Documentation',
            '',
            '> Community documentation for Magento Open Source, maintained by the Magento Association community: merchant guides for store owners, developer documentation for engineers, and The Core, a module-by-module reference.',
            '',
            '## Merchant documentation',
            '',
            '- [Merchant documentation home](' . $root . '/merchant): the nine areas of running a store, from setup to compliance',
        ];

        foreach ($this->navigation->getCategories() as $category) {
            $lines[] = '- [' . $category['name'] . '](' . $root . '/merchant/' . $category['slug'] . '): '
                . count($category['articles']) . ' guides';
        }

        $lines[] = '';
        $lines[] = '### Merchant guides';
        $lines[] = '';
        foreach ($this->navigation->getCategories() as $category) {
            foreach ($category['articles'] as $article) {
                $lines[] = '- [' . $article['title'] . '](' . $root . '/merchant/' . $article['path'] . ')';
            }
        }

        $lines[] = '';
        $lines[] = '## Developer documentation';
        $lines[] = '';
        $lines[] = '- [Developer documentation home](' . $root . '/developer/): guides, tutorials and module references for developers';
        foreach (array_keys($entries) as $path) {
            if (preg_match('#^/developer/(guide-[a-z0-9-]+|module-[a-z]+|certifications|learning-paths|how-to-guides)\.html$#', $path)) {
                $lines[] = '- [' . $this->labelFor($path) . '](' . $root . $path . ')';
            }
        }

        $lines[] = '';
        $lines[] = '## The Core';
        $lines[] = '';
        $lines[] = '- [The Core home](' . $root . '/the-core/): module-by-module reference for the Magento core';
        foreach (array_keys($entries) as $path) {
            if (preg_match('#^/the-core/modules/[^/]+/html/$#', $path)) {
                $lines[] = '- [' . $this->labelFor($path) . '](' . $root . $path . ')';
            }
        }

        $lines[] = '';
        $lines[] = '## More';
        $lines[] = '';
        $lines[] = '- [Contributors](' . $root . '/contributors): who writes and builds these docs';
        $lines[] = '- [Sitemap](' . $root . '/sitemap.xml)';
        $lines[] = '';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Human label from a URL path: the last segment, de-slugged.
     */
    private function labelFor(string $path): string
    {
        $segments = array_values(array_filter(explode('/', $path)));
        $last = (string) end($segments);
        if ($last === 'html' && count($segments) > 1) {
            $last = $segments[count($segments) - 2];
        }
        $last = preg_replace('/\.html$/', '', $last);
        $last = preg_replace('/^(guide|module)-/', '', (string) $last);
        $last = str_replace('Magento_', '', (string) $last);

        return ucwords(str_replace(['-', '_'], ' ', $last));
    }

    /**
     * Every URL to list, in order, with its last-modified date when one is known.
     *
     * @return array<string, string|null> path => ISO 8601 date or null
     */
    private function entries(): array
    {
        $newest = $this->dates->newest();
        $entries = ['/' => $newest, '/merchant' => $newest, '/contributors' => $newest];

        foreach ($this->navigation->getCategories() as $category) {
            $entries['/merchant/' . $category['slug']] = $this->dates->newest($category['slug'] . '/');
            foreach ($category['articles'] as $article) {
                $entries['/merchant/' . $article['path']] = $this->dates->forPath($article['path']);
            }
        }

        foreach ($this->staticPages() as $path => $lastmod) {
            $entries[$path] = $lastmod;
        }

        ksort($entries);

        return $entries;
    }

    /**
     * The generated and hand-written pages under public/developer and public/the-core.
     *
     * A page is listed only when it would be indexed as itself: no noindex, and no rel=canonical pointing at a
     * different URL (so pages that defer to another page drop out without a hand-kept list). The date is the
     * dateModified in the page's own JSON-LD, which the static page generator takes from git.
     *
     * @return array<string, string|null>
     */
    private function staticPages(): array
    {
        return $this->cache->remember('sitemap.static', self::STATIC_TTL, function () {
            $pages = [];

            foreach (glob(public_path('developer/*.html')) ?: [] as $file) {
                $name = basename($file);
                if (in_array($name, self::DEVELOPER_EXCLUDE, true)) {
                    continue;
                }
                $path = $name === 'index.html' ? '/developer/' : '/developer/' . $name;
                $this->addStaticPage($pages, $path, $file);
            }

            $core = public_path('the-core');
            if (is_dir($core)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($core, \FilesystemIterator::SKIP_DOTS)
                );
                foreach ($files as $file) {
                    if ($file->getExtension() === 'html') {
                        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($core)));
                        $this->addStaticPage(
                            $pages,
                            '/the-core' . preg_replace('#/index\.html$#', '/', $relative),
                            $file->getPathname()
                        );
                    }
                }
            }

            return $pages;
        });
    }

    /**
     * @param array<string, string|null> $pages
     */
    private function addStaticPage(array &$pages, string $path, string $file): void
    {
        $html = @file_get_contents($file);
        if ($html === false) {
            return;
        }

        if (preg_match('#<meta[^>]+name=["\']robots["\'][^>]*content=["\'][^"\']*noindex#i', $html)) {
            return;
        }

        $hasCanonical = preg_match('#<link[^>]+rel=["\']canonical["\'][^>]*>#i', $html, $tag)
            && preg_match('#href=["\']([^"\']+)["\']#i', $tag[0], $href);
        if ($hasCanonical) {
            $target = parse_url($href[1], PHP_URL_PATH) ?: '/';
            $target = preg_replace('#/index\.html$#', '/', $target);
            if ($target !== $path) {
                return;
            }
        }

        $lastmod = null;
        if (preg_match('#"dateModified"\s*:\s*"(\d{4}-\d{2}-\d{2}[^"]*)"#', $html, $m)) {
            $lastmod = $m[1];
        }

        $pages[$path] = $lastmod;
    }
}
