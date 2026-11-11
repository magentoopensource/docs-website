<?php

/*
 * Permanent (301) redirects, checked by App\Services\Redirects before a request is allowed to 404.
 *
 * Keys are request paths without query string. Values are the destination (a path, or an absolute URL).
 * 'exact' is a straight lookup; 'patterns' are regular expressions with a replacement, tried in order.
 *
 * This is data, not code: the slug clean-up (SEO plan D2) adds one line per renamed guide here, for example
 *   '/merchant/getting-started/howto-how-to-configure-ssl-and-security' => '/merchant/getting-started/configure-ssl-and-security',
 *
 * IMPORTANT: files under public/ (the /developer and /the-core pages) are served by the web server before
 * Laravel runs, so a redirect for one of those paths only fires once its file has been deleted. The
 * index.html forms below are listed for the day they are removed; while the files exist the web server
 * answers them and the pages' rel=canonical points at the folder URL instead.
 */
return [
    'exact' => [
        // Legacy developer pages replaced by their newer versions (files removed from public/developer).
        '/developer/catalog-overview.html' => '/developer/module-catalog-overview.html',
        '/developer/plugin-system-deep-dive.html' => '/developer/guide-tutorial-plugin-system-deep-dive.html',
        '/developer/contributors.html' => '/contributors',

        // Folder URL is the canonical form of a hub.
        '/developer/index.html' => '/developer/',
        '/the-core/index.html' => '/the-core/',
    ],

    'patterns' => [
        // Every The Core index page: /the-core/<...>/index.html -> /the-core/<...>/
        '#^(/the-core/(?:.+/)?)index\.html$#' => '$1',
    ],
];
