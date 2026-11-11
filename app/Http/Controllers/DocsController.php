<?php

namespace App\Http\Controllers;

use App\Documentation;
use App\Services\ContentDates;
use App\Services\NavigationParser;
use App\Services\Redirects;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class DocsController extends Controller
{
    public const DEFAULT_META_TITLE = "Magento Open Source Merchant Documentation";
    public const DEFAULT_META_DESCRIPTION = "Complete guide to running your Magento Open Source store: sell products, manage orders, grow your business and stay compliant.";
    const DEFAULT_META_KEYWORDS = "Magento Open Source, Merchant Documentation, Magento Store Management, eCommerce Guide, Magento Tutorial";

    /** Brand appended to page titles when it fits. */
    public const TITLE_BRAND = "Magento Open Source";

    /** Longest title search engines show in full. */
    public const TITLE_MAX_LENGTH = 60;

    /**
     * Full <title> text: the page name plus " | Magento Open Source" when the result fits in 60 characters,
     * otherwise the page name alone (a long guide name beats a truncated brand). Titles that already name
     * the brand are returned as they are, so hubs and the home page never repeat it.
     */
    public static function pageTitle(?string $title): string
    {
        $title = trim((string) $title);

        if ($title === "") {
            return self::DEFAULT_META_TITLE;
        }

        if (stripos($title, self::TITLE_BRAND) !== false) {
            return $title;
        }

        $branded = $title . " | " . self::TITLE_BRAND;

        return mb_strlen($branded) <= self::TITLE_MAX_LENGTH ? $branded : $title;
    }

    public function __construct(
        protected Documentation $docs,
        protected NavigationParser $navigationParser,
        protected ContentDates $contentDates
    ) {
    }

    /**
     * Show the main landing page with links to merchant and developer docs.
     */
    public function showRootPage()
    {
        // /index.php answers with the home page too (and its canonical becomes /index.php): send it to /.
        if (str_starts_with(request()->getRequestUri(), "/index.php")) {
            return response("", 301)->header("Location", "/");
        }

        return view("landing");
    }

    /**
     * Show the merchant homepage with documentation categories.
     */
    public function showMerchantHomepage()
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
            "title" => self::DEFAULT_META_TITLE,
            "metaTitle" => self::DEFAULT_META_TITLE,
            "metaDescription" => self::DEFAULT_META_DESCRIPTION,
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "merchant",
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
            "metaTitle" => self::pageTitle("Documentation Index"),
            "metaDescription" => self::DEFAULT_META_DESCRIPTION,
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "merchant/index",
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

        // Retired URLs (config/redirects.php) answer 301 to their new home before anything can 404.
        if ($to = Redirects::resolve("/merchant/" . $sectionPage)) {
            return redirect($to, 301);
        }

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
                    "metaTitle" => self::pageTitle("Page not found"),
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

        // Last updated: git commit date of the source file, shown by the H1 and published as dateModified.
        $lastUpdated = $this->contentDates->forPath($sectionPage);
        if ($lastUpdated) {
            $content = ContentDates::insertAfterHeading((string) $content, $lastUpdated);
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
            "lastUpdated" => $lastUpdated,
            "ogImage" => $this->guideImage($sectionPage),
        ]);
    }

    /**
     * Path of the pre-generated 1200x630 social image for a guide (bin/og/generate_og_images.py writes them to
     * public/img/og/), or null when the guide has none and the shared image applies.
     */
    protected function guideImage(string $guidePath): ?string
    {
        $relative = "/img/og/" . $guidePath . ".png";

        return is_file(public_path($relative)) ? $relative : null;
    }

    /**
     * Show a category page with articles.
     */
    public function showCategory(string $category)
    {
        if ($to = Redirects::resolve("/merchant/" . $category)) {
            return redirect($to, 301);
        }

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
                "read_time" => $frontMatter["read_time"] ?? $this->estimateReadTime($docPage["content"] ?? ""),
            ];
        });

        // Get category metadata (icons, colors, etc.) for potential future use
        $categoryMeta = $this->getCategoryMetadata($category);

        // The hub intro reads the category description; without this the "Learn more about X" stub always rendered.
        if (!empty($categoryMeta["description"])) {
            $categoryData["description"] = $categoryMeta["description"];
        }

        // 100-150 word introduction shown under the header: what the section covers and who it is for.
        $categoryData["intro"] = $categoryMeta["intro"] ?? "";

        // Use unified category view for all categories
        return view("category.show", [
            "title" => $categoryData["name"],
            "metaTitle" => self::pageTitle($categoryData["name"]),
            "metaDescription" =>
                $categoryMeta["description"] ?:
                "Learn about " . $categoryData["name"],
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "merchant/" . $category,
            "category" => $categoryData,
            "articles" => $articlesWithMetadata,
        ]);
    }

    /**
     * Reading time in whole minutes at 200 words per minute, never less than one.
     */
    protected function estimateReadTime(string $html): int
    {
        $text = strip_tags(preg_replace('#<(script|style)\b.*?</\1>#is', " ", $html) ?? $html);
        $words = preg_match_all('/\S+/u', $text);

        return max(1, (int) ceil(($words ?: 0) / 200));
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
                    "Set up a new Magento Open Source store: a complete setup walkthrough, domain and email configuration, and SSL and baseline security, in a sensible order.",
                "intro" =>
                    "Getting Started is the first section of the merchant documentation. It is for store owners and new administrators who have a Magento Open Source installation running and need to turn it into a store that can take orders. The section has three guides. Complete Store Setup from Scratch is a tutorial that takes you from first Admin login to a successful test order, covering store information, taxes, shipping, payments, the first products, a theme and launch checks. How to Configure Domain and Email sets up a canonical HTTPS domain and authenticated email so order confirmations reach customers. How to Configure SSL and Security covers HTTPS, secure cookies, two-factor authentication and reCAPTCHA. Start with the tutorial, then use the how-to guides to harden the result.",
            ],
            "start-selling" => [
                "color" => "bg-green-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                "description" =>
                    "Add your first products, accept payments and set up shipping in Magento Open Source, with step-by-step guides for merchants who are ready to take orders.",
                "intro" =>
                    "Start Selling covers the three things a store needs before it can take its first real order: products, a way to be paid, and a way to deliver. It is written for merchants and administrators working in the Magento Open Source Admin. Creating Your First Products is a tutorial that builds a simple product and a configurable product with variations. How to Set Up Payment Processing explains how to configure payment gateways, test them, and fix failures such as orders that stay pending after payment. How to Set Up Shipping and Fulfillment covers shipping methods, rates, free shipping thresholds and fulfillment, and how to fix the checkout message that no quotes are available. Read them in that order if you are launching a new store, or jump to the one you need.",
            ],
            "manage-catalog" => [
                "color" => "bg-yellow-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
                "description" =>
                    "Organize and maintain your Magento Open Source catalog: categories, product types and attributes, images, inventory, bulk import and export, and search.",
                "intro" =>
                    "Manage Catalog is for merchants and catalog managers who maintain the products in a Magento Open Source store. It has seven guides. Product Types and Attributes Reference explains how to choose a product type and configure attributes. Getting Started with Categories and Getting Started with Product Import and Export are tutorials for organizing the category tree and for bulk-updating products from CSV files. How to Optimize Product Images covers uploading, sizing and caching images. How to Set Up Inventory Tracking and Understanding Inventory Management cover stock levels, low-stock alerts, backorders, and how Multi-Source Inventory sources and stocks work. How to Optimize Products for Search covers search-friendly URLs, meta data and content, and what to check when a product does not appear in search.",
            ],
            "handle-orders" => [
                "color" => "bg-blue-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
                "description" =>
                    "Process orders in Magento Open Source from placement to delivery: the order lifecycle, a complete order workflow, and creating and managing invoices.",
                "intro" =>
                    "Handle Orders is for the people who process orders day to day: store owners, customer service staff and fulfillment teams working in the Magento Open Source Admin. It has three guides that work together. Understanding the Order Lifecycle explains each stage of an order, from cart and payment through invoicing and shipment to refunds, and when an order counts as complete. Complete Order Management Workflow is a tutorial that takes a test order through review, invoice, shipment, cancellation or hold, and credit memo. Getting Started: Create and Manage Invoices shows how to create full and partial invoices, capture payment online or record it offline, and email, print or export invoices. Read the lifecycle guide first if the terminology is new to you.",
            ],
            "grow-store" => [
                "color" => "bg-purple-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                "description" =>
                    "Grow sales from a Magento Open Source store: promotions and price rules, cross-selling and upselling, Google Analytics 4, sales reports and marketplaces.",
                "intro" =>
                    "Grow Store is for merchants who already have a working store and want to sell more through it. The section has eight guides. Promotions and Discounts Reference and the Shopping Cart Price Rules tutorial cover Cart Price Rules, Catalog Price Rules, coupons and free shipping. How to Set Up Cross-Selling and Upselling configures related products, up-sells and cross-sells. How to Set Up Google Analytics 4 adds enhanced ecommerce tracking, and How to Track Sales Performance uses the built-in Sales, Products and Customers reports. Understanding Key E-commerce Metrics explains which KPIs to track, and Understanding Customer Segmentation and Targeting explains Customer Groups and, in Adobe Commerce, Customer Segments. How to Connect with Online Marketplaces covers syncing listings, orders and inventory with Amazon, eBay and other channels.",
            ],
            "improve-ux" => [
                "color" => "bg-pink-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>',
                "description" =>
                    "Make a Magento Open Source storefront faster and easier to use: site speed, checkout, search and navigation, accessibility and a professional frontend design.",
                "intro" =>
                    "Improve UX is for merchants and store managers who want shoppers to find products and complete checkout with less effort. It has five guides. How to Improve Site Loading Speed covers Full Page Cache, a CDN and related performance settings. How to Optimize Checkout for Higher Conversions covers guest checkout, fewer form fields, express wallets and Instant Purchase. How to Set Up Site Search and Navigation covers search, layered navigation filters and menus. How to Make Your Store Accessible moves a storefront toward WCAG 2.1 AA using configuration and content changes. Designing a Professional Store Frontend is a no-code tutorial for the theme, branding, homepage and layouts. Each guide also covers the problems merchants most often run into.",
            ],
            "stay-compliant" => [
                "color" => "bg-red-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                "description" =>
                    "Protect a Magento Open Source store and meet legal duties: security threats, legal pages, GDPR, PCI DSS, security settings and a full compliance setup.",
                "intro" =>
                    "Stay Compliant is for merchants and administrators who are responsible for the security of a Magento Open Source store and for meeting its privacy and payment obligations. It has six guides. Understanding E-commerce Security Threats explains the main threats and what Magento does about them. How to Add Legal Pages and Compliance covers privacy policy and terms pages, terms acceptance at checkout and cookie consent. How to Implement GDPR Compliance covers privacy controls, cookie consent and data subject requests. How to Maintain PCI Compliance covers reducing PCI scope, HTTPS, Admin hardening and required scans. The reference lists the security settings, and the tutorial walks through a complete security and compliance setup. The guides are not legal advice.",
            ],
            "reports-and-analytics" => [
                "color" => "bg-gray-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                "description" =>
                    "Use the reports built into Magento Open Source and produce financial reports for accounting, tax filing and reconciliation with your payment provider.",
                "intro" =>
                    "Reports and Analytics is for merchants, finance staff and accountants who need numbers out of a Magento Open Source store. It has two guides. Built-in Reports and Analytics Reference is a catalog of the Admin reports for sales, product demand, customer growth, reviews and on-site search. It also explains how to export a report as CSV or Excel XML, and how to retrieve orders, invoices and customers through the REST and GraphQL APIs when you want to do your own aggregation. How to Generate Financial Reports shows how to produce exportable reports for tax filing and accounting reconciliation, and how to fix common mismatches such as totals that differ from the payment gateway, time zone differences and stale statistics. For sales performance reporting, see the Grow Store section.",
            ],
            "support-and-resources" => [
                "color" => "bg-gray-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                "description" =>
                    "Run a Magento Open Source store at scale: workflow automation, ERP integration, multiple stores, store operations, advanced configuration and API references.",
                "intro" =>
                    "Support and Resources is for merchants, operations staff and the developers who help them, once a Magento Open Source store is running and needs to run more efficiently. How to Automate Business Workflows covers automatic invoicing, cancelling stale pending-payment orders, inventory alerts and email deliverability, and how to fix cron problems. How to Integrate with ERP Systems covers connecting Magento to an ERP for inventory and order sync. How to Manage Multiple Stores explains running several brands, regions or catalogs from one installation. Streamlining Store Operations is a tutorial on reducing order handling time. Advanced Configuration Options Reference lists caching and performance settings, and REST and GraphQL API Reference shows how to authenticate and run common operations.",
            ],
        ];

        return $metadata[$category] ?? [
            "color" => "bg-gray-100",
            "icon" =>
                '<svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            "description" => "",
            "intro" => "",
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
