<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\TeamController;

// Apply rate limiting to all routes to prevent abuse
// 120 requests per minute per IP (reasonable for a documentation site)
Route::middleware('throttle:120,1')->group(function () {
    Route::get("/", [DocsController::class, "showRootPage"])->name("home");

    Route::get("/docs", [DocsController::class, "showDocsIndex"])->name("docs.index");
    Route::get("/docs/index.json", [DocsController::class, "index"]);

    Route::get("/docs/{category}/{page}", [DocsController::class, "show"])
        ->where("page", ".*")
        ->name("docs.show");

    Route::get("/docs/{category}", [DocsController::class, "showCategory"])
        ->name("docs.category");

    Route::get("team", [TeamController::class, "index"])->name("team");
});
