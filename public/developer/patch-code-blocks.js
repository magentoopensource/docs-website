#!/usr/bin/env node
/**
 * patch-code-blocks.js
 * Fixes invisible code blocks in deployed Magento Association HTML docs.
 *
 * Root cause: inline <style> overrides `.hljs { background: #2C2C2C !important }`
 * without declaring `color`, so Tailwind preflight inherits `text-charcoal` (#2C2C2C)
 * — dark text on dark background. Also adds copy-to-clipboard buttons.
 *
 * Fixes applied per file:
 *   A) Inject `color: #abb2bf !important;` into the .hljs block
 *   B) Inject `color: #abb2bf;` into .prose pre code if it exists and lacks it
 *   C) Inject copy button CSS before closing </style>
 *   D) Replace hljs init script (highlightAll or DOMContentLoaded variant)
 *      with combined highlight + copy-button script
 */

const fs = require('fs');
const path = require('path');

const DEPLOY_DIR = __dirname;

// ─── Fix A: .hljs block — inject color after background ───────────────────────
// Handles both #2C2C2C and #2c2c2c (case-insensitive hex)
const HLJS_COLOR = 'color: #abb2bf !important;';

function fixHljsBlock(html) {
    // Match the .hljs { ... } block that has background: #2C2C2C and is missing color
    // The pattern is: background: #2C2C2C !important; followed by optional whitespace then border-radius
    return html.replace(
        /(\.hljs\s*\{[^}]*background:\s*#2[Cc]2[Cc]2[Cc]\s*!important;)(\s*)(border-radius)/,
        (match, before, ws, after) => {
            // Don't double-apply — skip if color is already present
            if (/color\s*:/.test(before)) return match;
            return `${before}\n            ${HLJS_COLOR}${ws}${after}`;
        }
    );
}

// ─── Fix B: .prose pre code — inject color if missing ─────────────────────────
function fixProsePreCode(html) {
    if (!html.includes('.prose pre code')) return html;
    return html.replace(
        /(\.prose pre code\s*\{)([^}]*?)(\})/,
        (match, open, body, close) => {
            if (/color\s*:/.test(body)) return match; // already has color
            return `${open}${body}            color: #abb2bf;\n        ${close}`;
        }
    );
}

// ─── Fix C: copy button CSS to inject before </style> ─────────────────────────
const COPY_BTN_CSS = `
        /* Code block copy button */
        .code-wrapper {
            position: relative;
        }
        .copy-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-family: 'Inter Tight', system-ui, sans-serif;
            background: rgba(255,255,255,0.1);
            color: #abb2bf;
            border: 1px solid rgba(255,255,255,0.15);
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.15s ease, background 0.15s ease;
            z-index: 10;
            line-height: 1.4;
            border-radius: 4px;
            user-select: none;
        }
        .code-wrapper:hover .copy-btn {
            opacity: 1;
        }
        .copy-btn:hover,
        .copy-btn:focus {
            background: rgba(255,255,255,0.2);
            color: #ffffff;
            outline: 2px solid #D4551A;
            outline-offset: 1px;
        }
        .copy-btn.copied {
            color: #98c379;
            background: rgba(152, 195, 121, 0.15);
            opacity: 1;
        }
`;

function injectCopyBtnCss(html) {
    if (html.includes('.copy-btn')) return html; // already patched
    // Inject before the first </style>
    return html.replace('</style>', COPY_BTN_CSS + '    </style>');
}

// ─── Fix D: replace hljs init script with combined highlight + copy script ────
const COMBINED_SCRIPT = `<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('pre code').forEach(function (el) {
        hljs.highlightElement(el);
    });
    document.querySelectorAll('pre').forEach(function (pre) {
        if (pre.parentNode.classList && pre.parentNode.classList.contains('code-wrapper')) return;
        var wrapper = document.createElement('div');
        wrapper.className = 'code-wrapper';
        pre.parentNode.insertBefore(wrapper, pre);
        wrapper.appendChild(pre);
        var btn = document.createElement('button');
        btn.className = 'copy-btn';
        btn.setAttribute('type', 'button');
        btn.setAttribute('aria-label', 'Copy code to clipboard');
        btn.setAttribute('title', 'Copy');
        btn.textContent = 'Copy';
        wrapper.appendChild(btn);
        btn.addEventListener('click', function () {
            var code = pre.querySelector('code');
            var text = code ? (code.innerText || code.textContent) : (pre.innerText || pre.textContent);
            navigator.clipboard.writeText(text).then(function () {
                btn.textContent = 'Copied!';
                btn.classList.add('copied');
                setTimeout(function () { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
            }).catch(function () {
                var range = document.createRange();
                range.selectNode(code || pre);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                document.execCommand('copy');
                window.getSelection().removeAllRanges();
                btn.textContent = 'Copied!';
                btn.classList.add('copied');
                setTimeout(function () { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
            });
        });
    });
});
</script>`;

// Pattern 1: simple one-liner  <script>hljs.highlightAll();</script>
const HIGHLIGHT_ALL_RE = /<script>\s*hljs\.highlightAll\(\);\s*<\/script>/;

// Pattern 2: DOMContentLoaded wrapper with highlightElement loop (used in 2 files)
const DOMCONTENTLOADED_RE = /<!--\s*Initialize Highlight\.js\s*-->\s*<script>[\s\S]*?DOMContentLoaded[\s\S]*?<\/script>/;

function replaceInitScript(html) {
    // Guard: already has the combined clipboard script — nothing to do
    if (html.includes('navigator.clipboard')) return html;

    if (HIGHLIGHT_ALL_RE.test(html)) {
        return html.replace(HIGHLIGHT_ALL_RE, COMBINED_SCRIPT);
    }
    if (DOMCONTENTLOADED_RE.test(html)) {
        return html.replace(DOMCONTENTLOADED_RE, COMBINED_SCRIPT);
    }
    return html;
}

// ─── Main runner ──────────────────────────────────────────────────────────────
const files = fs.readdirSync(DEPLOY_DIR)
    .filter(f => f.endsWith('.html'))
    .map(f => path.join(DEPLOY_DIR, f));

// Only patch files that have the broken .hljs block (skip index/nav pages)
const targets = files.filter(f => {
    const content = fs.readFileSync(f, 'utf8');
    return /\.hljs\s*\{/.test(content);
});

console.log(`Found ${files.length} HTML files total.`);
console.log(`Found ${targets.length} files with .hljs blocks to patch.\n`);

let patched = 0;
let skipped = 0;

for (const filePath of targets) {
    const fileName = path.basename(filePath);
    let html = fs.readFileSync(filePath, 'utf8');
    const original = html;

    html = fixHljsBlock(html);
    html = fixProsePreCode(html);
    html = injectCopyBtnCss(html);
    html = replaceInitScript(html);

    if (html !== original) {
        fs.writeFileSync(filePath, html, 'utf8');
        console.log(`  PATCHED  ${fileName}`);
        patched++;
    } else {
        console.log(`  skipped  ${fileName} (no changes needed)`);
        skipped++;
    }
}

console.log(`\nDone. ${patched} file(s) patched, ${skipped} already up to date.`);

// Verify the reported broken file explicitly
const reportedFile = path.join(DEPLOY_DIR, 'guide-tutorial-docker-development-environment.html');
if (fs.existsSync(reportedFile)) {
    const content = fs.readFileSync(reportedFile, 'utf8');
    const hasColor = /\.hljs\s*\{[^}]*color\s*:\s*#abb2bf/i.test(content);
    const hasCopyBtn = content.includes('copy-btn');
    const hasScript = content.includes('navigator.clipboard');
    console.log(`\nVerification — guide-tutorial-docker-development-environment.html:`);
    console.log(`  .hljs color fix applied : ${hasColor ? 'YES' : 'NO — CHECK FAILED'}`);
    console.log(`  Copy button CSS present : ${hasCopyBtn ? 'YES' : 'NO — CHECK FAILED'}`);
    console.log(`  Copy script present     : ${hasScript ? 'YES' : 'NO — CHECK FAILED'}`);
}
