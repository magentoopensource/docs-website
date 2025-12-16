<?php

namespace App\Http\Controllers;

use App\Documentation;
use App\Services\NavigationParser;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class DocsController extends Controller
{
    public const DEFAULT_META_TITLE = "Magento 2 Merchant Documentation - Your Complete Guide";
    public const DEFAULT_META_DESCRIPTION = "Complete guide to managing your Magento 2 store. Learn how to sell products, manage orders, grow your business, and stay compliant with our comprehensive merchant documentation.";
    const DEFAULT_META_KEYWORDS = "Magento 2, Merchant Documentation, Magento Store Management, eCommerce Guide, Magento Tutorial";

    public function __construct(
        protected Documentation $docs,
        protected NavigationParser $navigationParser
    ) {
    }

    /**
     * Show the homepage with merchant documentation categories.
     */
    public function showRootPage()
    {
        $categories = $this->navigationParser->getCategories()
            ->map(function ($category) {
                // Limit each category to max 4 articles
                $articles = array_slice($category['articles'], 0, 4);

                // Add icons to each article
                $category['articles'] = array_map(function ($article) {
                    $article['icon'] = $this->getArticleIcon($article['path']);
                    return $article;
                }, $articles);

                // Add metadata (description, color, etc.)
                $metadata = $this->getCategoryMetadata($category['slug']);
                $category['description'] = $metadata['description'];

                return $category;
            });

        return view("homepage", [
            "title" => "Merchant Documentation",
            "metaTitle" => self::DEFAULT_META_TITLE,
            "metaDescription" => self::DEFAULT_META_DESCRIPTION,
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "",
            "categories" => $categories,
        ]);
    }

    /**
     * Show the documentation index page.
     */
    public function showDocsIndex()
    {
        $categories = $this->navigationParser->getCategories()
            ->map(function ($category) {
                // Limit each category to max 4 articles
                $category['articles'] = array_slice($category['articles'], 0, 4);
                return $category;
            });

        return view("docs-index", [
            "title" => "Documentation Index",
            "metaTitle" => "Documentation Index - " . self::DEFAULT_META_TITLE,
            "metaDescription" => self::DEFAULT_META_DESCRIPTION,
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "docs",
            "categories" => $categories,
        ]);
    }

    /**
     * Show the documentation index JSON representation.
     */
    public function index(Documentation $docs)
    {
        return response()->json($docs->indexArray());
    }

    /**
     * Show a documentation page.
     */
    public function show(string $category, string $page)
    {
        $sectionPage = $category . "/" . $page;
        $docPage = $this->docs->get($sectionPage);

        $content = $docPage ? $docPage["content"] : null;
        $pageCustomData = $docPage ? $docPage["frontMatter"] : [];
        $isStyledHtml = $docPage ? ($docPage["isStyledHtml"] ?? false) : false;
        $metaDescription =
            $pageCustomData["description"] ?? self::DEFAULT_META_DESCRIPTION;
        $metaKeywords =
            $pageCustomData["keywords"] ?? self::DEFAULT_META_KEYWORDS;
        $communityNote = $pageCustomData["communityNote"] ?? true;

        if (is_null($content)) {
            return response()->view(
                "docs",
                [
                    "title" => "Page not found",
                    "index" => $this->docs->getIndex(),
                    "content" => view("docs-missing", [
                        "page" => $sectionPage,
                        "category" => $category,
                        "otherVersions" => collect([]), // Empty collection for now as we only have 'main' version
                    ]),
                    "currentSection" => "",
                    "canonical" => null,
                    "category" => $category,
                    "page" => $page,
                    "categoryArticles" => collect([]),
                    "tableOfContents" => [],
                    "metaTitle" => "Page not found - " . self::DEFAULT_META_TITLE,
                    "metaDescription" => self::DEFAULT_META_DESCRIPTION,
                    "metaKeywords" => self::DEFAULT_META_KEYWORDS,
                    "communityNote" => false,
                    "edit_link" => "",
                ],
                404
            );
        }

        $title = (new Crawler($content))->filterXPath("//h1");

        $canonical = null;
        if ($this->docs->sectionExists($sectionPage)) {
            $canonical = "merchant/" . $sectionPage;
        }

        // Get category articles for left sidebar
        $categoryArticles = $this->navigationParser->getCategoryArticles($category);

        // Extract table of contents for right sidebar
        $tableOfContents = Documentation::extractTableOfContents($content);

        // Determine view template from front matter or use default
        $viewTemplate = $pageCustomData["template"] ?? "docs";

        return view($viewTemplate, [
            "title" => count($title)
                ? $title->text()
                : self::DEFAULT_META_TITLE,
            "index" => $this->docs->getIndex(),
            "content" => $content,
            "currentSection" => "/" . $sectionPage,
            "canonical" => $canonical,
            "edit_link" => $this->docs->getEditUrlForPage($sectionPage),
            "metaTitle" => count($title)
                ? $title->text()
                : self::DEFAULT_META_TITLE,
            "metaDescription" => $metaDescription,
            "metaKeywords" => $metaKeywords,
            "communityNote" => $communityNote,
            "category" => $category,
            "page" => $page,
            "categoryArticles" => $categoryArticles,
            "tableOfContents" => $tableOfContents,
            "isStyledHtml" => $isStyledHtml,
        ]);
    }

    /**
     * Show a category page with articles.
     */
    public function showCategory(string $category)
    {
        $categoryData = $this->navigationParser->getCategory($category);

        if (!$categoryData) {
            abort(404);
        }

        $articles = $this->navigationParser->getCategoryArticles($category);

        // Map articles to the expected format with metadata
        $articlesWithMetadata = $articles->map(function ($article) {
            $docPage = $this->docs->get($article["path"]);
            $frontMatter = $docPage["frontMatter"] ?? [];

            return [
                "title" => $article["title"],
                "description" =>
                    $frontMatter["description"] ??
                    "Learn about " . strtolower($article["title"]),
                "url" => "/merchant/" . $article["path"],
                "difficulty" => $frontMatter["difficulty"] ?? "Intermediate",
                "read_time" => $frontMatter["read_time"] ?? 5,
            ];
        });

        // Get category metadata (icons, colors, etc.) for potential future use
        $categoryMeta = $this->getCategoryMetadata($category);

        // Use unified category view for all categories
        return view("category.show", [
            "title" => $categoryData["name"] . " - Merchant Documentation",
            "metaTitle" =>
                $categoryData["name"] . " - " . self::DEFAULT_META_TITLE,
            "metaDescription" =>
                $categoryMeta["description"] ??
                "Learn about " . $categoryData["name"],
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "merchant/" . $category,
            "category" => $categoryData,
            "articles" => $articlesWithMetadata,
        ]);
    }

    /**
     * Get category metadata (icons, colors, descriptions).
     */
    protected function getCategoryMetadata(string $category): array
    {
        $metadata = [
            "getting-started" => [
                "color" => "bg-blue-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>',
                "description" =>
                    "Get your store up and running with essential setup and configuration guides.",
            ],
            "start-selling" => [
                "color" => "bg-green-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                "description" =>
                    "Get your store up and running with your first products, payments, and shipping setup.",
            ],
            "manage-catalog" => [
                "color" => "bg-yellow-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
                "description" =>
                    "Organize and manage your product catalog efficiently with bulk operations.",
            ],
            "handle-orders" => [
                "color" => "bg-blue-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
                "description" =>
                    "Process orders, handle refunds, and manage shipping efficiently.",
            ],
            "grow-store" => [
                "color" => "bg-purple-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                "description" =>
                    "Boost sales with marketing tools, analytics, and promotional strategies.",
            ],
            "improve-ux" => [
                "color" => "bg-pink-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>',
                "description" =>
                    "Enhance customer experience with design, navigation, and performance optimizations.",
            ],
            "stay-compliant" => [
                "color" => "bg-red-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                "description" =>
                    "Ensure your store meets legal requirements and data protection standards.",
            ],
        ];

        return $metadata[$category] ?? [
            "color" => "bg-gray-100",
            "icon" =>
                '<svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            "description" => "",
        ];
    }

    /**
     * Get the icon filename for an article based on its path/title keywords.
     */
    protected function getArticleIcon(string $path): string
    {
        $pathLower = strtolower($path);

        // Keywords to icon mapping
        $iconMappings = [
            // Products & Catalog
            'product' => 'simple-products',
            'first-product' => 'simple-products',
            'inventory' => 'inventory',
            'image' => 'product-images',
            'import' => 'import-products',
            'export' => 'import-products',
            'categor' => 'categories',
            'variant' => 'variants',

            // Payments & Shipping
            'payment' => 'credit-card',
            'credit' => 'credit-card',
            'shipping' => 'shipping-rates',
            'fulfillment' => 'free-shipping',

            // Orders
            'order-lifecycle' => 'lifecycle',
            'order-management' => 'orders',
            'invoice' => 'invoice',
            'refund' => 'refunds',
            'edit-order' => 'edit-orders',
            'label' => 'label-management',

            // Growth & Marketing
            'customer' => 'customers',
            'segment' => 'customers',
            'metric' => 'analytics',
            'analytics' => 'analytics',
            'cross-sell' => 'upsell',
            'upsell' => 'upsell',
            'google-analytics' => 'analytics',
            'sales-performance' => 'reports',
            'marketplace' => 'marketplace',
            'price-rule' => 'discount',
            'promotion' => 'discount',
            'discount' => 'discount',

            // UX & Design
            'loading-speed' => 'speed',
            'performance' => 'speed',
            'checkout' => 'checkout',
            'conversion' => 'checkout',
            'search-and-navigation' => 'navigation',
            'site-search' => 'search-seo',
            'accessible' => 'accessibility',
            'design' => 'design',
            'frontend' => 'design',

            // Security & Compliance
            'security' => 'security',
            'threat' => 'security',
            'legal' => 'document',
            'compliance' => 'security',
            'gdpr' => 'security',
            'pci' => 'credit-card',

            // Operations & Integration
            'report' => 'reports',
            'financial' => 'reports',
            'workflow' => 'automation',
            'automate' => 'automation',
            'erp' => 'integration',
            'integrat' => 'integration',
            'multiple-store' => 'stores',
            'operation' => 'setup',
            'api' => 'api',
            'graphql' => 'api',
            'rest' => 'api',
            'config' => 'setup',

            // Getting Started
            'store-setup' => 'setup',
            'domain' => 'setup',
            'email' => 'setup',
            'ssl' => 'security',
        ];

        // Check each mapping
        foreach ($iconMappings as $keyword => $icon) {
            if (str_contains($pathLower, $keyword)) {
                return $icon;
            }
        }

        // Default icon
        return 'document';
    }

    /**
     * Get related categories for the sidebar.
     */
    protected function getRelatedCategories(string $currentCategory): array
    {
        $allCategories = $this->navigationParser->getCategories();

        return $allCategories
            ->filter(fn($cat) => $cat["slug"] !== $currentCategory)
            ->take(3)
            ->map(function ($cat) {
                $meta = $this->getCategoryMetadata($cat["slug"]);
                return [
                    "title" => $cat["name"],
                    "color" => $meta["color"] ?? "bg-gray-100",
                    "icon" => $meta["icon"],
                    "count" => count($cat["articles"]),
                    "url" => "/merchant/" . $cat["slug"],
                ];
            })
            ->toArray();
    }
}
