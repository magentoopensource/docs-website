<?php

namespace Tests\Feature;

use App\Services\Redirects;
use App\Services\StructuredData;
use Tests\TestCase;

/**
 * SEO plan phase 4 and 5: renamed guide URLs, question-style answers with FAQPage markup, hub introductions and
 * per-guide social images.
 */
class GuideContentTest extends TestCase
{
    private const NEW_URL = '/merchant/getting-started/how-to-configure-domain-and-email';

    private const OLD_URL = '/merchant/getting-started/howto-how-to-configure-domain-and-email';

    public function testRenamedGuideAnswers200AtItsNewUrl(): void
    {
        $this->get(self::NEW_URL)->assertOk();
    }

    public function testOldGuideUrlRedirectsPermanentlyToTheNewUrl(): void
    {
        $this->get(self::OLD_URL)->assertStatus(301)->assertRedirect(self::NEW_URL);
    }

    public function testTruncatedPriceRulesSlugIsRepaired(): void
    {
        $this->get('/merchant/grow-store/tutorial-getting-started-with-create-shopping-cart-price-ru')
            ->assertStatus(301)
            ->assertRedirect('/merchant/grow-store/tutorial-create-shopping-cart-price-rules');
        $this->get('/merchant/grow-store/tutorial-create-shopping-cart-price-rules')->assertOk();
    }

    public function testCurrentAndPreviousGuidePathsAreInverseOfTheRedirectMap(): void
    {
        $old = 'getting-started/howto-how-to-configure-domain-and-email';
        $new = 'getting-started/how-to-configure-domain-and-email';

        $this->assertSame($new, Redirects::currentGuidePath($old));
        $this->assertSame($new, Redirects::currentGuidePath($new));
        $this->assertSame([$old], Redirects::previousGuidePaths($new));
        $this->assertSame([], Redirects::previousGuidePaths('start-selling/tutorial-creating-your-first-products'));
    }

    public function testQuestionHeadingsBecomeFaqPairsWithTheirVisibleAnswer(): void
    {
        $html = '<h2>Overview</h2><p>Not a question.</p>'
            . '<div><h3 class="x">Why is it slow?</h3> <p class="y">Because of caching &amp; cookies.</p></div>'
            . '<h3>What is it?</h3><ul><li>no paragraph first</li></ul>';

        $this->assertSame(
            [['question' => 'Why is it slow?', 'answer' => 'Because of caching & cookies.']],
            StructuredData::faqPairs($html)
        );
        $this->assertNull(StructuredData::faqPage('https://example.test/a', []));
    }

    public function testGuideWithQuestionAnswersPublishesFaqPage(): void
    {
        $html = $this->get(self::NEW_URL)->assertOk()->getContent();
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $faq = null;
        foreach ($m[1] as $json) {
            foreach (json_decode($json, true, 512, JSON_THROW_ON_ERROR)['@graph'] ?? [] as $node) {
                if (($node['@type'] ?? null) === 'FAQPage') {
                    $faq = $node;
                }
            }
        }

        $this->assertNotNull($faq, 'FAQPage node present');
        $this->assertCount(2, $faq['mainEntity']);
        $this->assertSame('Question', $faq['mainEntity'][0]['@type']);
        $this->assertStringEndsWith('?', $faq['mainEntity'][0]['name']);
        // The marked-up answer is visible text on the page.
        $this->assertStringContainsString($faq['mainEntity'][0]['acceptedAnswer']['text'], html_entity_decode(strip_tags($html)));
    }

    public function testGuideWithoutQuestionsHasNoFaqPage(): void
    {
        $this->get('/merchant/start-selling/tutorial-creating-your-first-products')
            ->assertOk()
            ->assertDontSee('"FAQPage"', false);
    }

    public function testHubIntroIsAHundredToOneFiftyWords(): void
    {
        $html = $this->get('/merchant/grow-store')->assertOk()->getContent();
        $this->assertSame(1, preg_match('#<p data-hub-intro[^>]*>(.*?)</p>#s', $html, $m));
        $words = str_word_count(html_entity_decode(strip_tags($m[1])), 0, '0123456789-');
        $this->assertGreaterThanOrEqual(100, $words);
        $this->assertLessThanOrEqual(150, $words);
        $this->assertStringNotContainsString('Learn more about', $html);
    }

    public function testGuideUsesItsOwnSocialImage(): void
    {
        $url = url('/img/og/getting-started/how-to-configure-domain-and-email.png');
        $html = $this->get(self::NEW_URL)->assertOk()->getContent();

        $this->assertStringContainsString('<meta property="og:image" content="' . $url . '">', $html);
        $this->assertStringContainsString('<meta name="twitter:image" content="' . $url . '">', $html);
        $this->assertFileExists(public_path('img/og/getting-started/how-to-configure-domain-and-email.png'));
    }

    public function testPagesWithoutAGuideImageKeepTheSharedOne(): void
    {
        $this->get('/merchant')->assertOk()->assertSee('/img/merchant-docs-og.png', false);
    }
}
