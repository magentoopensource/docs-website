/*
 * Contributor box for the developer documentation.
 *
 * One box under every dev-docs page: a podium of the top 3 contributors, a "places"
 * meter for the top 10 (with the full list behind a toggle), and two ways to earn a
 * place: "Suggest an idea" (GitHub issue form, or email) and "Improve this page"
 * (the page's GitHub edit link).
 *
 * The same render path also produces the standalone contributors page. A page that
 * carries <div id="dev-contributors-full"> gets the FULL variant of the same box:
 * a larger podium, the top 10 open with no toggle, a "how this is counted" note, and
 * an edit card pointed at the repository root (that page has no upstream source file).
 * Every other page gets the compact box: heading and the two ways to contribute.
 *
 * Article pages also get a compact "Suggest an idea" button injected into the page's
 * own sticky "On this page" panel, so the call to action is reachable without scrolling
 * to the bottom. It is placed inside that panel on purpose: it then inherits the panel's
 * own visibility (`hidden xl:block`) instead of hard-coding a breakpoint here.
 *
 * Data: a locally-baked contributors.json (written by the generator at build time),
 * so visitors never hit the rate-limited GitHub API. The per-page edit URL comes from
 * <meta name="edit-url">; pages without it (the landing) hide the "Improve this page" card.
 *
 * Included on every dev-docs page as:  <script src="includes/contributors.js"></script>
 */
(function () {
    // Issue forms (idea-content.yml, idea-website.yml) live in the docs content repo.
    var IDEA_REPO = 'magentoopensource/docs';

    /*
     * Shared contract (MA-DOCS-IDEAS-WIDGET.md §6): template names, field id `page_url`,
     * title prefixes and mail subject format must stay identical to the issue forms and the
     * merchant-docs Blade component.
     */
    var IDEA_ISSUE_URL = 'https://github.com/' + IDEA_REPO + '/issues/new';
    // Repository root. Used by the contributors page, which has no upstream source file of
    // its own, so there is nothing for <meta name="edit-url"> to point at. Derived from the
    // one repo constant above rather than hard-coding a second repository name.
    var REPO_ROOT_URL = 'https://github.com/' + IDEA_REPO;
    var IDEA_MAIL_PARTS = ['documentation-committee', 'magentoassociation.org']; // Association docs committee group (D1)
    var IDEA_TITLE_SUFFIX = ' — Magento 2 Developer Documentation';
    var IDEA_TYPES = {
        content: { template: 'idea-content.yml', titlePrefix: '[Docs topic] ', mailLabel: 'Topic' },
        website: { template: 'idea-website.yml', titlePrefix: '[Website] ', mailLabel: 'Website' }
    };
    var PLACES = 10;
    // A page carrying this placeholder is the contributors page: render the full variant there.
    var FULL_TARGET_ID = 'dev-contributors-full';
    // A hub page (developer home, section indexes) carries this placeholder: render the compact block there.
    var HUB_TARGET_ID = 'dev-contributor-box';
    // Breathing room left under a capped sticky panel so the last control is not flush to the edge.
    var STICKY_GAP = 24;
    var LINE = 'border-[#e4e2e0]';
    var TINT = 'bg-[#f5f3f1]';
    // Gold / silver / bronze rank badges (tokens from the page's Tailwind config).
    var RANK = ['bg-yellow-400 text-yellow-900', 'bg-charcoal-100 text-charcoal-400', 'bg-orange-300 text-orange-800'];

    var GITHUB_MARK = '<svg class="w-4 h-4 flex-none" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>';
    var EDIT_ICON = '<svg class="w-4 h-4 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
    var INFO_ICON = '<svg class="inline-block w-[13px] h-[13px] mr-1 align-[-2px]" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8zm6.5-.25A.75.75 0 017.25 7h1a.75.75 0 01.75.75v2.75h.25a.75.75 0 010 1.5h-2a.75.75 0 010-1.5h.25v-2h-.25a.75.75 0 01-.75-.75zM8 6a1 1 0 100-2 1 1 0 000 2z"/></svg>';
    var CHEVRON = '<svg data-cbox-chevron class="w-3.5 h-3.5 group-aria-expanded:rotate-180 transition-transform duration-200 motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
    var FOCUS = 'focus:outline-none focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-offset-2 focus-visible:outline-orange';

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    // Only https URLs reach an href/src (the JSON and meta are ours, but never trust a scheme).
    function safeUrl(u) {
        var s = String(u == null ? '' : u);
        return /^https:\/\//i.test(s) ? s : '';
    }

    function pad2(n) { return n < 10 ? '0' + n : String(n); }

    function normalise(data) {
        if (!Array.isArray(data)) { return []; }
        var list = data.filter(function (c) {
            return c && c.login && safeUrl(c.html_url) && safeUrl(c.avatar_url);
        }).map(function (c, i) {
            return { login: String(c.login), html_url: c.html_url, avatar_url: c.avatar_url, contributions: Number(c.contributions || 0), i: i };
        });
        // Highest first; ties keep their baked order.
        list.sort(function (a, b) { return (b.contributions - a.contributions) || (a.i - b.i); });
        return list.slice(0, PLACES);
    }

    /* ---------- Idea links (built in the browser; no form, no endpoint) ---------- */

    function ideaPageTitle() {
        var t = String(document.title || '');
        var i = t.lastIndexOf(IDEA_TITLE_SUFFIX);
        if (i !== -1 && i === t.length - IDEA_TITLE_SUFFIX.length) { t = t.slice(0, i); }
        return t.replace(/^\s+|\s+$/g, '');
    }

    function ideaPageUrl() {
        return String(window.location.href).split('#')[0];
    }

    function ideaGithubUrl(type) {
        var cfg = IDEA_TYPES[type] || IDEA_TYPES.content;
        // Website ideas are about this page, so the title carries it; topic ideas start blank.
        var title = type === 'website' ? cfg.titlePrefix + ideaPageTitle() : cfg.titlePrefix;
        return IDEA_ISSUE_URL +
            '?template=' + encodeURIComponent(cfg.template) +
            '&title=' + encodeURIComponent(title) +
            '&page_url=' + encodeURIComponent(ideaPageUrl());
    }

    function ideaMailUrl(type) {
        var website = type === 'website';
        var cfg = website ? IDEA_TYPES.website : IDEA_TYPES.content;
        var subject = '[Docs idea – ' + cfg.mailLabel + '] ' + ideaPageTitle();
        var body = 'Page: ' + ideaPageUrl() + '\n\n' +
            (website
                ? 'What should we improve?\n\n\nWhy would it help?\n\n'
                : 'What topic is missing?\n\n\nWho is it for? (merchant / developer / both)\n\n') +
            '\n—\nSent from the docs “Suggest an idea” widget';
        return 'mailto:' + IDEA_MAIL_PARTS.join('@') +
            '?subject=' + encodeURIComponent(subject) +
            '&body=' + encodeURIComponent(body);
    }

    /* ---------- Head: kicker, heading, podium ---------- */

    function podiumStep(c, rank, full) {
        var lg = rank === 1;
        var size = full
            ? (lg ? 'w-[104px] h-[104px]' : 'w-[84px] h-[84px]')
            : (lg ? 'w-[60px] h-[60px]' : 'w-[50px] h-[50px]');
        var badge = full ? 'w-[24px] h-[24px] text-[12px]' : 'w-[19px] h-[19px] text-[10px]';
        var name = full ? 'max-w-[112px] text-sm' : 'max-w-[96px] text-xs';
        var count = full ? 'text-[13px]' : 'text-[11px]';
        return '' +
            '<a data-cbox-step="' + rank + '" href="' + esc(c.html_url) + '" target="_blank" rel="noopener noreferrer" ' +
            'class="group grid justify-items-center gap-1.5 text-charcoal no-underline ' + FOCUS + '">' +
                '<span class="relative block ' + size + ' border-2 border-white outline outline-1 outline-[#e4e2e0] bg-charcoal-100">' +
                    '<img src="' + esc(c.avatar_url) + '" alt="" width="104" height="104" loading="lazy" class="block w-full h-full object-cover" />' +
                    '<span class="absolute -top-2 -right-2 grid place-items-center ' + badge + ' font-extrabold ' + RANK[rank - 1] + '" aria-hidden="true">' + rank + '</span>' +
                '</span>' +
                '<span class="' + name + ' truncate font-semibold group-hover:underline group-hover:decoration-orange group-hover:underline-offset-[3px]">' +
                    '<span class="sr-only">Rank ' + rank + ', </span>' + esc(c.login) +
                '</span>' +
                '<span class="font-mono ' + count + ' font-medium tabular-nums text-orange-700">' +
                    esc(c.contributions.toLocaleString()) + '<span class="sr-only"> contributions (opens GitHub profile in a new tab)</span>' +
                '</span>' +
            '</a>';
    }

    function podium(list, full) {
        if (!list.length) { return ''; }
        // Visual order: #2 left, #1 middle (larger), #3 right.
        var steps = [[list[1], 2], [list[0], 1], [list[2], 3]].filter(function (p) { return p[0]; })
            .map(function (p) { return podiumStep(p[0], p[1], full); }).join('');
        return '<div data-cbox-podium class="flex items-end ' + (full ? 'gap-3 sm:gap-4' : 'gap-2') + '" role="group" aria-label="Top 3 contributors">' + steps + '</div>';
    }

    function head(list, full) {
        return '' +
            '<div class="grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_auto] gap-x-7 gap-y-[18px] items-start sm:items-end px-6 ' + (full ? 'pt-7 pb-6' : 'pt-6 pb-5') + ' border-b ' + LINE + '">' +
                '<div>' +
                    '<div class="inline-flex items-center gap-2 mb-2.5 font-mono text-[11px] font-medium leading-none tracking-[0.12em] uppercase text-orange-700">' +
                        '<i class="inline-block w-2.5 h-2.5 bg-orange" aria-hidden="true"></i>Written by the community' +
                    '</div>' +
                    '<h2 id="dev-cbox-title" class="m-0 mb-1.5 ' + (full ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl') + ' leading-tight font-extrabold tracking-[-0.015em] text-charcoal [text-wrap:balance]">Help build the Magento docs, and get your name on them</h2>' +
                    (full ? '<p class="m-0 max-w-[52ch] text-sm text-charcoal-300">Every merged edit counts toward the top 10 contributors. Ideas shape what we write next.</p>' : '') +
                '</div>' +
                // The podium, meter and top 10 live on the contributors page only (Carl, 2026-10-04).
                (full ? podium(list, full) : '') +
            '</div>';
    }

    /* ---------- Places meter + top 10 ---------- */

    function places(list, full) {
        var taken = list.length;
        var open = PLACES - taken;
        var cells = '';
        for (var i = 0; i < PLACES; i++) {
            var cls = i < taken ? 'bg-charcoal border border-charcoal'
                : (i === taken ? 'bg-orange-50 border-2 border-orange' : 'bg-white border border-dashed border-charcoal-200');
            cells += '<span data-cbox-cell="' + (i < taken ? 'on' : (i === taken ? 'next' : 'open')) + '" class="h-4 ' + cls + '"></span>';
        }
        var text = '<b class="font-bold">' + taken + ' of ' + PLACES + '</b> places taken.' +
            (open > 0
                ? ' <em class="not-italic font-semibold text-orange-700">' + open + ' open.</em> Place #' + (taken + 1) + ' is yours with 1 contribution.'
                : '');
        // The full page shows the list outright, so it has no toggle to offer.
        var toggle = full ? '' : '' +
            '<button data-cbox-toggle type="button" aria-expanded="false" aria-controls="dev-cbox-top10" ' +
            'class="inline-flex items-center gap-1.5 bg-transparent border-0 py-1 px-0 cursor-pointer text-sm font-semibold text-charcoal underline decoration-orange decoration-2 underline-offset-4 group ' + FOCUS + '">' +
                '<span data-cbox-toggle-label>See the top 10</span>' + CHEVRON +
            '</button>';
        return '' +
            '<div class="flex flex-wrap items-center justify-between gap-x-5 gap-y-2.5 px-6 py-3.5 ' + TINT + ' border-b ' + LINE + '">' +
                '<div class="flex flex-wrap items-center gap-3">' +
                    '<div class="grid grid-cols-[repeat(10,16px)] gap-1" aria-hidden="true">' + cells + '</div>' +
                    '<span data-cbox-meter-text class="text-sm text-charcoal">' + text + '</span>' +
                '</div>' +
                toggle +
            '</div>';
    }

    function topRow(c, pos, taken) {
        var num = '<span class="font-mono text-xs font-medium tabular-nums text-charcoal-300">' + pad2(pos) + '</span>';
        var base = 'grid grid-cols-[26px_30px_minmax(0,1fr)_auto] gap-2.5 items-center py-2 border-b ' + LINE + ' text-sm break-inside-avoid';
        if (c) {
            return '' +
                '<li data-cbox-row="' + pos + '" class="' + base + '">' + num +
                    '<span class="block w-[30px] h-[30px] border border-white outline outline-1 outline-[#e4e2e0] bg-charcoal-100"><img src="' + esc(c.avatar_url) + '" alt="" width="30" height="30" loading="lazy" class="block w-full h-full object-cover" /></span>' +
                    '<span class="truncate font-semibold text-charcoal">' + esc(c.login) + '</span>' +
                    '<span class="font-mono text-xs font-medium tabular-nums px-[7px] py-0.5 bg-orange-100 text-orange-700">' + esc(c.contributions.toLocaleString()) + '<span class="sr-only"> contributions</span></span>' +
                '</li>';
        }
        var next = pos === taken + 1;
        return '' +
            '<li data-cbox-row="' + pos + '" data-cbox-open="' + (next ? 'next' : 'open') + '" class="' + base + '">' + num +
                '<span class="block w-[30px] h-[30px] ' + (next ? 'border-2 border-orange bg-orange-50' : 'border border-dashed border-charcoal-200') + '" aria-hidden="true"></span>' +
                '<span class="truncate ' + (next ? 'font-medium text-charcoal' : 'font-medium text-charcoal-300') + '">' + (next ? 'Open place, this could be you' : 'Open place') + '</span>' +
                '<span></span>' +
            '</li>';
    }

    // D6: the ranking is built from code contributions only. Spelled out on the full page,
    // where there is room to say it, so nobody expects an idea to move them up the list.
    function counting() {
        return '' +
            '<div data-cbox-counting class="mt-4 pt-4 border-t ' + LINE + '">' +
                '<h3 class="m-0 mb-1.5 text-sm font-bold text-charcoal">How this is counted</h3>' +
                '<p class="m-0 max-w-[70ch] text-[13px] leading-relaxed text-charcoal-300">' +
                    'The ranking counts <strong class="font-semibold text-charcoal">code contributions only</strong> — commits merged into the ' +
                    '<code class="font-mono">docs</code> and <code class="font-mono">docs-website</code> repositories. ' +
                    'Ideas sent through “Suggest an idea” are credited in the issue itself and do not change the ranking. ' +
                    'Counts are baked in when the site is rebuilt.' +
                '</p>' +
            '</div>';
    }

    function top10(list, full) {
        var rows = '';
        for (var p = 1; p <= PLACES; p++) { rows += topRow(list[p - 1], p, list.length); }
        return '' +
            '<div id="dev-cbox-top10" class="px-6 pt-2 pb-4 border-b ' + LINE + '"' + (full ? '' : ' hidden') + '>' +
                '<ol class="list-none m-0 p-0 columns-1 sm:columns-2 gap-x-7">' + rows + '</ol>' +
                counting() +
            '</div>';
    }

    /* ---------- Ways to contribute ---------- */

    function chip(label) {
        return '<span class="font-mono text-[10px] font-medium leading-none tracking-[0.1em] uppercase whitespace-nowrap text-orange-700 bg-orange-50 border border-orange-200 px-1.5 py-1">' + label + '</span>';
    }

    function ideaChoice(value, label, hint, checked, extraClass) {
        return '' +
            '<label class="relative block ' + extraClass + '">' +
                '<input type="radio" name="dev-idea-type" value="' + esc(value) + '"' + (checked ? ' checked' : '') + ' class="peer absolute inset-0 z-10 m-0 w-full h-full opacity-0 cursor-pointer" />' +
                '<span class="grid gap-px h-full px-3 ' + (hint ? 'py-[9px]' : 'py-[7px]') + ' cursor-pointer text-charcoal peer-[:not(:checked):hover]:bg-[#f5f3f1] peer-checked:bg-charcoal peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-[3px] peer-focus-visible:-outline-offset-[3px] peer-focus-visible:outline-orange">' +
                    '<strong class="text-[13px] font-semibold">' + esc(label) + '</strong>' +
                    (hint ? '<small class="text-[11.5px] leading-[1.35] opacity-80">' + esc(hint) + '</small>' : '') +
                '</span>' +
            '</label>';
    }

    function ideaCard() {
        return '' +
            '<div data-cbox-idea class="grid gap-3.5 content-start px-6 pt-5 pb-[22px]">' +
                '<div class="flex flex-wrap justify-between items-baseline gap-2.5"><h3 class="m-0 text-base font-bold text-charcoal">Suggest an idea</h3>' + chip('Credited in the issue') + '</div>' +
                '<p class="-mt-2 mb-0 text-[13px] text-charcoal-300">Something missing, or something we could do better? No code needed.</p>' +
                '<fieldset class="m-0 p-0 border-0 min-w-0">' +
                    '<legend class="sr-only">What kind of idea is it?</legend>' +
                    '<div class="grid grid-cols-1 min-[421px]:grid-cols-2 border border-charcoal">' +
                        ideaChoice('content', 'A docs topic', 'New content or a missing subject', true, '') +
                        ideaChoice('website', 'This website', 'Navigation, search, layout', false, 'border-t min-[421px]:border-t-0 min-[421px]:border-l border-charcoal') +
                    '</div>' +
                '</fieldset>' +
                '<div class="flex flex-wrap items-center gap-x-4 gap-y-2.5">' +
                    '<a data-dev-idea-github href="' + esc(ideaGithubUrl('content')) + '" target="_blank" rel="noopener noreferrer" ' +
                    'class="inline-flex items-center justify-center gap-2 px-[15px] py-2.5 bg-orange-700 hover:bg-orange-800 border-2 border-orange-700 hover:border-orange-800 text-white hover:text-white text-sm font-semibold no-underline ' + FOCUS.replace('outline-orange', 'outline-charcoal') + '">' +
                        GITHUB_MARK + 'Suggest on GitHub<span class="sr-only"> (opens in a new tab)</span>' +
                    '</a>' +
                    '<a data-dev-idea-mail href="#" ' +
                    'class="py-1 text-[13px] font-medium text-charcoal underline decoration-orange decoration-2 underline-offset-4 hover:text-orange-700 ' + FOCUS + '">' +
                        'Email us' +
                    '</a>' +
                '</div>' +
            '</div>';
    }

    function editCard(editUrl, full) {
        // The contributors page has no source file of its own, so its card points at the
        // repository root and says so, rather than claiming to edit the page you are on.
        var title = full ? 'Improve a page' : 'Improve this page';
        var lead = full
            ? 'Every documentation page carries an “Edit this page on GitHub” button. Open the repository to browse the source and start a pull request.'
            : 'Spotted something wrong? Fix it directly. Your edit becomes a pull request we review.';
        var action = full ? 'Open the docs repository' : 'Edit this page on GitHub';
        return '' +
            '<div data-cbox-edit class="grid gap-3.5 content-start px-6 pt-5 pb-[22px] border-t md:border-t-0 md:border-l ' + LINE + '">' +
                '<div class="flex flex-wrap justify-between items-baseline gap-2.5"><h3 class="m-0 text-base font-bold text-charcoal">' + title + '</h3>' + chip('Counts toward the top 10') + '</div>' +
                '<p class="-mt-2 mb-0 text-[13px] text-charcoal-300">' + lead + '</p>' +
                '<ul class="list-none m-0 p-0 grid gap-1.5 text-[13px] text-[#4b4b4b]">' +
                    ['Typos and unclear wording', 'Steps that changed in Magento 2.4.8', 'Missing examples or screenshots'].map(function (t) {
                        return '<li class="grid grid-cols-[10px_minmax(0,1fr)] gap-[9px]"><span class="w-1.5 h-1.5 mt-[7px] bg-orange" aria-hidden="true"></span><span>' + t + '</span></li>';
                    }).join('') +
                '</ul>' +
                '<div class="flex flex-wrap items-center gap-x-4 gap-y-2.5">' +
                    '<a data-cbox-edit-link href="' + esc(editUrl) + '" target="_blank" rel="noopener noreferrer" ' +
                    'class="inline-flex items-center justify-center gap-2 px-[15px] py-2.5 bg-white hover:bg-charcoal border-2 border-charcoal text-charcoal hover:text-white text-sm font-semibold no-underline ' + FOCUS + '">' +
                        EDIT_ICON + action + '<span class="sr-only"> (opens in a new tab)</span>' +
                    '</a>' +
                '</div>' +
            '</div>';
    }

    // repoCard: hub pages and the contributors page have no source file of their own, so the
    // improve card opens the repository and says so.
    function ways(editUrl, repoCard) {
        var cols = editUrl ? 'md:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]' : '';
        return '<div class="grid grid-cols-1 ' + cols + '">' + ideaCard() + (editUrl ? editCard(editUrl, repoCard) : '') + '</div>';
    }

    function boxHtml(list, editUrl, full, hub) {
        return '' +
            '<section data-dev-cbox' + (full ? ' data-dev-cbox-full' : '') + ' class="' + (full ? 'mt-0 mb-10' : 'mt-9 mb-10') + ' border-2 border-charcoal bg-white font-sans text-charcoal [overflow-wrap:anywhere]" aria-labelledby="dev-cbox-title">' +
                head(list, full) +
                (full ? places(list, full) + top10(list, full) : '') +
                ways(editUrl, full || hub) +
                '<div class="flex flex-wrap justify-between gap-x-4 gap-y-1.5 px-6 py-[11px] border-t ' + LINE + ' text-xs text-charcoal-300">' +
                    '<span>' + INFO_ICON + 'Ideas and edits on GitHub are public.</span>' +
                    '<span>Emailed ideas go straight to the docs team.</span>' +
                '</div>' +
            '</section>';
    }

    // Compact block for hub pages (Carl, 2026-10-05): heading, then one row with the idea choice,
    // Suggest on GitHub, Email us and the repository link. Same hooks as the full box, so bind() works.
    function compactHtml(repoUrl) {
        var label = 'm-0 text-sm font-bold text-charcoal';
        return '' +
            '<section data-dev-cbox data-dev-cbox-compact class="mt-9 mb-10 border-2 border-charcoal bg-white font-sans text-charcoal [overflow-wrap:anywhere]" aria-labelledby="dev-cbox-title">' +
                '<div class="px-5 sm:px-6 py-5">' +
                    '<div class="inline-flex items-center gap-2 mb-2 font-mono text-[11px] font-medium leading-none tracking-[0.12em] uppercase text-orange-700">' +
                        '<i class="inline-block w-2.5 h-2.5 bg-orange" aria-hidden="true"></i>Written by the community' +
                    '</div>' +
                    '<h2 id="dev-cbox-title" class="m-0 text-lg sm:text-xl leading-tight font-extrabold tracking-[-0.015em] text-charcoal [text-wrap:balance]">Help build the Magento docs, and get your name on them</h2>' +
                    '<div class="grid gap-4 mt-4 pt-4 border-t ' + LINE + ' lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center lg:gap-x-8">' +
                        '<div data-cbox-idea class="flex flex-wrap items-center gap-x-4 gap-y-2.5">' +
                            '<h3 class="' + label + '">Suggest an idea</h3>' +
                            '<fieldset class="m-0 p-0 border-0 min-w-0">' +
                                '<legend class="sr-only">What kind of idea is it?</legend>' +
                                '<div class="grid grid-cols-2 border border-charcoal">' +
                                    ideaChoice('content', 'A docs topic', '', true, '') +
                                    ideaChoice('website', 'This website', '', false, 'border-l border-charcoal') +
                                '</div>' +
                            '</fieldset>' +
                            '<div class="flex flex-wrap items-center gap-x-4 gap-y-2.5">' +
                                '<a data-dev-idea-github href="' + esc(ideaGithubUrl('content')) + '" target="_blank" rel="noopener noreferrer" ' +
                                'class="inline-flex items-center justify-center gap-2 px-[15px] py-2 bg-orange-700 hover:bg-orange-800 border-2 border-orange-700 hover:border-orange-800 text-white hover:text-white text-sm font-semibold no-underline ' + FOCUS.replace('outline-orange', 'outline-charcoal') + '">' +
                                    GITHUB_MARK + 'Suggest on GitHub<span class="sr-only"> (opens in a new tab)</span>' +
                                '</a>' +
                                '<a data-dev-idea-mail href="#" ' +
                                'class="py-1 text-[13px] font-medium text-charcoal underline decoration-orange decoration-2 underline-offset-4 hover:text-orange-700 ' + FOCUS + '">Email us</a>' +
                            '</div>' +
                        '</div>' +
                        '<div data-cbox-edit class="flex flex-wrap items-center gap-x-4 gap-y-2.5 lg:pl-8 lg:border-l ' + LINE + '">' +
                            '<h3 class="' + label + '">Improve a page</h3>' +
                            '<a data-cbox-edit-link href="' + esc(repoUrl) + '" target="_blank" rel="noopener noreferrer" ' +
                            'class="inline-flex items-center justify-center gap-2 px-[15px] py-2 bg-white hover:bg-charcoal border-2 border-charcoal text-charcoal hover:text-white text-sm font-semibold no-underline ' + FOCUS + '">' +
                                EDIT_ICON + 'Open the docs repository<span class="sr-only"> (opens in a new tab)</span>' +
                            '</a>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</section>';
    }

    /* ---------- Behaviour (bound after injection; no inline handlers) ---------- */

    function bind(box) {
        var github = box.querySelector('[data-dev-idea-github]');
        var mail = box.querySelector('[data-dev-idea-mail]');
        var toggle = box.querySelector('[data-cbox-toggle]');
        var list = box.querySelector('#dev-cbox-top10');

        function selectedType() {
            var checked = box.querySelector('input[name="dev-idea-type"]:checked');
            return checked && checked.value === 'website' ? 'website' : 'content';
        }
        function syncGithub() { if (github) { github.setAttribute('href', ideaGithubUrl(selectedType())); } }

        box.addEventListener('change', function (e) {
            if (e.target && e.target.name === 'dev-idea-type') { syncGithub(); }
        });
        if (github) {
            // Refresh before activation too, so a page title/URL change since render is picked up.
            github.addEventListener('click', syncGithub);
            github.addEventListener('auxclick', syncGithub);
        }
        if (mail) {
            // The address is assembled only at click time; it is never in the rendered HTML.
            mail.addEventListener('click', function () {
                mail.setAttribute('href', ideaMailUrl(selectedType()));
            });
        }
        // The full variant has no toggle: its list is open from the start.
        if (toggle && list) {
            var label = toggle.querySelector('[data-cbox-toggle-label]');
            toggle.addEventListener('click', function () {
                var open = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
                list.hidden = open;
                if (label) { label.textContent = open ? 'See the top 10' : 'Hide the top 10'; }
            });
        }
    }

    /* ---------- Sticky "Suggest an idea" button (article pages) ---------- */

    /*
     * The page's own TOC CSS (`.toc-sidebar a { ... }`, specificity 0,2,0) beats any utility
     * class on an anchor inside that panel, so the button is styled through one scoped rule
     * with an attribute selector (0,3,0) instead of fighting it with utilities.
     */
    var STICKY_CSS = '' +
        '.toc-sidebar a[data-cbox-sticky-link],a[data-cbox-sticky-link]{' +
            'display:flex;align-items:center;justify-content:center;gap:.5rem;' +
            'box-sizing:border-box;height:2.75rem;margin:0;padding:0 .75rem;border:2px solid #bc3312;background:#bc3312;' +
            'color:#fff;font-size:.8125rem;font-weight:600;line-height:1.25;text-decoration:none;' +
            'transition:background-color .15s ease,border-color .15s ease;}' +
        '.toc-sidebar a[data-cbox-sticky-link]:hover,a[data-cbox-sticky-link]:hover{' +
            'background:#962a16;border-color:#962a16;color:#fff;}' +
        'a[data-cbox-sticky-link]:focus-visible{outline:3px solid #2c2c2c;outline-offset:2px;}' +
        // "Back to top" sits underneath the idea button, so it drops its own divider (Carl, 2026-10-05).
        '.toc-sidebar [data-cbox-sticky] + .toc-back-to-top{margin-top:.75rem;padding-top:0;border-top:0;}' +
        // Right-aligned with the arrow after the words (Carl, 2026-10-05): the generated markup has the
        // arrow first, so reverse the row rather than regenerating every page.
        '.toc-sidebar [data-cbox-sticky] + .toc-back-to-top a{padding-top:0;padding-bottom:0;line-height:1.25rem;flex-direction:row-reverse;justify-content:flex-start;}' +
        // Floating twin for narrower screens and pages without the panel (bottom-right corner).
        'a[data-cbox-float]{position:fixed;right:16px;bottom:16px;z-index:30;' +
            'box-shadow:0 4px 14px rgba(0,0,0,.18);}';

    function stickyStyle() {
        if (document.getElementById('dev-cbox-sticky-css')) { return; }
        var s = document.createElement('style');
        s.id = 'dev-cbox-sticky-css';
        s.appendChild(document.createTextNode(STICKY_CSS));
        document.head.appendChild(s);
    }

    /*
     * A long page's TOC panel is taller than the viewport, which would push anything appended
     * below it off screen for good. Cap the panel to the room under its own sticky offset and
     * let the nav scroll inside it — but only when it actually overflows, so a short panel is
     * left with no inline styles at all.
     */
    function fitPanel(panel, nav) {
        panel.removeAttribute('style');
        nav.removeAttribute('style');
        var cs = window.getComputedStyle(panel);
        if (cs.position !== 'sticky') { return; }
        var offset = parseFloat(cs.top);
        if (!isFinite(offset)) { return; }
        var room = window.innerHeight - offset - STICKY_GAP;
        if (room <= 0) { return; }
        if (panel.scrollHeight <= room) { return; } // fits as it is — leave it untouched
        panel.style.display = 'flex';
        panel.style.flexDirection = 'column';
        panel.style.maxHeight = room + 'px';
        nav.style.flex = '1 1 auto';
        nav.style.minHeight = '0';
        nav.style.overflowY = 'auto';
        nav.style.overscrollBehavior = 'contain';
    }

    function stickyIdea() {
        var nav = document.getElementById('toc-nav');
        if (!nav || !nav.closest) { return; }
        var panel = nav.closest('.sticky');
        // No sticky panel on this page (the landing, the section indexes): add nothing.
        if (!panel || panel.querySelector('[data-cbox-sticky]')) { return; }

        stickyStyle();
        var wrap = document.createElement('div');
        wrap.setAttribute('data-cbox-sticky', '');
        // At the foot of the page this divider lines up with the contributor row's top border and
        // the button with "Edit this page on GitHub": 77px = the row's 32px top padding + its 89px card - the 44px button.
        wrap.className = 'mt-6 pt-[77px] border-t ' + LINE;
        // One action only: the docs-topic issue form from the shared contract (§6).
        wrap.innerHTML = '' +
            '<a data-cbox-sticky-link href="' + esc(ideaGithubUrl('content')) + '" target="_blank" rel="noopener noreferrer">' +
                GITHUB_MARK + '<span>Suggest an idea</span><span class="sr-only"> (opens in a new tab)</span>' +
            '</a>';
        // Above "Back to top", which moves underneath the button (Carl, 2026-10-05).
        var back = panel.querySelector('.toc-back-to-top');
        if (back) { panel.insertBefore(wrap, back); } else { panel.appendChild(wrap); }

        var link = wrap.querySelector('[data-cbox-sticky-link]');
        function sync() { link.setAttribute('href', ideaGithubUrl('content')); }
        link.addEventListener('click', sync);
        link.addEventListener('auxclick', sync);

        var fit = function () { fitPanel(panel, nav); };
        fit();
        // Fonts and lazy content can change the panel's height after first paint.
        window.addEventListener('load', fit);
        var t = 0;
        window.addEventListener('resize', function () {
            window.clearTimeout(t);
            t = window.setTimeout(fit, 120);
        });
    }

    /*
     * Floating "Suggest an idea" (Vijay's feedback, Carl 2026-10-04): wherever the panel button
     * above is not showing (below 1280px, or a page with no "On this page" panel), the same
     * action sits in the bottom-right corner. It steps aside while the contributor box at the
     * bottom of the page is on screen, so the two never double up. Not on the contributors page.
     */
    function floatingIdea() {
        if (document.querySelector('[data-cbox-float]')) { return; }
        stickyStyle();
        var link = document.createElement('a');
        link.setAttribute('data-cbox-sticky-link', '');
        link.setAttribute('data-cbox-float', '');
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
        link.setAttribute('href', ideaGithubUrl('content'));
        link.innerHTML = GITHUB_MARK + '<span>Suggest an idea</span><span class="sr-only"> (opens in a new tab)</span>';
        link.style.display = 'none';
        document.body.appendChild(link);

        function sync() { link.setAttribute('href', ideaGithubUrl('content')); }
        link.addEventListener('click', sync);
        link.addEventListener('auxclick', sync);

        function onScreen(el) {
            if (!el || !el.getClientRects().length) { return false; }
            var r = el.getBoundingClientRect();
            return r.bottom > 0 && r.top < window.innerHeight;
        }
        function update() {
            var panelLink = document.querySelector('[data-cbox-sticky] a[data-cbox-sticky-link]');
            var panelShown = !!panelLink && panelLink.getClientRects().length > 0;
            var boxShown = onScreen(document.querySelector('[aria-label="Top Contributors"]'));
            link.style.display = panelShown || boxShown ? 'none' : '';
        }
        var queued = false;
        function schedule() {
            if (queued) { return; }
            queued = true;
            window.requestAnimationFrame(function () { queued = false; update(); });
        }
        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', schedule);
        window.addEventListener('load', schedule);
        update();
        return schedule;
    }

    /* ---------- Article pages: the contributor row ---------- */

    // Restyled in line with the contributor block (Carl, 2026-10-05): avatars only (names stay
    // available to screen readers), linking to our contributors page, and "Edit this page on
    // GitHub" as a black button. Same layout and sizes as the merchant row (github-contributors.blade.php).
    var CONTRIBUTORS_PAGE = '/contributors';

    function contributorsSection(contribs) {
        if (!Array.isArray(contribs) || contribs.length === 0) { return ''; }
        var top = contribs.slice(0, 5);
        var avatars = top.map(function (c, i) {
            return '<img src="' + esc(safeUrl(c.avatar_url)) + '" alt="" width="44" height="44" loading="lazy" ' +
                'class="block w-11 h-11 max-w-none m-0 object-cover bg-[#f1f1f1] border-2 border-white outline outline-1 outline-[#e5e7eb]' + (i ? ' -ml-2' : '') + '" />';
        }).join('');
        return '' +
            // One bordered card, wholly a link, like the hub block (Carl, 2026-10-05)
            '<div role="region" aria-label="Top Contributors" class="flex">' +
                '<a href="' + CONTRIBUTORS_PAGE + '" class="group inline-flex flex-col gap-2 px-4 py-3 bg-white border border-gray-200 hover:border-orange-500 text-charcoal hover:text-charcoal no-underline transition-colors duration-150 motion-reduce:transition-none ' + FOCUS + '">' +
                    '<span class="flex items-center gap-2 font-mono text-[11px] font-medium leading-none tracking-[0.12em] uppercase text-orange-700">' +
                        '<i class="inline-block w-2 h-2 bg-orange" aria-hidden="true"></i>Written by the community' +
                    '</span>' +
                    '<span class="flex items-center gap-3 text-sm font-semibold">' +
                        '<span class="inline-flex" aria-hidden="true">' + avatars + '</span>' +
                        '<span>Meet the contributors</span>' +
                        '<span class="sr-only">: ' + top.map(function (c) { return esc(c.login); }).join(', ') + '</span>' +
                        '<svg class="w-4 h-4 ml-3 text-orange-700 transition-transform duration-150 group-hover:translate-x-[3px] motion-reduce:transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6l6 6-6 6"/></svg>' +
                    '</span>' +
                '</a>' +
            '</div>';
    }

    function rowEditSection(editUrl) {
        if (!editUrl) { return ''; }
        return '' +
            '<a href="' + esc(editUrl) + '" target="_blank" rel="noopener noreferrer" ' +
            'class="inline-flex items-center gap-2 h-11 px-4 bg-charcoal hover:bg-charcoal-400 border-2 border-charcoal hover:border-charcoal-400 text-white hover:text-white text-sm font-semibold no-underline ' + FOCUS + '">' +
                EDIT_ICON + 'Edit this page on GitHub<span class="sr-only"> (opens in a new tab)</span>' +
            '</a>';
    }

    function rowHtml(contribs, editUrl) {
        var left = contributorsSection(contribs);
        var right = rowEditSection(editUrl);
        if (!left && !right) { return ''; }
        // No page-width wrapper: the row fills its host container (the #dev-contributors
        // placeholder inside the article column on content pages), matching the merchant docs.
        return '' +
            '<div data-contributor-row class="mt-16 pt-8 pb-8 border-t border-gray-200 flex flex-wrap items-end justify-between gap-x-6 gap-y-5">' +
                left + right +
            '</div>';
    }


    /* ---------- Injection ---------- */

    function inject(html, fullTarget) {
        var container = document.createElement('div');
        container.innerHTML = html;
        var node = container.firstChild;
        // The contributors page: its own placeholder takes precedence.
        var target = fullTarget || document.getElementById('dev-contributors');
        if (target) {
            target.appendChild(node);
        } else {
            // Fallback: just above the footer.
            var footer = document.querySelector('footer');
            if (footer && footer.parentNode) {
                footer.parentNode.insertBefore(node, footer);
            } else {
                document.body.appendChild(node);
            }
        }
        bind(node);
    }

    function injectRow(html) {
        if (!html) { return; }
        var container = document.createElement('div');
        container.innerHTML = html;
        var node = container.firstChild;
        var target = document.getElementById('dev-contributors');
        if (target) { target.appendChild(node); return; }
        var footer = document.querySelector('footer');
        if (footer && footer.parentNode) { footer.parentNode.insertBefore(node, footer); } else { document.body.appendChild(node); }
    }

    function init() {
        // Three placements (Carl, 2026-10-05):
        //   contributors page  <div id="dev-contributors-full">  the full block (podium, top 10, cards)
        //   hub pages          <div id="dev-contributor-box">    the compact block (heading + one row of actions)
        //   article pages      everything else                    the original top-contributors row
        var fullTarget = document.getElementById(FULL_TARGET_ID);
        var hubTarget = document.getElementById(HUB_TARGET_ID);
        if (hubTarget && !fullTarget) {
            // The compact block shows no contributors, so it needs no data.
            inject(compactHtml(REPO_ROOT_URL), hubTarget);
            return;
        }
        var data = fetch('contributors.json', { cache: 'no-cache' })
            .then(function (r) { return r.ok ? r.json() : []; })
            .catch(function () { return []; });

        if (fullTarget) {
            // No source file of its own, so the improve card goes to the repo root.
            data.then(function (d) { inject(boxHtml(normalise(d), REPO_ROOT_URL, true, false), fullTarget); });
            return;
        }

        var meta = document.querySelector('meta[name="edit-url"]');
        var editUrl = safeUrl(meta ? meta.getAttribute('content') : '');
        // Independent of the contributor data, and the page's TOC is already built by now.
        stickyIdea();
        var refloat = floatingIdea();
        data.then(function (d) { injectRow(rowHtml(d, editUrl)); })
            .then(function () { if (refloat) { refloat(); } });
    }

    if (document.readyState !== 'loading') { init(); }
    else { document.addEventListener('DOMContentLoaded', init); }
})();
