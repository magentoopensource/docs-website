<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use App\Markdown\GithubFlavoredMarkdownConverter;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Cache\Repository as Cache;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class Documentation
{
    /**
     * Create a new documentation instance.
     */
    public function __construct(
        protected Filesystem $files,
        protected Cache $cache
    ) {
    }

    /**
     * Get the documentation index page (navigation).
     *
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->cache->remember(
            "docs.main.navigation",
            CarbonInterval::minutes(5),
            function () {
                $path = base_path("resources/docs/main/navigation.md");

                if ($this->files->exists($path)) {
                    return $this->replaceLinks(
                        (new GithubFlavoredMarkdownConverter())->convert(
                            $this->files->get($path)
                        )
                    );
                }

                return null;
            }
        );
    }

    /**
     * Get the given documentation page.
     *
     * @param  string  $page
     * @return array|null
     */
    public function get(string $page): ?array
    {
        // Sanitize page path to prevent path traversal attacks
        $page = $this->sanitizePath($page);

        if ($page === null) {
            return null;
        }

        return $this->cache->remember(
            "docs.main." . $page,
            CarbonInterval::minutes(5),
            function () use ($page) {
                $path = base_path("resources/docs/main/" . $page . ".md");

                if ($this->files->exists($path)) {
                    $content = $this->files->get($path);

                    $content = (new GithubFlavoredMarkdownConverter())->convert(
                        $content
                    );
                    $frontMatter = [];
                    if ($content instanceof RenderedContentWithFrontMatter) {
                        $frontMatter = $content->getFrontMatter();
                    }

                    return [
                        "content" => $this->replaceLinks($content),
                        "frontMatter" => $frontMatter,
                    ];
                }

                return null;
            }
        );
    }

    /**
     * Get the array based index representation of the documentation.
     *
     * @return array
     */
    public function indexArray(): array
    {
        return $this->cache->remember(
            "docs.main.index.array",
            CarbonInterval::hour(1),
            function () {
                $path = base_path("resources/docs/main/navigation.md");

                if (!$this->files->exists($path)) {
                    return [];
                }

                return [
                    "pages" => collect(
                        explode(
                            PHP_EOL,
                            $this->replaceLinks($this->files->get($path))
                        )
                    )
                        ->filter(
                            fn($line) => Str::contains($line, "/docs/")
                        )
                        ->map(
                            fn($line) => resource_path(
                                Str::of($line)
                                    ->afterLast("(/docs/")
                                    ->before(")")
                                    ->prepend("docs/main/")
                                    ->append(".md")
                            )
                        )
                        ->filter(fn($path) => $this->files->exists($path))
                        ->mapWithKeys(function ($path) {
                            $contents = $this->files->get($path);

                            preg_match(
                                '/\# (?<title>[^\\n]+)/',
                                $contents,
                                $page
                            );
                            preg_match_all(
                                '/<a name="(?<fragments>[^"]+)"><\\/a>\n#+ (?<titles>[^\\n]+)/',
                                $contents,
                                $section
                            );

                            return [
                                (string) Str::of($path)
                                    ->afterLast("/")
                                    ->before(".md") => [
                                    "title" => $page["title"] ?? "",
                                    "sections" => collect($section["fragments"])
                                        ->combine($section["titles"])
                                        ->map(
                                            fn($title) => ["title" => $title]
                                        ),
                                ],
                            ];
                        }),
                ];
            }
        );
    }

    /**
     * Replace the version place-holder in links.
     */
    public static function replaceLinks(string $content): string
    {
        // Remove /docs/{{version}}/ and replace with just /docs/
        $content = str_replace("/docs/{{version}}/", "/docs/", $content);
        $content = str_replace("/docs/%7B%7Bversion%7D%7D/", "/docs/", $content);
        return $content;
    }

    /**
     * Check if the given section exists.
     */
    public function sectionExists(string $page): bool
    {
        // Sanitize page path to prevent path traversal attacks
        $page = $this->sanitizePath($page);

        if ($page === null) {
            return false;
        }

        return $this->files->exists(
            base_path("resources/docs/main/" . $page . ".md")
        );
    }

    /**
     * Get the URL to edit a documentation file on GitHub.
     */
    public function getEditUrlForPage(string $page): string
    {
        // Sanitize page path to prevent path traversal attacks
        $page = $this->sanitizePath($page);

        if ($page === null) {
            $page = '';
        }

        $baseEditUrl = "https://github.com/magentoopensource/docs/edit/main";
        return "{$baseEditUrl}/{$page}.md";
    }

    /**
     * Sanitize a path to prevent path traversal attacks.
     * Returns null if path is invalid.
     */
    protected function sanitizePath(string $path): ?string
    {
        // Remove null bytes
        $path = str_replace("\0", '', $path);

        // Normalize path separators to forward slash
        $path = str_replace('\\', '/', $path);

        // Remove any attempts at parent directory traversal
        if (str_contains($path, '..')) {
            return null;
        }

        // Remove leading slashes
        $path = ltrim($path, '/');

        // Ensure path doesn't start with a protocol or absolute path indicator
        if (preg_match('/^[a-z]+:/i', $path)) {
            return null;
        }

        // Additional validation: only allow alphanumeric, hyphens, underscores, forward slashes
        if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $path)) {
            return null;
        }

        return $path;
    }

    /**
     * Extract table of contents from HTML content.
     * Returns array of headings with their text, slug, and level.
     */
    public static function extractTableOfContents(string $html): array
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $headings = [];

        // Extract h2 and h3 headings
        $crawler->filter('h2, h3')->each(function ($node) use (&$headings) {
            $text = $node->text();
            $level = (int) str_replace('h', '', $node->nodeName());

            // Generate slug from heading text
            $slug = Str::slug($text);

            // Check if heading has an anchor name or id
            $id = $node->attr('id') ?: $slug;

            // Check for anchor tags immediately before the heading
            $previousNode = $node->getNode(0)->previousSibling;
            if ($previousNode && $previousNode->nodeName === 'a' && $previousNode->hasAttribute('name')) {
                $id = $previousNode->getAttribute('name');
            }

            $headings[] = [
                'text' => $text,
                'slug' => $id,
                'level' => $level,
            ];
        });

        return $headings;
    }
}
