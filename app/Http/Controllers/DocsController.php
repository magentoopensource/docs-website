<?php

namespace App\Http\Controllers;

use App\Documentation;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class DocsController extends Controller
{
    public const DEFAULT_META_TITLE = "Magento 2 Merchant Documentation - Your Complete Guide";
    public const DEFAULT_META_DESCRIPTION = "Complete guide to managing your Magento 2 store. Learn how to sell products, manage orders, grow your business, and stay compliant with our comprehensive merchant documentation.";
    const DEFAULT_META_KEYWORDS = "Magento 2, Merchant Documentation, Magento Store Management, eCommerce Guide, Magento Tutorial";

    /**
     * The documentation repository.
     *
     * @var \App\Documentation
     */
    protected $docs;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Documentation  $docs
     * @return void
     */
    public function __construct(Documentation $docs)
    {
        $this->docs = $docs;
    }

    /**
     * Show the homepage with merchant documentation categories.
     *
     * @return \Illuminate\View\View
     */
    public function showRootPage()
    {
        return view("homepage", [
            "title" => "Merchant Documentation",
            "metaTitle" => self::DEFAULT_META_TITLE,
            "metaDescription" => self::DEFAULT_META_DESCRIPTION,
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "",
        ]);
    }

    /**
     * Show the documentation index JSON representation.
     *
     * @param  string  $version
     * @param  \App\Documentation  $docs
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function index($version, Documentation $docs)
    {
        $major = Str::before($version, ".");

        if (
            Str::before(array_values(Documentation::getDocVersions())[1], ".") +
                1 ===
            (int) $major
        ) {
            $version = $major = "main";
        }

        if (!$this->isVersion($version)) {
            return redirect("docs/" . DEFAULT_VERSION . "/index.json", 301);
        }

        if ($major !== "main" && $major < 9) {
            return [];
        }

        return response()->json($docs->indexArray($version));
    }

    /**
     * Show a documentation page.
     *
     * @param  string  $version
     * @param  string|null  $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($version, $page = null)
    {
        if (!$this->isVersion($version)) {
            return redirect("docs/" . DEFAULT_VERSION . "/" . $version, 301);
        }

        if (!defined("CURRENT_VERSION")) {
            define("CURRENT_VERSION", $version);
        }

        $sectionPage = $page ?: "installation-guide";
        $docPage = $this->docs->get($version, $sectionPage);

        $content = $docPage ? $docPage["content"] : null;
        $pageCustomData = $docPage ? $docPage["frontendMatter"] : [];
        $metaDescription =
            $pageCustomData["description"] ?? self::DEFAULT_META_DESCRIPTION;
        $metaKeywords =
            $pageCustomData["keywords"] ?? self::DEFAULT_META_KEYWORDS;
        $communityNote = $pageCustomData["communityNote"] ?? true;

        if (is_null($content)) {
            $otherVersions = $this->docs->versionsContainingPage($sectionPage);

            return response()->view(
                "docs",
                [
                    "title" => "Page not found",
                    "index" => $this->docs->getIndex($version),
                    "content" => view("docs-missing", [
                        "otherVersions" => $otherVersions,
                        "page" => $sectionPage,
                    ]),
                    "currentVersion" => $version,
                    "versions" => Documentation::getDocVersions(),
                    "currentSection" => $otherVersions->isEmpty()
                        ? ""
                        : "/" . $sectionPage,
                    "canonical" => null,
                ],
                404
            );
        }

        $title = (new Crawler($content))->filterXPath("//h1");

        $section = "";

        if ($this->docs->sectionExists($version, $page)) {
            $section .= "/" . $page;
        } elseif (!is_null($page)) {
            return redirect("/docs/" . $version);
        }

        $canonical = null;

        if ($this->docs->sectionExists(DEFAULT_VERSION, $sectionPage)) {
            $canonical = "docs/" . DEFAULT_VERSION . "/" . $sectionPage;
        }

        return view("docs", [
            "title" => count($title)
                ? $title->text()
                : self::DEFAULT_META_TITLE,
            "index" => $this->docs->getIndex($version),
            "content" => $content,
            "currentVersion" => $version,
            "versions" => Documentation::getDocVersions(),
            "currentSection" => $section,
            "canonical" => $canonical,
            "edit_link" => $this->docs->getEditUrl($version, $sectionPage),
            "metaTitle" => count($title)
                ? $title->text()
                : self::DEFAULT_META_TITLE,
            "metaDescription" => $metaDescription,
            "metaKeywords" => $metaKeywords,
            "communityNote" => $communityNote,
        ]);
    }

    /**
     * Show a category page with articles.
     *
     * @param  string  $category
     * @return \Illuminate\View\View
     */
    public function showCategory($category)
    {
        $categories = [
            "start-selling" => [
                "title" => "Start Selling",
                "description" =>
                    "Get your store up and running with your first products, payments, and shipping setup.",
                "color" => "bg-green-100",
                "icon" =>
                    '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                "articles" => [
                    [
                        "title" => "How to create your first simple product",
                        "description" =>
                            "Learn the basics of adding products to your Magento store, including essential fields and best practices.",
                        "url" => "/docs/main/products/simple-product",
                        "difficulty" => "Beginner",
                        "read_time" => 5,
                    ],
                    [
                        "title" => "Set up shipping rates by country or region",
                        "description" =>
                            "Configure shipping methods and rates for different geographical locations to serve customers worldwide.",
                        "url" => "/docs/main/shipping/rates",
                        "difficulty" => "Intermediate",
                        "read_time" => 8,
                    ],
                    [
                        "title" => "Enable credit card payments securely",
                        "description" =>
                            "Set up secure payment processing with popular payment gateways and ensure PCI compliance.",
                        "url" => "/docs/main/payments/credit-cards",
                        "difficulty" => "Intermediate",
                        "read_time" => 10,
                    ],
                    [
                        "title" =>
                            "Add free shipping over a minimum cart value",
                        "description" =>
                            "Create promotional shipping rules to encourage larger orders and increase customer satisfaction.",
                        "url" => "/docs/main/shipping/free-shipping",
                        "difficulty" => "Beginner",
                        "read_time" => 4,
                    ],
                    [
                        "title" => "Configure tax rules for multiple regions",
                        "description" =>
                            "Set up tax calculations for different countries and states to ensure compliance with local tax laws.",
                        "url" => "/docs/main/tax/rules",
                        "difficulty" => "Advanced",
                        "read_time" => 12,
                    ],
                    [
                        "title" =>
                            "Sell digital products or downloadable files",
                        "description" =>
                            "Learn how to set up downloadable products, manage file security, and handle digital delivery.",
                        "url" => "/docs/main/products/downloadable",
                        "difficulty" => "Intermediate",
                        "read_time" => 7,
                    ],
                ],
            ],
        ];

        if (!isset($categories[$category])) {
            abort(404);
        }

        $categoryData = $categories[$category];

        $relatedCategories = [
            [
                "title" => "Manage Catalog",
                "color" => "bg-yellow-100",
                "icon" =>
                    '<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
                "count" => 10,
                "url" => "/category/manage-catalog",
            ],
            [
                "title" => "Handle Orders",
                "color" => "bg-blue-100",
                "icon" =>
                    '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
                "count" => 8,
                "url" => "/category/handle-orders",
            ],
            [
                "title" => "Grow Store",
                "color" => "bg-purple-100",
                "icon" =>
                    '<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                "count" => 12,
                "url" => "/category/grow-store",
            ],
        ];

        return view("category", [
            "title" => $categoryData["title"] . " - Merchant Documentation",
            "category_title" => $categoryData["title"],
            "category_description" => $categoryData["description"],
            "category_color" => $categoryData["color"],
            "category_icon" => $categoryData["icon"],
            "articles" => $categoryData["articles"],
            "related_categories" => $relatedCategories,
            "quick_start_url" => "/docs/main/getting-started",
            "metaTitle" =>
                $categoryData["title"] . " - " . self::DEFAULT_META_TITLE,
            "metaDescription" => $categoryData["description"],
            "metaKeywords" => self::DEFAULT_META_KEYWORDS,
            "canonical" => "category/" . $category,
        ]);
    }

    /**
     * Determine if the given URL segment is a valid version.
     *
     * @param  string  $version
     * @return bool
     */
    protected function isVersion($version)
    {
        return array_key_exists($version, Documentation::getDocVersions());
    }
}
