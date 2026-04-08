#!/usr/bin/env node
/**
 * Patch all guide/module HTML files to add a right-hand "On this page" TOC sidebar.
 * - Converts the flex layout to include a 3rd column (right aside)
 * - Injects CSS for the TOC + scroll-spy active states
 * - Injects JS that auto-generates TOC from h2/h3 headings + IntersectionObserver scroll-spy
 */
const fs = require('fs');
const path = require('path');

const deployDir = __dirname;
const files = fs.readdirSync(deployDir).filter(f => f.endsWith('.html'));

// CSS for the right-hand TOC sidebar
const tocCSS = `
        /* Right-hand TOC sidebar */
        .toc-sidebar {
            width: 14rem;
            flex-shrink: 0;
        }
        .toc-sidebar a {
            display: block;
            padding: 0.375rem 0;
            font-size: 0.8125rem;
            line-height: 1.4;
            color: #6b7280;
            text-decoration: none;
            transition: color 0.15s ease;
            border-left: 2px solid transparent;
            padding-left: 0.75rem;
        }
        .toc-sidebar a:hover {
            color: #111827;
        }
        .toc-sidebar a.toc-active {
            color: #ea580c;
            border-left-color: #ea580c;
            font-weight: 500;
        }
        .toc-sidebar a.toc-h3 {
            padding-left: 1.5rem;
            font-size: 0.75rem;
        }
        .toc-sidebar .toc-back-to-top {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        .toc-sidebar .toc-back-to-top a {
            border-left: none;
            padding-left: 0;
            font-weight: 500;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .toc-sidebar .toc-back-to-top a:hover {
            color: #ea580c;
        }`;

// The right aside HTML placeholder (JS will populate it)
const tocAside = `
            <!-- Right Sidebar: On This Page (desktop) -->
            <aside class="toc-sidebar hidden xl:block" aria-label="On this page">
                <div class="sticky top-24">
                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-4 mt-0">On this page</h3>
                    <nav id="toc-nav" class="space-y-0" aria-label="Table of contents"></nav>
                    <div class="toc-back-to-top">
                        <a href="#">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            Back to top
                        </a>
                    </div>
                </div>
            </aside>`;

// JS for TOC generation + scroll-spy
const tocScript = `
<script>
(function() {
    var nav = document.getElementById('toc-nav');
    if (!nav) return;
    var article = document.getElementById('main-content');
    if (!article) return;
    var headings = article.querySelectorAll('h2[id], h3[id]');
    if (headings.length < 3) {
        // Hide the entire TOC sidebar if fewer than 3 headings
        var sidebar = nav.closest('.toc-sidebar');
        if (sidebar) sidebar.style.display = 'none';
        return;
    }
    var ids = [];
    headings.forEach(function(h) {
        var a = document.createElement('a');
        a.href = '#' + h.id;
        a.textContent = h.textContent.replace(/^#+\\s*/, '').replace(/\\s+/g, ' ').trim();
        if (h.tagName === 'H3') a.classList.add('toc-h3');
        nav.appendChild(a);
        ids.push(h.id);
    });
    // Scroll-spy with IntersectionObserver
    var links = nav.querySelectorAll('a');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                links.forEach(function(l) { l.classList.remove('toc-active'); });
                var active = nav.querySelector('a[href="#' + entry.target.id + '"]');
                if (active) active.classList.add('toc-active');
            }
        });
    }, { rootMargin: '-80px 0px -70% 0px', threshold: 0 });
    headings.forEach(function(h) { observer.observe(h); });
})();
</script>`;

let patched = 0;
let skipped = 0;

files.forEach(file => {
    const filePath = path.join(deployDir, file);
    let html = fs.readFileSync(filePath, 'utf8');

    // Only patch files that have the main-content layout (guide/module pages)
    if (!html.includes('id="main-content"')) {
        skipped++;
        return;
    }

    // Skip if already patched
    if (html.includes('toc-sidebar')) {
        console.log(`SKIP (already has TOC): ${file}`);
        skipped++;
        return;
    }

    const original = html;

    // 1. Inject TOC CSS before closing </style>
    const styleCloseIdx = html.lastIndexOf('</style>');
    if (styleCloseIdx === -1) {
        console.log(`SKIP (no </style>): ${file}`);
        skipped++;
        return;
    }
    html = html.slice(0, styleCloseIdx) + tocCSS + '\n    ' + html.slice(styleCloseIdx);

    // 2. Inject the right aside after </main> (before the closing </div> of the flex container)
    html = html.replace(
        /(<\/main>\s*<\/div>)/,
        '</main>' + tocAside + '\n        </div>'
    );

    // 3. Inject the TOC script before </body>
    html = html.replace('</body>', tocScript + '\n</body>');

    if (html !== original) {
        fs.writeFileSync(filePath, html, 'utf8');
        console.log(`PATCHED: ${file}`);
        patched++;
    } else {
        console.log(`SKIP (no changes): ${file}`);
        skipped++;
    }
});

console.log(`\nDone: ${patched} patched, ${skipped} skipped`);
