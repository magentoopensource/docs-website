<?php

namespace Tests\Feature;

use App\Http\Controllers\DocsController;
use Tests\TestCase;

class DocsControllerTest extends TestCase
{
    /**
     * Test homepage loads successfully
     */
    public function testHomepageLoadsSuccessfully(): void
    {
        $response = $this->get('/merchant');

        $response->assertStatus(200);
        $response->assertViewIs('homepage');
        $response->assertViewHas('categories');
        $response->assertSee('Merchant Documentation');
    }

    /**
     * Test merchant docs index page loads
     */
    public function testDocsIndexLoads(): void
    {
        $response = $this->get('/merchant');

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    /**
     * Test documentation page loads successfully
     */
    public function testDocumentationPageLoads(): void
    {
        $response = $this->get('/merchant/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewIs('docs');
        $response->assertViewHas(['content', 'index', 'currentSection']);
    }

    /**
     * Test 404 for non-existent documentation page
     */
    public function testNonexistentPageReturns404(): void
    {
        $response = $this->get('/merchant/category/page-does-not-exist');

        $response->assertStatus(404);
        $response->assertViewIs('docs');
        $response->assertSee('Page not found');
    }

    /**
     * Test category page loads successfully
     */
    public function testCategoryPageLoads(): void
    {
        $response = $this->get('/merchant/start-selling');

        $response->assertStatus(200);
        $response->assertViewHas(['category', 'articles']);
    }

    /**
     * Test non-existent category returns 404
     */
    public function testNonexistentCategoryReturns404(): void
    {
        $response = $this->get('/merchant/category-that-does-not-exist');

        $response->assertStatus(404);
    }

    /**
     * Test JSON index endpoint
     */
    public function testJsonIndexReturnsValidJson(): void
    {
        $response = $this->get('/merchant/index.json');

        $response->assertStatus(200);
        $response->assertJson([]);
        $this->assertIsArray($response->json());
    }

    /**
     * Test team page loads
     */
    public function testTeamPageLoads(): void
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
    public function testDocumentationPageHasMetaTags(): void
    {
        $response = $this->get('/merchant/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewHas(['metaTitle', 'metaDescription', 'metaKeywords']);
    }

    /**
     * Test documentation page has table of contents
     */
    public function testDocumentationPageHasTableOfContents(): void
    {
        $response = $this->get('/merchant/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewHas('tableOfContents');
        $tableOfContents = $response->viewData('tableOfContents');
        $this->assertIsArray($tableOfContents);
    }

    /**
     * Test documentation page has edit link
     */
    public function testDocumentationPageHasEditLink(): void
    {
        $response = $this->get('/merchant/start-selling/tutorial-creating-your-first-products');

        $response->assertStatus(200);
        $response->assertViewHas('edit_link');
        $editLink = $response->viewData('edit_link');
        $this->assertStringContainsString('github.com/', $editLink);
    }
    /**
     * /merchant names itself as canonical and og:url (it declared the site root before).
     */
    public function testMerchantHubCanonicalAndOgUrlAreTheHub(): void
    {
        $response = $this->get('/merchant');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical" href="' . url('/merchant') . '">', false);
        $response->assertSee('<meta property="og:url" content="' . url('/merchant') . '">', false);
    }

    /**
     * The documentation index page canonicalises to itself, never to the non-existent /docs.
     */
    public function testDocumentationIndexCanonicalIsItsOwnUrl(): void
    {
        $response = $this->get('/merchant/index');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical" href="' . url('/merchant/index') . '">', false);
        $response->assertDontSee(url('/docs') . '"', false);
    }

    /**
     * Titles use the Magento Open Source brand, stay within 60 characters and never repeat the label.
     */
    public function testPageTitlesUseShortOpenSourceSuffix(): void
    {
        $this->assertSame('Magento Open Source Merchant Documentation', DocsController::pageTitle(null));
        $this->assertSame('Contributors | Magento Open Source', DocsController::pageTitle('Contributors'));
        $named = 'Magento Open Source Merchant Documentation';
        $this->assertSame($named, DocsController::pageTitle($named));

        $long = 'How to Configure Shipping Methods and Rates for Your Store';
        $this->assertSame($long, DocsController::pageTitle($long), 'a title too long for the suffix stays bare');

        $this->get('/merchant')->assertSee('<title>Magento Open Source Merchant Documentation</title>', false);
        $this->get('/merchant/grow-store')->assertSee('<title>Grow Store | Magento Open Source</title>', false);
        $this->get('/contributors')->assertSee('<title>Contributors | Magento Open Source</title>', false);
    }

    /**
     * Metadata no longer says "Magento 2", and Twitter tags use name= (property= is not read by X).
     */
    public function testMetaTagsUseOpenSourceWordingAndValidTwitterTags(): void
    {
        $response = $this->get('/merchant');

        $html = $response->getContent();
        $head = substr($html, 0, strpos($html, '</head>'));
        $this->assertStringNotContainsString('Magento 2', $head, 'head says Magento Open Source');
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertDontSee('property="twitter:', false);
        $response->assertDontSee('twitter:url', false);
    }

    /**
     * Every hub has its own description, and the hub intro uses it instead of the "Learn more about" stub.
     */
    public function testEveryCategoryHubHasADescriptionAndIntro(): void
    {
        foreach (['reports-and-analytics', 'support-and-resources', 'grow-store'] as $slug) {
            $response = $this->get('/merchant/' . $slug);

            $response->assertStatus(200);
            $description = $response->viewData('metaDescription');
            $this->assertNotSame('', trim($description), $slug . ' has an empty meta description');
            $this->assertStringNotContainsString('Learn about', $description);
            $response->assertDontSee('Learn more about', false);
            $response->assertSee($description);
        }
    }

    /**
     * Hub cards show a reading time from the article length, not a constant 5.
     */
    public function testCategoryHubReadTimesComeFromWordCount(): void
    {
        $articles = $this->get('/merchant/grow-store')->viewData('articles');

        $this->assertGreaterThan(1, count($articles));
        $times = collect($articles)->pluck('read_time');
        $this->assertTrue($times->every(fn ($t) => is_int($t) && $t >= 1));
        $this->assertGreaterThan(1, $times->unique()->count(), 'read times differ between short and long guides');
    }

    /**
     * The mobile menu is not a heading, and lists every section including Improve UX.
     */
    public function testNavigationListsAllSectionsWithoutAMenuHeading(): void
    {
        $response = $this->get('/merchant/grow-store');

        $response->assertSee('href="/merchant/improve-ux"', false);
        $response->assertDontSee('>Menu</h2>', false);
    }
}
