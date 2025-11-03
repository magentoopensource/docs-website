<?php

namespace Tests\Feature;

use App\Services\NavigationParser;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class NavigationParserTest extends TestCase
{
    protected NavigationParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->parser = app(NavigationParser::class);
    }

    /**
     * Test getting all categories
     */
    public function test_can_get_categories(): void
    {
        $categories = $this->parser->getCategories();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $categories);
        $this->assertNotEmpty($categories);

        // Each category should have required keys
        $firstCategory = $categories->first();
        $this->assertArrayHasKey('name', $firstCategory);
        $this->assertArrayHasKey('slug', $firstCategory);
        $this->assertArrayHasKey('articles', $firstCategory);
    }

    /**
     * Test getting a specific category by slug
     */
    public function test_can_get_category_by_slug(): void
    {
        $category = $this->parser->getCategory('start-selling');

        $this->assertIsArray($category);
        $this->assertEquals('start-selling', $category['slug']);
        $this->assertArrayHasKey('articles', $category);
    }

    /**
     * Test getting non-existent category returns null
     */
    public function test_nonexistent_category_returns_null(): void
    {
        $category = $this->parser->getCategory('category-that-does-not-exist');

        $this->assertNull($category);
    }

    /**
     * Test getting category articles
     */
    public function test_can_get_category_articles(): void
    {
        $articles = $this->parser->getCategoryArticles('start-selling');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $articles);

        if ($articles->isNotEmpty()) {
            $firstArticle = $articles->first();
            $this->assertArrayHasKey('title', $firstArticle);
            $this->assertArrayHasKey('path', $firstArticle);
            $this->assertArrayHasKey('slug', $firstArticle);
        }
    }

    /**
     * Test getting articles for non-existent category returns empty collection
     */
    public function test_nonexistent_category_articles_returns_empty(): void
    {
        $articles = $this->parser->getCategoryArticles('nonexistent-category');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $articles);
        $this->assertTrue($articles->isEmpty());
    }

    /**
     * Test cache is used for categories
     */
    public function test_categories_are_cached(): void
    {
        // First call should cache
        $categories1 = $this->parser->getCategories();

        // Second call should return cached version
        $categories2 = $this->parser->getCategories();

        $this->assertEquals($categories1, $categories2);
    }

    /**
     * Test clearing cache
     */
    public function test_can_clear_cache(): void
    {
        // Load categories to cache them
        $this->parser->getCategories();

        // Clear cache
        $this->parser->clearCache();

        // Cache should be cleared (we can't directly assert this, but we can verify it doesn't throw)
        $categories = $this->parser->getCategories();
        $this->assertNotNull($categories);
    }
}
