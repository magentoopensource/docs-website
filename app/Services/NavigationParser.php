<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Cache\Repository as Cache;

class NavigationParser
{
    public function __construct(
        protected Filesystem $files,
        protected Cache $cache
    ) {
    }

    /**
     * Parse navigation.md and extract categories with their articles.
     */
    public function getCategories(): Collection
    {
        return $this->cache->remember('navigation.categories', 3600, function () {
            $path = base_path('resources/docs/main/navigation.md');

            if (!$this->files->exists($path)) {
                return collect();
            }

            $content = $this->files->get($path);
            $lines = explode("\n", $content);

            $categories = collect();
            $currentCategory = null;

            foreach ($lines as $line) {
                // Match category headers: - ## Category Name
                if (preg_match('/^- ## (.+)$/', $line, $matches)) {
                    // Save previous category if exists
                    if ($currentCategory && !empty($currentCategory['articles'])) {
                        $categories->push($currentCategory);
                    }

                    $categoryName = trim($matches[1]);
                    $slug = Str::slug($categoryName);

                    $currentCategory = [
                        'name' => $categoryName,
                        'slug' => $slug,
                        'articles' => [],
                    ];

                    continue;
                }

                // Match article links: - [Article Title](/docs/{{version}}/path/to/article)
                if ($currentCategory && preg_match('/^\s+- \[(.+)\]\(\/docs\/\{\{version\}\}\/(.+)\)$/', $line, $matches)) {
                    $articleTitle = trim($matches[1]);
                    $articlePath = trim($matches[2]);

                    $currentCategory['articles'][] = [
                        'title' => $articleTitle,
                        'path' => $articlePath,
                        'slug' => $this->getSlugFromPath($articlePath),
                        'category_slug' => $this->getCategoryFromPath($articlePath),
                    ];
                }
            }

            // Add the last category if exists
            if ($currentCategory && !empty($currentCategory['articles'])) {
                $categories->push($currentCategory);
            }

            return $categories;
        });
    }

    /**
     * Get a specific category by slug.
     */
    public function getCategory(string $slug): ?array
    {
        return $this->getCategories()->firstWhere('slug', $slug);
    }

    /**
     * Get articles for a specific category.
     */
    public function getCategoryArticles(string $categorySlug): Collection
    {
        $category = $this->getCategory($categorySlug);

        if (!$category) {
            return collect();
        }

        return collect($category['articles']);
    }

    /**
     * Extract the category from a path like "shipping/rates" → "shipping"
     */
    protected function getCategoryFromPath(string $path): string
    {
        if (Str::contains($path, '/')) {
            return Str::before($path, '/');
        }

        return 'general';
    }

    /**
     * Extract the slug from a path like "shipping/rates" → "rates"
     */
    protected function getSlugFromPath(string $path): string
    {
        if (Str::contains($path, '/')) {
            return Str::afterLast($path, '/');
        }

        return $path;
    }

    /**
     * Clear the navigation cache.
     */
    public function clearCache(): void
    {
        $this->cache->forget('navigation.categories');
    }
}
