/*
 * Contributor + "Edit on GitHub" widget for the developer documentation.
 *
 * Reads a locally-baked /developer/contributors.json (written by the generator
 * at build time from the magentoopensource/docs GitHub API) and injects a
 * "Top Contributors" widget just above the page footer — mirroring the merchant
 * docs' server-rendered widget (contributors LEFT, edit link RIGHT).
 *
 * The per-page edit URL comes from <meta name="edit-url"> (set by the generator).
 * Content pages have it; the bespoke landing does not (so it shows contributors only).
 * Reading the local JSON avoids per-visitor GitHub API calls (rate-limited 60/hr).
 *
 * Included on every dev-docs page as:  <script src="includes/contributors.js"></script>
 */
(function () {
    // Dev-docs scoped: the commit history of the developer/ folder.
    var REPO_URL = 'https://github.com/magentoopensource/docs/commits/main/developer';
    var RANK = [
        'bg-yellow-400 text-yellow-900 ring-2 ring-yellow-500',
        'bg-gray-300 text-gray-700 ring-2 ring-gray-400',
        'bg-orange-300 text-orange-800 ring-2 ring-orange-400'
    ];

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function contributorsSection(contribs) {
        if (!Array.isArray(contribs) || contribs.length === 0) { return ''; }
        var cards = contribs.slice(0, 3).map(function (c, i) {
            var count = Number(c.contributions || 0).toLocaleString();
            return '' +
                '<a href="' + esc(c.html_url) + '" target="_blank" rel="noopener noreferrer" ' +
                'title="' + esc(c.login) + ' — ' + count + ' contributions" ' +
                'class="group relative flex items-center gap-3 px-4 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 hover:border-gray-300 transition-all duration-150 no-underline">' +
                    '<img src="' + esc(c.avatar_url) + '" alt="' + esc(c.login) + '" class="w-8 h-8 ring-2 ring-white" loading="lazy" width="32" height="32" />' +
                    '<span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">' + esc(c.login) + '</span>' +
                    '<span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 font-medium">' + count + '</span>' +
                    '<span class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 text-[10px] font-bold shadow-sm ' + (RANK[i] || RANK[2]) + '">' + (i + 1) + '</span>' +
                '</a>';
        }).join('');
        return '' +
            '<div class="flex-1" role="region" aria-label="Top Contributors">' +
                '<h2 class="text-sm font-semibold uppercase tracking-wider text-charcoal-300 mb-4">Top Contributors</h2>' +
                '<div class="flex flex-wrap items-center gap-3">' + cards + '</div>' +
                '<div class="mt-4">' +
                    '<a href="' + REPO_URL + '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors no-underline">' +
                        'View all contributors' +
                        '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>' +
                    '</a>' +
                '</div>' +
            '</div>';
    }

    function editSection(editUrl) {
        if (!editUrl) { return ''; }
        return '' +
            '<div class="flex-shrink-0 sm:text-right">' +
                '<a href="' + esc(editUrl) + '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-150 no-underline">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
                    'Edit this page on GitHub' +
                '</a>' +
            '</div>';
    }

    function widgetHtml(contribs, editUrl) {
        var left = contributorsSection(contribs);
        var right = editSection(editUrl);
        if (!left && !right) { return ''; }
        // No page-width wrapper: the widget fills its host container (the #dev-contributors
        // placeholder inside the article column on content pages), matching the merchant docs.
        return '' +
            '<div class="mt-16 pt-8 border-t border-gray-200">' +
                '<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-8">' +
                    left + right +
                '</div>' +
            '</div>';
    }

    function inject(html) {
        if (!html) { return; }
        var container = document.createElement('div');
        container.innerHTML = html;
        var node = container.firstChild;
        // Preferred: the #dev-contributors placeholder inside the article/content column.
        var target = document.getElementById('dev-contributors');
        if (target) { target.appendChild(node); return; }
        // Fallback: just above the footer.
        var footer = document.querySelector('footer');
        if (footer && footer.parentNode) {
            footer.parentNode.insertBefore(node, footer);
        } else {
            document.body.appendChild(node);
        }
    }

    function init() {
        var meta = document.querySelector('meta[name="edit-url"]');
        var editUrl = meta ? meta.getAttribute('content') : '';
        fetch('contributors.json', { cache: 'no-cache' })
            .then(function (r) { return r.ok ? r.json() : []; })
            .then(function (data) { inject(widgetHtml(data, editUrl)); })
            .catch(function () { inject(widgetHtml([], editUrl)); }); // still show Edit if data unavailable
    }

    if (document.readyState !== 'loading') { init(); }
    else { document.addEventListener('DOMContentLoaded', init); }
})();
