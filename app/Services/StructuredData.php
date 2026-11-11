<?php

namespace App\Services;

/**
 * JSON-LD builders for the Laravel-rendered pages. The Organization and WebSite nodes carry the same @id,
 * name, url and sameAs as the static pages (bin/devdocs/seo_head.py), see config/seo.php.
 */
class StructuredData
{
    /**
     * Encode for a <script type="application/ld+json"> element. "<", ">" and "&" are escaped so a value can
     * never close the script element early; the result is still plain valid JSON.
     */
    public static function encode(array $data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );
    }

    public static function organization(): array
    {
        $org = config('seo.organization');

        return [
            '@type' => 'Organization',
            '@id' => $org['id'],
            'name' => $org['name'],
            'url' => $org['url'],
            'logo' => $org['logo'],
            'sameAs' => array_values($org['same_as']),
        ];
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => config('seo.website_id'),
            'url' => rtrim(config('seo.site'), '/') . '/',
            'name' => config('seo.site_name'),
            'inLanguage' => 'en',
            'publisher' => ['@id' => config('seo.organization.id')],
        ];
    }

    /**
     * Sitewide block: Organization and WebSite.
     */
    public static function site(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [self::organization(), self::website()],
        ];
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $items  [name, absolute url], site root first
     */
    public static function breadcrumb(array $items): array
    {
        $list = [];
        foreach (array_values($items) as $i => [$name, $url]) {
            $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $url];
        }

        return ['@type' => 'BreadcrumbList', 'itemListElement' => $list];
    }

    public static function techArticle(string $headline, string $description, string $url, ?string $dateModified, ?string $image = null): array
    {
        $node = [
            '@type' => 'TechArticle',
            '@id' => $url . '#article',
            'headline' => mb_substr($headline, 0, 110),
            'description' => $description,
            'url' => $url,
            'mainEntityOfPage' => $url,
            'inLanguage' => 'en',
            'image' => $image ?? config('seo.image'),
            'author' => ['@type' => 'Organization', 'name' => config('seo.author_name')],
            'publisher' => [
                '@type' => 'Organization',
                '@id' => config('seo.organization.id'),
                'name' => config('seo.organization.name'),
            ],
            'isPartOf' => ['@id' => config('seo.website_id')],
        ];

        if ($dateModified) {
            $node['dateModified'] = $dateModified;
        }

        return $node;
    }

    /**
     * Question and answer pairs found in an article body: every h2 or h3 whose text ends with "?" and the first
     * paragraph that follows it inside the same parent. Only text that is visible on the page is returned, which
     * is what FAQPage markup requires.
     *
     * @return list<array{question: string, answer: string}>
     */
    public static function faqPairs(string $html): array
    {
        if (!str_contains($html, '?</h')) {
            return [];
        }

        $pairs = [];
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $crawler->filter('h2, h3')->each(function ($heading) use (&$pairs) {
            $question = trim(preg_replace('/\s+/u', ' ', $heading->text()) ?? '');
            if ($question === '' || !str_ends_with($question, '?')) {
                return;
            }

            for ($node = $heading->getNode(0)->nextSibling; $node !== null; $node = $node->nextSibling) {
                if ($node->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }
                if ($node->nodeName === 'p') {
                    $answer = trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? '');
                    if ($answer !== '') {
                        $pairs[] = ['question' => $question, 'answer' => $answer];
                    }
                }
                break;
            }
        });

        return $pairs;
    }

    /**
     * FAQPage node for the given pairs, or null when there are none.
     *
     * @param  list<array{question: string, answer: string}>  $pairs
     */
    public static function faqPage(string $url, array $pairs): ?array
    {
        if ($pairs === []) {
            return null;
        }

        return [
            '@type' => 'FAQPage',
            '@id' => $url . '#faq',
            'isPartOf' => ['@id' => $url . '#article'],
            'mainEntity' => array_map(fn (array $p) => [
                '@type' => 'Question',
                'name' => $p['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $p['answer']],
            ], $pairs),
        ];
    }

    /**
     * Page-level block wrapping the given nodes.
     */
    public static function graph(?array ...$nodes): array
    {
        return ['@context' => 'https://schema.org', '@graph' => array_values(array_filter($nodes))];
    }
}
