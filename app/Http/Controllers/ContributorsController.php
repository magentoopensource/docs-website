<?php

namespace App\Http\Controllers;

use App\Services\GitHubContributorsService;

class ContributorsController extends Controller
{
    public function __construct(protected GitHubContributorsService $contributors)
    {
    }

    /**
     * Show the full contributors page: podium, complete top 10, how counting works,
     * and both ways to contribute (D7/D8 — MA-DOCS-IDEAS-WIDGET.md).
     */
    public function index()
    {
        return view('contributors', [
            'title' => 'Contributors',
            'metaTitle' => DocsController::pageTitle('Contributors'),
            'metaDescription' => 'The people who write and build the Magento Open Source documentation, ranked by contributions to the docs and docs-website repositories.',
            'metaKeywords' => DocsController::DEFAULT_META_KEYWORDS,
            'canonical' => 'contributors',
            'contributors' => $this->contributors->getTopContributors(10),
        ]);
    }
}
