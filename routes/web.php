<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContributorsController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WebhookController;

// Apply rate limiting to all routes to prevent abuse
// 120 requests per minute per IP (reasonable for a documentation site).
// "docs" group: no session or cookies. Public Cache-Control for 5 minutes, matching the app-level page cache.
Route::middleware(['docs', 'throttle:120,1', 'cache.headers:public;max_age=300'])->group(function () {
    Route::get("/", [DocsController::class, "showRootPage"])->name("home");

    Route::get("/merchant", [DocsController::class, "showMerchantHomepage"])->name("merchant.home");
    Route::get("/merchant/index", [DocsController::class, "showDocsIndex"])->name("merchant.index");
    Route::get("/merchant/index.json", [DocsController::class, "index"]);

    Route::get("/merchant/{category}/{page}", [DocsController::class, "show"])
        ->where("page", ".*")
        ->name("merchant.show");

    Route::get("/merchant/{category}", [DocsController::class, "showCategory"])
        ->name("merchant.category");

    Route::get("team", [TeamController::class, "index"])->name("team");

    Route::get("contributors", [ContributorsController::class, "index"])->name("contributors");

    // Built from the merchant navigation and the developer files; robots.txt points here.
    Route::get("sitemap.xml", [SitemapController::class, "index"])->name("sitemap");
});

// GitHub webhook for docs sync (excluded from throttling)
Route::post('/webhook/sync-docs', [WebhookController::class, 'syncDocs'])
    ->middleware(['web', 'throttle:10,1'])
    ->name('webhook.sync-docs');
