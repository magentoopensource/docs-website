<?php

namespace Tests\Feature;

use Tests\TestCase;

class DocsControllerTest extends TestCase
{
    /**
     * Test homepage loads successfully
     */
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('homepage');
        $response->assertViewHas('categories');
        $response->assertSee('Merchant Documentation');
    }

    /**
     * Test docs index page loads
     */
    public function test_docs_index_loads(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    /**
     * Test documentation page loads successfully
     */
    public function test_documentation_page_loads(): void
    {
        $response = $this->get('/docs/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewIs('docs');
        $response->assertViewHas(['content', 'index', 'currentSection']);
    }

    /**
     * Test 404 for non-existent documentation page
     */
    public function test_nonexistent_page_returns_404(): void
    {
        $response = $this->get('/docs/category/page-does-not-exist');

        $response->assertStatus(404);
        $response->assertViewIs('docs');
        $response->assertSee('Page not found');
    }

    /**
     * Test category page loads successfully
     */
    public function test_category_page_loads(): void
    {
        $response = $this->get('/docs/start-selling');

        $response->assertStatus(200);
        $response->assertViewHas(['category', 'articles']);
    }

    /**
     * Test non-existent category returns 404
     */
    public function test_nonexistent_category_returns_404(): void
    {
        $response = $this->get('/docs/category-that-does-not-exist');

        $response->assertStatus(404);
    }

    /**
     * Test JSON index endpoint
     */
    public function test_json_index_returns_valid_json(): void
    {
        $response = $this->get('/docs/index.json');

        $response->assertStatus(200);
        $response->assertJson([]);
        $this->assertIsArray($response->json());
    }

    /**
     * Test team page loads
     */
    public function test_team_page_loads(): void
    {
        $response = $this->get('/team');

        $response->assertStatus(200);
        $response->assertViewIs('team');
        $response->assertViewHas('team');
        $response->assertSee('Team');
    }

    /**
     * Test documentation page has proper meta tags
     */
    public function test_documentation_page_has_meta_tags(): void
    {
        $response = $this->get('/docs/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewHas(['metaTitle', 'metaDescription', 'metaKeywords']);
    }

    /**
     * Test documentation page has table of contents
     */
    public function test_documentation_page_has_table_of_contents(): void
    {
        $response = $this->get('/docs/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewHas('tableOfContents');
        $tableOfContents = $response->viewData('tableOfContents');
        $this->assertIsArray($tableOfContents);
    }

    /**
     * Test documentation page has edit link
     */
    public function test_documentation_page_has_edit_link(): void
    {
        $response = $this->get('/docs/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewHas('edit_link');
        $editLink = $response->viewData('edit_link');
        $this->assertStringContainsString('github.com/mage-os/devdocs', $editLink);
    }
}
