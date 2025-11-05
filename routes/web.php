<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\TeamController;

// Apply rate limiting to all routes to prevent abuse
// 120 requests per minute per IP (reasonable for a documentation site)
Route::middleware('throttle:120,1')->group(function () {
    Route::get("/", [DocsController::class, "showRootPage"])->name("home");

    Route::get("/merchant", [DocsController::class, "showDocsIndex"])->name("merchant.index");
    Route::get("/merchant/index.json", [DocsController::class, "index"]);

    Route::get("/merchant/{category}/{page}", [DocsController::class, "show"])
        ->where("page", ".*")
        ->name("merchant.show");

    Route::get("/merchant/{category}", [DocsController::class, "showCategory"])
        ->name("merchant.category");

    Route::get("team", [TeamController::class, "index"])->name("team");
});
