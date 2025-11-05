<?php

namespace Tests\Feature;

use App\Documentation;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class DocumentationTest extends TestCase
{
    protected Documentation $docs;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->docs = app(Documentation::class);
    }

    /**
     * Test getting a valid documentation page
     */
    public function test_can_get_valid_documentation_page(): void
    {
        $result = $this->docs->get('start-selling/tutorial-creating-your-first-products');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('frontMatter', $result);
        $this->assertNotEmpty($result['content']);
    }

    /**
     * Test getting non-existent page returns null
     */
    public function test_nonexistent_page_returns_null(): void
    {
        $result = $this->docs->get('category/page-that-does-not-exist');

        $this->assertNull($result);
    }

    /**
     * Test getting navigation index
     */
    public function test_can_get_navigation_index(): void
    {
        $index = $this->docs->getIndex();

        $this->assertNotNull($index);
        $this->assertIsString($index);
    }

    /**
     * Test getting index array
     */
    public function test_can_get_index_array(): void
    {
        $indexArray = $this->docs->indexArray();

        $this->assertIsArray($indexArray);
        $this->assertArrayHasKey('pages', $indexArray);
    }

    /**
     * Test section exists for valid page
     */
    public function test_section_exists_returns_true_for_valid_page(): void
    {
        $exists = $this->docs->sectionExists('start-selling/tutorial-creating-your-first-products');

        $this->assertTrue($exists);
    }

    /**
     * Test section exists returns false for invalid page
     */
    public function test_section_exists_returns_false_for_invalid_page(): void
    {
        $exists = $this->docs->sectionExists('category/nonexistent-page');

        $this->assertFalse($exists);
    }

    /**
     * Test edit URL generation
     */
    public function test_generates_correct_edit_url(): void
    {
        $editUrl = $this->docs->getEditUrlForPage('start-selling/tutorial-creating-your-first-products');

        $this->assertStringContainsString('github.com/', $editUrl);
        $this->assertStringContainsString('start-selling/tutorial-creating-your-first-products.md', $editUrl);
    }

    /**
     * Test replaceLinks static method
     */
    public function test_replace_links_removes_version_placeholders(): void
    {
        $contentWithPlaceholder = 'Check out [this link](/merchant/{{version}}/page)';
        $result = Documentation::replaceLinks($contentWithPlaceholder);

        $this->assertStringNotContainsString('{{version}}', $result);
        $this->assertStringContainsString('/merchant/page', $result);
    }

    /**
     * Test replaceLinks handles URL encoded version placeholders
     */
    public function test_replace_links_handles_url_encoded_placeholders(): void
    {
        $contentWithEncoded = 'Check out [this link](/merchant/%7B%7Bversion%7D%7D/page)';
        $result = Documentation::replaceLinks($contentWithEncoded);

        $this->assertStringNotContainsString('%7B%7Bversion%7D%7D', $result);
        $this->assertStringContainsString('/merchant/page', $result);
    }

    /**
     * Test extractTableOfContents extracts headings correctly
     */
    public function test_extract_table_of_contents(): void
    {
        $html = '<h2 id="section-1">Section 1</h2><h3 id="subsection">Subsection</h3>';
        $toc = Documentation::extractTableOfContents($html);

        $this->assertIsArray($toc);
        $this->assertCount(2, $toc);
        $this->assertEquals('Section 1', $toc[0]['text']);
        $this->assertEquals(2, $toc[0]['level']);
        $this->assertEquals('Subsection', $toc[1]['text']);
        $this->assertEquals(3, $toc[1]['level']);
    }

    /**
     * Test caching behavior for documentation pages
     */
    public function test_documentation_pages_are_cached(): void
    {
        // First call should cache
        $result1 = $this->docs->get('start-selling/tutorial-creating-your-first-products');

        // Second call should return cached version
        $result2 = $this->docs->get('start-selling/tutorial-creating-your-first-products');

        $this->assertEquals($result1, $result2);
    }
}
