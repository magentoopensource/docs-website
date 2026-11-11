<?php

namespace Tests\Feature;

use App\Services\ContentDates;
use App\Services\Redirects;
use App\Services\StructuredData;
use Tests\TestCase;

class StructuredDataTest extends TestCase
{
    /**
     * @return array<int, array<string, mixed>> every JSON-LD node on the page, @graph flattened
     */
    private function nodes(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $nodes = [];
        foreach ($m[1] as $json) {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            foreach ($data['@graph'] ?? [$data] as $node) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    private function ofType(array $nodes, string $type): ?array
    {
        foreach ($nodes as $node) {
            if (($node['@type'] ?? null) === $type) {
                return $node;
            }
        }

        return null;
    }

    public function testGuideCarriesOrganizationWebsiteBreadcrumbAndArticle(): void
    {
        $nodes = $this->nodes($this->get('/merchant/start-selling/tutorial-creating-your-first-products')->assertOk()->getContent());

        $this->assertNotNull($this->ofType($nodes, 'Organization'));
        $this->assertNotNull($this->ofType($nodes, 'WebSite'));
        $this->assertCount(3, $this->ofType($nodes, 'BreadcrumbList')['itemListElement']);

        $article = $this->ofType($nodes, 'TechArticle');
        $this->assertNotNull($article);
        $this->assertSame('Organization', $article['author']['@type']);
        $this->assertSame(config('seo.organization.id'), $article['publisher']['@id']);
        $this->assertNotEmpty($article['headline']);
    }

    public function testHubCarriesBreadcrumbList(): void
    {
        $nodes = $this->nodes($this->get('/merchant/grow-store')->assertOk()->getContent());

        $this->assertCount(2, $this->ofType($nodes, 'BreadcrumbList')['itemListElement']);
        $this->assertNull($this->ofType($nodes, 'TechArticle'));
    }

    public function testNotFoundPageHasNoArticleMarkup(): void
    {
        $nodes = $this->nodes($this->get('/merchant/category/page-does-not-exist')->assertNotFound()->getContent());

        $this->assertNull($this->ofType($nodes, 'TechArticle'));
    }

    public function testEncodingCannotCloseTheScriptElement(): void
    {
        $json = StructuredData::encode(['name' => '</script><b>&']);

        $this->assertStringNotContainsString('</script>', $json);
        $this->assertSame('</script><b>&', json_decode($json, true)['name']);
    }

    public function testLastUpdatedDateIsInsertedAfterTheHeading(): void
    {
        $html = ContentDates::insertAfterHeading('<h1>Title</h1><p>Body</p>', '2025-12-13T16:05:00+00:00');

        $this->assertStringContainsString('</h1>' . "\n" . '<p class="docs-last-updated">Last updated <time datetime="2025-12-13">13 December 2025</time></p><p>Body</p>', $html);
        $this->assertSame('<p>x</p>', ContentDates::insertAfterHeading('<p>x</p>', '2025-12-13T16:05:00+00:00'));
        $this->assertSame('<h1>T</h1>', ContentDates::insertAfterHeading('<h1>T</h1>', 'not a date'));
    }

    public function testMissingContentCloneGivesNoDatesAndNoError(): void
    {
        $dates = new class (app('cache.store')) extends ContentDates {
            protected function readFromGit(string $dir): array
            {
                return parent::readFromGit('/nonexistent/clone');
            }
        };

        $this->assertSame([], $dates->refresh());
        $this->assertNull($dates->forPath('grow-store/anything'));
        $this->assertNull($dates->newest());
    }

    public function testSitemapListsMerchantLastmodAndOmitsRetiredPages(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#<loc>[^<]*/merchant</loc><lastmod>\d{4}-\d{2}-\d{2}#', $xml);
        foreach (['hero-options', 'catalog-overview.html', 'plugin-system-deep-dive.html', 'contributors.html', 'docs-landing', 'hexagon'] as $gone) {
            $this->assertStringNotContainsString($gone, str_replace('module-catalog-overview.html', '', str_replace('guide-tutorial-plugin-system-deep-dive.html', '', $xml)));
        }
    }

    public function testRetiredDeveloperPagesRedirectPermanently(): void
    {
        $this->get('/developer/catalog-overview.html')->assertStatus(301)->assertRedirect('/developer/module-catalog-overview.html');
        $this->get('/developer/plugin-system-deep-dive.html')->assertStatus(301)->assertRedirect('/developer/guide-tutorial-plugin-system-deep-dive.html');
        $this->get('/developer/contributors.html')->assertStatus(301)->assertRedirect('/contributors');
    }

    public function testUnknownPathStillReturns404(): void
    {
        $this->get('/developer/never-existed.html')->assertNotFound();
    }

    public function testRedirectMapSupportsPatternsAndMerchantSlugs(): void
    {
        config(['redirects.exact' => ['/merchant/a/old' => '/merchant/a/new']]);

        $this->assertSame('/the-core/modules/Magento_Sales/html/', Redirects::resolve('/the-core/modules/Magento_Sales/html/index.html'));
        $this->assertNull(Redirects::resolve('/the-core/modules/Magento_Sales/html/'));
        $this->get('/merchant/a/old')->assertStatus(301)->assertRedirect('/merchant/a/new');
    }

    public function testLlmsTxtIsPlainTextAndListsTheSections(): void
    {
        $response = $this->get('/llms.txt')->assertOk();

        $this->assertStringStartsWith('text/plain', $response->headers->get('Content-Type'));
        $response->assertSee('## Merchant documentation')->assertSee('## Developer documentation')->assertSee('## The Core');
    }
}
