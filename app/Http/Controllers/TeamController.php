<?php

namespace App\Http\Controllers;

class TeamController extends Controller
{
    /**
     * Show the team page.
     */
    public function index()
    {
        return view('team', [
            'team' => config('team.members'),
            'title' => 'Team',
            'metaTitle' => DocsController::pageTitle('Meet the Team'),
            'metaDescription' => 'Meet the passionate team behind Magento Merchant Documentation. We\'re dedicated to helping merchants succeed with clear and comprehensive guides.',
            'metaKeywords' => DocsController::DEFAULT_META_KEYWORDS,
            'canonical' => 'team',
        ]);
    }
}
