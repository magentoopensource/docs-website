<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocsController;

Route::get("/", [DocsController::class, "showRootPage"])->name("home");

Route::get("/docs", [DocsController::class, "showDocsIndex"])->name("docs.index");
Route::get("/docs/index.json", [DocsController::class, "index"]);

Route::get("/docs/{category}/{page}", [DocsController::class, "show"])
    ->where("page", ".*")
    ->name("docs.show");

Route::get("/docs/{category}", [DocsController::class, "showCategory"])
    ->name("docs.category");

Route::get("team", function () {
    return view("team", [
        "team" => [
            [
                "name" => "Sarah Chen",
                "role" => "Lead Technical Writer",
                "location" => "San Francisco, CA",
                "bio" => "Specializes in creating clear, actionable documentation for merchants. 10+ years in eCommerce.",
                "github_username" => "torvalds",
                "twitter_username" => "github",
                "linkedin_username" => "in/example",
            ],
            [
                "name" => "Marcus Rodriguez",
                "role" => "Documentation Engineer",
                "location" => "Austin, TX",
                "bio" => "Focuses on technical accuracy and keeping docs up-to-date with the latest Magento features.",
                "github_username" => "github",
                "twitter_username" => "github",
            ],
            [
                "name" => "Aisha Patel",
                "role" => "Community Manager",
                "location" => "London, UK",
                "bio" => "Connects with merchants daily to understand their documentation needs and pain points.",
                "github_username" => "github",
                "linkedin_username" => "in/example",
            ],
            [
                "name" => "David Kim",
                "role" => "Senior Content Strategist",
                "location" => "Toronto, Canada",
                "bio" => "Ensures documentation structure and flow meet merchant needs at every skill level.",
                "github_username" => "github",
                "twitter_username" => "github",
            ],
            [
                "name" => "Elena Volkov",
                "role" => "Technical Reviewer",
                "location" => "Berlin, Germany",
                "bio" => "Reviews all documentation for technical accuracy and best practices before publication.",
                "github_username" => "github",
            ],
            [
                "name" => "James O'Brien",
                "role" => "Developer Advocate",
                "location" => "Dublin, Ireland",
                "bio" => "Bridges the gap between developers and merchants, ensuring docs serve both audiences.",
                "github_username" => "github",
                "twitter_username" => "github",
                "linkedin_username" => "in/example",
            ],
        ],
        "title" => "Team - Magento Merchant Documentation",
        "metaTitle" => "Meet the Team - " . DocsController::DEFAULT_META_TITLE,
        "metaDescription" => "Meet the passionate team behind Magento Merchant Documentation. We're dedicated to helping merchants succeed with clear and comprehensive guides.",
        "metaKeywords" => DocsController::DEFAULT_META_KEYWORDS,
        "canonical" => "team",
    ]);
})->name("team");
