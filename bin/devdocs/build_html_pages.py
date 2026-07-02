#!/usr/bin/env python3
"""
Convert all ma-devdocs markdown files to styled HTML pages matching the existing Bauhaus design.
Also updates placeholder href="#" links in overview pages.

Design language: square corners everywhere, hexagon bullets, orange/yellow/charcoal palette,
Inter Tight font, Courier New for code (no JetBrains Mono).
"""

import markdown
import os
import re
import yaml
import glob
import json
import urllib.request

# Paths are resolved from env vars set by generate.sh.
# Fallback defaults are relative to this script's location (repo root → ma-devdocs / deploy dir).
_SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
MD_DIR = os.environ.get("DEVDOCS_MD_DIR") or os.path.normpath(
    os.path.join(_SCRIPT_DIR, "..", "..", "..", "ma-devdocs")
)
OUT_DIR = os.environ.get("DEVDOCS_OUT_DIR") or os.path.normpath(
    os.path.join(_SCRIPT_DIR, "..", "..", "..", "deploy-magento-association")
)

# Filename mapping: markdown path -> html filename
def md_to_html_filename(md_path):
    """Convert markdown path to flat HTML filename."""
    rel = os.path.relpath(md_path, MD_DIR)
    # guides/tutorials/docker-development-environment.md -> guide-tutorial-docker-development-environment.html
    # modules/catalog/architecture.md -> module-catalog-architecture.html
    # modules/catalog/README.md -> module-catalog-overview.html (README -> overview)
    parts = rel.replace('.md', '').split('/')
    if parts[-1] == 'README':
        parts[-1] = 'overview'
    # Singularize category prefixes for cleaner URLs
    if parts[0] == 'guides':
        parts[0] = 'guide'
        # tutorials -> tutorial, how-to -> howto, etc.
        if len(parts) > 1:
            parts[1] = parts[1].rstrip('s') if parts[1] != 'how-to' else 'howto'
            if parts[1] == 'reference':
                parts[1] = 'reference'
            if parts[1] == 'explanation':
                parts[1] = 'explanation'
    elif parts[0] == 'modules':
        parts[0] = 'module'
    return '-'.join(parts) + '.html'

def rewrite_md_links(html, md_path):
    """Rewrite relative .md cross-reference links to their flat .html filenames.

    Internal links in the markdown (e.g. ../tutorials/x.md, ../../modules/catalog/README.md)
    are resolved relative to the source file and mapped through md_to_html_filename so they
    point at the generated HTML pages instead of raw markdown. External URLs, anchors and
    links outside the docs tree are left untouched.
    """
    src_dir = os.path.dirname(md_path)

    def _repl(match):
        target = match.group(2)
        if target.startswith(('http://', 'https://', '//', '#', 'mailto:')):
            return match.group(0)
        path_part, sep, frag = target.partition('#')
        if not path_part.endswith('.md'):
            return match.group(0)
        abs_md = os.path.normpath(os.path.join(src_dir, path_part))
        rel = os.path.relpath(abs_md, MD_DIR)
        if rel.startswith('..'):
            return match.group(0)
        new_target = md_to_html_filename(abs_md) + (sep + frag if sep else '')
        return f'{match.group(1)}{new_target}{match.group(3)}'

    return re.sub(r'(href=")([^"]+?\.md(?:#[^"]*)?)(")', _repl, html)

def parse_frontmatter(content):
    """Extract YAML frontmatter and body from markdown."""
    if content.startswith('---'):
        parts = content.split('---', 2)
        if len(parts) >= 3:
            try:
                meta = yaml.safe_load(parts[1])
                body = parts[2].strip()
                return meta or {}, body
            except yaml.YAMLError:
                pass
    return {}, content

def get_breadcrumb(meta, md_path):
    """Generate breadcrumb HTML from file metadata and path. Uses orange › separator."""
    rel = os.path.relpath(md_path, MD_DIR)
    parts = rel.split('/')
    crumbs = ['<a href="index.html" class="text-orange hover:text-orange-600 transition-colors no-underline">Home</a>']

    sep = '<span class="text-orange font-medium">›</span>'

    if parts[0] == 'guides':
        crumbs.append(sep)
        crumbs.append('<a href="index.html" class="text-charcoal-300 hover:text-orange transition-colors no-underline">Guides</a>')
        cat = parts[1] if len(parts) > 1 else ''
        cat_map = {
            'tutorials': ('Tutorials', 'tutorials.html'),
            'how-to': ('How-To Guides', 'how-to-guides.html'),
            'explanations': ('Architecture', 'architecture.html'),
            'references': ('References', 'references.html'),
        }
        if cat in cat_map:
            label, href = cat_map[cat]
            crumbs.append(sep)
            crumbs.append(f'<a href="{href}" class="text-charcoal-300 hover:text-orange transition-colors no-underline">{label}</a>')
    elif parts[0] == 'modules':
        crumbs.append(sep)
        crumbs.append(f'<a href="modules.html" class="text-charcoal-300 hover:text-orange transition-colors no-underline">Modules</a>')
        if len(parts) > 1:
            mod_name = parts[1].capitalize()
            crumbs.append(sep)
            crumbs.append(f'<a href="module-{parts[1]}.html" class="text-charcoal-300 hover:text-orange transition-colors no-underline">{mod_name}</a>')

    title = meta.get('title', parts[-1].replace('-', ' ').replace('.md', '').title())
    crumbs.append(sep)
    crumbs.append(f'<span class="text-charcoal font-medium">{title}</span>')

    return ' '.join(crumbs)

def get_doc_type_badge(meta, md_path):
    """Return a square badge for the document type. No rounded corners."""
    rel = os.path.relpath(md_path, MD_DIR)
    doc_type = meta.get('doc_type', meta.get('type', ''))

    # Square badges: no rounded-full, matches overview page pattern
    badges = {
        'tutorial':              ('Tutorial',             'bg-orange-50 text-orange-700 border border-orange-200'),
        'how-to':                ('How-To',               'bg-yellow-50 text-yellow-800 border border-yellow-200'),
        'explanation':           ('Explanation',          'bg-charcoal-50 text-charcoal border border-charcoal-100'),
        'reference':             ('Reference',            'bg-charcoal-50 text-charcoal-400 border border-charcoal-100'),
        'architecture':          ('Architecture',         'bg-charcoal-50 text-charcoal border border-charcoal-100'),
        'execution-flows':       ('Execution Flows',      'bg-orange-50 text-orange-700 border border-orange-200'),
        'plugins-and-observers': ('Extension Points',     'bg-yellow-50 text-yellow-800 border border-yellow-200'),
        'integrations':          ('Integrations',         'bg-orange-50 text-orange-700 border border-orange-200'),
        'anti-patterns':         ('Anti-Patterns',        'bg-charcoal text-white'),
        'performance':           ('Performance',          'bg-yellow-50 text-yellow-800 border border-yellow-200'),
        'known-issues':          ('Known Issues',         'bg-charcoal text-white'),
        'version-compatibility': ('Version Compatibility','bg-orange-50 text-orange-700 border border-orange-200'),
        'overview':              ('Overview',             'bg-charcoal-50 text-charcoal border border-charcoal-100'),
    }

    # Try doc_type from frontmatter first
    if doc_type in badges:
        label, classes = badges[doc_type]
        return f'<span class="inline-block px-3 py-1.5 text-xs font-medium {classes}">{label}</span>'

    # Infer from filename
    filename = os.path.basename(md_path).replace('.md', '')
    if filename == 'README':
        filename = 'overview'
    if filename in badges:
        label, classes = badges[filename]
        return f'<span class="inline-block px-3 py-1.5 text-xs font-medium {classes}">{label}</span>'

    return ''

def get_sidebar_nav(md_path):
    """Generate sidebar navigation based on the section. Square hover states, no rounded corners."""
    rel = os.path.relpath(md_path, MD_DIR)
    parts = rel.split('/')
    current_file = md_to_html_filename(md_path)

    items = []

    if parts[0] == 'modules' and len(parts) > 1:
        module = parts[1]
        mod_name = module.capitalize()
        section_files = [
            (f'module-{module}-overview.html', 'Overview'),
            (f'module-{module}-architecture.html', 'Architecture'),
            (f'module-{module}-execution-flows.html', 'Execution Flows'),
            (f'module-{module}-plugins-and-observers.html', 'Plugins & Observers'),
            (f'module-{module}-integrations.html', 'Integrations'),
            (f'module-{module}-anti-patterns.html', 'Anti-Patterns'),
            (f'module-{module}-performance.html', 'Performance'),
            (f'module-{module}-known-issues.html', 'Known Issues'),
            (f'module-{module}-version-compatibility.html', 'Version Compatibility'),
        ]
        items.append(f'<h3 class="text-xs font-bold text-charcoal uppercase tracking-wider mb-4">{mod_name} Module</h3>')
        items.append('<ul class="space-y-0.5 list-none pl-0">')
        for href, label in section_files:
            if href == current_file:
                active = 'text-orange font-semibold bg-orange-50 border-l-4 border-orange pl-2'
            else:
                active = 'text-charcoal-400 hover:text-orange hover:bg-off-white'
            items.append(f'<li class="pl-0"><a href="{href}" class="block px-3 py-2 text-sm {active} transition-colors no-underline">{label}</a></li>')
        items.append('</ul>')

        # Other modules
        other_modules = [m for m in ['catalog', 'checkout', 'customer', 'quote', 'sales'] if m != module]
        items.append('<h3 class="text-xs font-bold text-charcoal uppercase tracking-wider mb-4 mt-8">Other Modules</h3>')
        items.append('<ul class="space-y-0.5 list-none pl-0">')
        for m in other_modules:
            items.append(f'<li class="pl-0"><a href="module-{m}.html" class="block px-3 py-2 text-sm text-charcoal-400 hover:text-orange hover:bg-off-white transition-colors no-underline">{m.capitalize()}</a></li>')
        items.append('</ul>')

    elif parts[0] == 'guides':
        cat = parts[1] if len(parts) > 1 else ''
        cat_map = {
            'tutorials': ('Tutorials', 'tutorials.html'),
            'how-to': ('How-To Guides', 'how-to-guides.html'),
            'explanations': ('Architecture', 'architecture.html'),
            'references': ('References', 'references.html'),
        }

        # Current section files
        if cat:
            section_dir = os.path.join(MD_DIR, 'guides', cat)
            section_files = sorted(glob.glob(os.path.join(section_dir, '*.md')))
            label = cat_map.get(cat, (cat.title(), '#'))[0]
            items.append(f'<h3 class="text-xs font-bold text-charcoal uppercase tracking-wider mb-4">{label}</h3>')
            items.append('<ul class="space-y-0.5 list-none pl-0">')
            for sf in section_files:
                sf_html = md_to_html_filename(sf)
                sf_meta, _ = parse_frontmatter(open(sf).read())
                sf_title = sf_meta.get('title', os.path.basename(sf).replace('.md', '').replace('-', ' ').title())
                # Truncate long titles
                if len(sf_title) > 35:
                    sf_title = sf_title[:32] + '...'
                if sf_html == current_file:
                    active = 'text-orange font-semibold bg-orange-50 border-l-4 border-orange pl-2'
                else:
                    active = 'text-charcoal-400 hover:text-orange hover:bg-off-white'
                items.append(f'<li class="pl-0"><a href="{sf_html}" class="block px-3 py-2 text-sm {active} transition-colors no-underline">{sf_title}</a></li>')
            items.append('</ul>')

        # Other guide sections
        items.append('<h3 class="text-xs font-bold text-charcoal uppercase tracking-wider mb-4 mt-8">Guide Sections</h3>')
        items.append('<ul class="space-y-0.5 list-none pl-0">')
        for section_key, (section_label, section_href) in cat_map.items():
            if section_key == cat:
                active_class = 'text-orange font-semibold bg-orange-50 border-l-4 border-orange pl-2'
            else:
                active_class = 'text-charcoal-400 hover:text-orange hover:bg-off-white'
            items.append(f'<li class="pl-0"><a href="{section_href}" class="block px-3 py-2 text-sm {active_class} transition-colors no-underline">{section_label}</a></li>')
        items.append('</ul>')

    return '\n'.join(items)

def build_page(md_path):
    """Convert a markdown file to a full HTML page with Bauhaus design language."""
    with open(md_path, 'r') as f:
        raw = f.read()

    meta, body = parse_frontmatter(raw)
    title = meta.get('title', os.path.basename(md_path).replace('.md', '').replace('-', ' ').title())
    description = meta.get('description', title)

    # Per-page "Edit on GitHub" URL — points at the source .md under developer/
    # in the content repo (read by includes/contributors.js to render the edit button).
    rel_md = os.path.relpath(md_path, MD_DIR)
    edit_url = f"https://github.com/magentoopensource/docs/edit/main/developer/{rel_md}"

    # Convert markdown to HTML
    md = markdown.Markdown(extensions=[
        'tables',
        'fenced_code',
        'toc',
        'attr_list',
        'md_in_html',
    ], extension_configs={
        'toc': {'permalink': False, 'toc_depth': '2-3'},
    })

    content_html = md.convert(body)
    content_html = rewrite_md_links(content_html, md_path)
    toc_html = md.toc

    # Post-process: style tables, callouts, lists, headings
    content_html = style_content(content_html)

    breadcrumb = get_breadcrumb(meta, md_path)
    badge = get_doc_type_badge(meta, md_path)
    sidebar = get_sidebar_nav(md_path)

    # Meta info bar — square inline-flex pills with icons matching merchant docs style
    meta_items = []
    if meta.get('difficulty'):
        diff = meta['difficulty'].capitalize()
        meta_items.append(
            f'<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-yellow-50 text-yellow-800">'
            f'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
            f'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
            f'</svg>{diff}</span>'
        )
    if meta.get('estimated_time'):
        meta_items.append(
            f'<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-orange-50 text-orange-700">'
            f'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
            f'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            f'</svg>{meta["estimated_time"]}</span>'
        )
    if meta.get('version'):
        meta_items.append(
            f'<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-charcoal text-white">'
            f'Magento {meta["version"]}</span>'
        )
    if meta.get('module'):
        meta_items.append(
            f'<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-charcoal-50 text-charcoal border border-charcoal-100">'
            f'{meta["module"]}</span>'
        )
    meta_bar = '\n'.join(meta_items)

    html_filename = md_to_html_filename(md_path)

    return html_filename, f'''<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title} — Magento 2 Developer Documentation</title>
    <meta name="description" content="{description}">
    <meta name="edit-url" content="{edit_url}">

    <!-- Google Fonts: Inter Tight only. Courier New used for code. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Highlight.js: syntax highlighting for code blocks -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/yaml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/ini.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {{
            theme: {{
                extend: {{
                    colors: {{
                        charcoal: {{
                            DEFAULT: '#2c2c2c', 50: '#f1f1f1', 100: '#d9d9d9', 200: '#b6b6b6',
                            300: '#818181', 400: '#474747', 500: '#2c2c2c', 600: '#262626',
                            700: '#202020', 800: '#1c1c1c', 900: '#191919', 950: '#121212'
                        }},
                        orange: {{
                            DEFAULT: '#F26423', 50: '#fef5ee', 100: '#fee9d6', 200: '#fbd0ad',
                            300: '#f9ae78', 400: '#f58242', 500: '#f26423', 600: '#e34613',
                            700: '#bc3312', 800: '#962a16', 900: '#792515', 950: '#411009'
                        }},
                        yellow: {{
                            DEFAULT: '#F1BC1B', 50: '#fffdeb', 100: '#fdf9c8', 200: '#fbf38c',
                            300: '#f8e651', 400: '#f7d728', 500: '#f1bc1b', 600: '#d5900a',
                            700: '#b1670c', 800: '#8f5111', 900: '#764311', 950: '#442204'
                        }},
                        'off-white': '#FAFAFA',
                    }},
                    fontFamily: {{
                        sans: ['Inter Tight', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['Courier New', 'Courier', 'monospace'],
                    }},
                }}
            }}
        }}
    </script>

    <style>
        /* ── Force light mode ── */
        :root {{ color-scheme: light only; }}

        [x-cloak] {{ display: none !important; }}

        /* ── Scroll offset for sticky header ── */
        .prose h2, .prose h3 {{ scroll-margin-top: 5rem; }}

        /* ── Code blocks: Bauhaus charcoal bg, no border-radius, highlight.js override ── */
        .prose pre {{
            background-color: #2C2C2C !important;
            border-radius: 0 !important;
            padding: 1.25rem;
            overflow-x: auto;
            font-size: 0.875rem;
            line-height: 1.7;
            font-family: 'Courier New', Courier, monospace;
            margin: 1.5rem 0;
        }}
        .prose pre code {{
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.875rem;
            background: transparent !important;
            padding: 0;
            /* Light base colour so code stays readable on the charcoal bg even for
               languages highlight.js doesn't tokenize (e.g. vcl). Recognized
               languages still get their own token colours from the hljs theme. */
            color: #abb2bf;
        }}
        /* Override highlight.js theme background to match Bauhaus charcoal */
        .hljs {{
            background: #2C2C2C !important;
            border-radius: 0 !important;
        }}

        /* ── Inline code: charcoal bg, yellow text, no border-radius ── */
        .prose code {{
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85em;
        }}
        .prose :not(pre) > code {{
            background-color: #2C2C2C;
            color: #F1BC1B;
            padding: 0.2em 0.45em;
            border-radius: 0;
        }}

        /* ── Step-code: same charcoal/yellow style for UI path chips ── */
        .step-code {{
            background: #2C2C2C;
            color: #F1BC1B;
            padding: 0.2rem 0.5rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.875rem;
            border-radius: 0;
        }}

        /* ── Tables: no border-radius, charcoal header ── */
        .prose table {{ width: 100%; border-collapse: collapse; }}
        .prose th {{
            background-color: #2C2C2C;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #2C2C2C;
            border-radius: 0;
        }}
        .prose td {{ padding: 0.75rem 1rem; border-bottom: 1px solid #e5e5e5; }}
        .prose tr:hover {{ background-color: #fafafa; }}

        /* ── Blockquote: Bauhaus style — border-4 yellow, square, no left-only border ── */
        .prose blockquote {{
            border: 4px solid #F1BC1B;
            background-color: #ffffff;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
            border-radius: 0;
        }}
        .prose blockquote p {{ margin: 0; }}

        /* ── Lists: hexagon bullets ── */
        .prose ul {{ list-style: none; padding-left: 0; }}
        .prose ul li {{
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 0.4rem;
            line-height: 1.7;
        }}
        .prose ul li::before {{
            content: "";
            position: absolute;
            left: 0;
            top: 0.45em;
            width: 10px;
            height: 10px;
            background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12 2L21 7V17L12 22L3 17V7L12 2Z' stroke='%23F26423' stroke-width='2.5' stroke-linejoin='miter'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
        }}
        .prose ol {{ list-style-type: decimal; padding-left: 1.5rem; }}
        .prose li {{ margin-bottom: 0.4rem; }}

        /* ── Headings ── */
        .prose h2 {{
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-top: 1.5rem;
            border-top: 4px solid #F1BC1B;
            color: #2C2C2C;
        }}
        .prose h3 {{
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            color: #2C2C2C;
        }}
        .prose h4 {{
            font-size: 1.05rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #2C2C2C;
        }}
        .prose p {{ margin-bottom: 1rem; line-height: 1.75; }}
        .prose a {{ color: #F26423; text-decoration: none; }}
        .prose a:hover {{ color: #e34613; text-decoration: underline; }}
        .prose hr {{ border: none; border-top: 1px solid #e5e5e5; margin: 2rem 0; }}

        /* ── Callout boxes: Bauhaus — border-4 yellow, white bg, icon + title ── */
        .callout-box {{
            background-color: #ffffff;
            border: 4px solid #F1BC1B;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
        }}
        .callout-warning {{
            border-color: #F26423;
        }}
        .callout-important {{
            border-color: #2C2C2C;
        }}
        .callout-box .callout-inner {{
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }}
        .callout-box .callout-title {{
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 0.25rem;
        }}
        .callout-box .callout-body {{
            font-size: 0.875rem;
            line-height: 1.6;
            color: rgba(44,44,44,0.85);
        }}

        /* ── TOC sidebar ── */
        .toc-sidebar a {{
            color: #818181;
            font-size: 0.8125rem;
            text-decoration: none;
            display: block;
            padding: 0.25rem 0;
            transition: color 0.15s;
        }}
        .toc-sidebar a:hover {{ color: #F26423; }}
        .toc-sidebar ul {{ list-style: none; padding-left: 0; }}
        .toc-sidebar > ul > li > ul {{ padding-left: 0.75rem; }}
        /* Override hexagon bullets inside TOC */
        .toc-sidebar ul li::before {{ display: none; }}
        .toc-sidebar ul li {{ padding-left: 0; }}
    </style>
</head>

<body class="font-sans bg-off-white text-charcoal">

    <!-- Shared header — single source of truth -->
    <script src="includes/header.js"></script>
    <!-- Contributor widget (reads locally-baked contributors.json) -->
    <script src="includes/contributors.js"></script>

    <!-- Breadcrumb — orange › separators -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl xl:max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-2 text-sm flex-wrap">
                {breadcrumb}
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl xl:max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-8">

            <!-- Left Sidebar (desktop) -->
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <div class="sticky top-24">
                    {sidebar}
                </div>
            </aside>

            <!-- Article — square corners, border-t-4 orange accent -->
            <main class="flex-1 min-w-0">
                <article class="bg-white shadow-sm border border-gray-100 border-t-4 border-t-orange p-8 md:p-12">
                    <!-- Title block -->
                    <div class="mb-8">
                        {badge}
                        <h1 class="text-3xl md:text-4xl font-extrabold text-charcoal mt-3 mb-3">{title}</h1>
                        <p class="text-charcoal-300 text-lg mb-4">{description}</p>
                        <!-- Meta pills -->
                        <div class="flex flex-wrap gap-2">{meta_bar}</div>
                    </div>

                    <hr class="border-0 border-t border-gray-100 mb-8">

                    <!-- Content -->
                    <div class="prose max-w-none">
                        {content_html}
                    </div>
                </article>
                <!-- Contributor + edit widget injected here (inside the content column) -->
                <div id="dev-contributors"></div>
            </main>

            <!-- TOC right sidebar (desktop) -->
            <aside class="hidden xl:block w-56 flex-shrink-0">
                <div class="sticky top-24 toc-sidebar">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-charcoal-300 mb-4">On this page</h4>
                    {toc_html}
                </div>
            </aside>
        </div>
    </div>

    <!-- ================================================================
         FOOTER — matches module-catalog.html multi-column layout exactly
         ================================================================ -->
    <footer class="relative pt-12 bg-white border-t border-charcoal mt-16">
        <div class="max-w-7xl xl:max-w-[90rem] mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div>
                <a href="index.html" class="inline-flex items-center no-underline">
                    <svg width="30" height="33" viewBox="0 0 30 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 4.06492H29.6763V31.8882C29.6763 32.502 29.1713 33 28.5487 33H1.12762C0.505079 33 0 32.502 0 31.8882V4.06492Z" fill="#34323A"/>
                        <path d="M1.26857 0H28.4078C29.1066 0 29.6763 0.561678 29.6763 1.25075V4.06492H0V1.25075C0 0.561678 0.569682 0 1.26857 0Z" fill="#C9C9C9"/>
                        <path d="M2.37269 3.0458C2.94031 3.0458 3.40046 2.59211 3.40046 2.03246C3.40046 1.47281 2.94031 1.01913 2.37269 1.01913C1.80506 1.01913 1.34491 1.47281 1.34491 2.03246C1.34491 2.59211 1.80506 3.0458 2.37269 3.0458Z" fill="#848484"/>
                        <path d="M5.28571 3.0458C5.85334 3.0458 6.31349 2.59211 6.31349 2.03246C6.31349 1.47281 5.85334 1.01913 5.28571 1.01913C4.71809 1.01913 4.25793 1.47281 4.25793 2.03246C4.25793 2.59211 4.71809 3.0458 5.28571 3.0458Z" fill="#848484"/>
                        <path d="M14.7883 7.46973L4.90405 13.0923V24.349L7.54104 25.8487V14.5978L14.7883 10.4692L22.0415 14.5978V25.8487L24.6785 24.349V13.0923L14.7883 7.46973Z" fill="#F1BC1B"/>
                        <path d="M16.0862 26.2367L14.7883 26.9779L13.4492 26.2135V14.233L10.178 16.0975V27.3485L13.4492 29.213L14.7883 29.9773L16.0862 29.2362L19.4045 27.3485V16.0975L16.0862 14.2098V26.2367Z" fill="#F1BC1B"/>
                    </svg>
                    <span class="ml-3 text-xl font-bold text-charcoal">Developer Documentation</span>
                </a>
            </div>

            <div class="grid grid-cols-12 md:gap-x-8 gap-y-12 lg:mt-12">
                <div class="col-span-12 lg:col-span-4">
                    <p class="hidden lg:block text-xs text-charcoal sm:text-sm">Comprehensive developer documentation for Magento 2.4.7+ — tutorials, architecture deep dives, module references, and production-ready patterns built by the community, for the community.</p>
                    <ul class="mt-6 hidden lg:flex items-center gap-3">
                        <li class="pl-0">
                            <a href="https://github.com/magento/magento2" class="p-2 md:p-0" aria-label="GitHub">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="text-xs col-span-6 md:col-span-3 lg:col-span-2">
                    <span class="uppercase font-semibold text-charcoal">Guides</span>
                    <div class="mt-6">
                        <ul class="space-y-3.5 md:space-y-3 text-charcoal list-none">
                            <li class="pl-0"><a href="tutorials.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Tutorials</a></li>
                            <li class="pl-0"><a href="how-to-guides.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">How-To Guides</a></li>
                            <li class="pl-0"><a href="architecture.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Architecture</a></li>
                            <li class="pl-0"><a href="learning-paths.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Learning Paths</a></li>
                            <li class="pl-0"><a href="references.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">References</a></li>
                        </ul>
                    </div>
                </div>

                <div class="text-xs col-span-6 md:col-span-3 lg:col-span-2">
                    <span class="uppercase font-semibold text-charcoal">Modules</span>
                    <div class="mt-6">
                        <ul class="space-y-3.5 md:space-y-3 text-charcoal list-none">
                            <li class="pl-0"><a href="module-catalog.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Catalog</a></li>
                            <li class="pl-0"><a href="module-checkout.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Checkout</a></li>
                            <li class="pl-0"><a href="module-sales.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Sales</a></li>
                            <li class="pl-0"><a href="module-customer.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Customer</a></li>
                            <li class="pl-0"><a href="module-quote.html" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Quote</a></li>
                        </ul>
                    </div>
                </div>

                <div class="text-xs col-span-6 md:col-span-3 lg:col-span-2">
                    <span class="uppercase font-semibold text-charcoal">Community</span>
                    <div class="mt-6">
                        <ul class="space-y-3.5 md:space-y-3 text-charcoal list-none">
                            <li class="pl-0"><a href="https://community.magento.com/" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Magento Community</a></li>
                            <li class="pl-0"><a href="https://magento.stackexchange.com/" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Stack Exchange</a></li>
                            <li class="pl-0"><a href="https://marketplace.magento.com/" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Marketplace</a></li>
                            <li class="pl-0"><a href="https://github.com/magento/magento2" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">GitHub</a></li>
                        </ul>
                    </div>
                </div>

                <div class="text-xs col-span-6 md:col-span-3 lg:col-span-2">
                    <span class="uppercase font-semibold text-charcoal">Support</span>
                    <div class="mt-6">
                        <ul class="space-y-3.5 md:space-y-3 text-charcoal list-none">
                            <li class="pl-0"><a href="https://github.com/magento/magento2/issues" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Report Issues</a></li>
                            <li class="pl-0"><a href="https://community.magento.com/" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Community Resources</a></li>
                            <li class="pl-0"><a href="https://developer.adobe.com/commerce/" class="block py-2 transition-colors no-underline hover:text-orange hover:bg-off-white px-2 -mx-2">Adobe Commerce Docs</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-12 border-t pt-6 pb-16 border-charcoal">
                <p class="text-xs text-charcoal">
                    &copy; 2026 Magento 2 Developer Documentation. Magento&reg; is a registered trademark of Adobe Inc.
                </p>
                <p class="mt-6 text-xs text-charcoal">
                    This documentation provides guidance for Magento 2 developers and extension builders. For merchant documentation, visit the <a href="index.html" class="text-orange hover:text-orange-600 transition-colors">merchant documentation</a>.
                </p>
            </div>
        </div>
    </footer>

    <!-- Initialize highlight.js -->
    <script>hljs.highlightAll();</script>

</body>
</html>'''


def style_content(html):
    """Apply Bauhaus-matching styles to rendered HTML elements.

    Principles:
    - No border-radius anywhere
    - Tables: no wrapper radius, charcoal header
    - Callouts: border-4 yellow/orange/charcoal, white bg, icon + title (not border-left style)
    - Lists: hexagon bullets via CSS (handled in <style> block above)
    """

    # ── Tables: no rounded wrapper, plain border ──
    html = html.replace(
        '<table>',
        '<div class="overflow-x-auto my-6 border border-gray-200"><table class="min-w-full">'
    )
    html = html.replace('</table>', '</table></div>')

    # ── Callout boxes: Bauhaus style with icon + title ──
    # Map callout type -> (border CSS class, icon SVG path d, title label)
    callout_configs = {
        'Note:': (
            'callout-box',
            'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
            'Note',
            'fill'
        ),
        'Tip:': (
            'callout-box',
            'M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z',
            'Tip',
            'fill'
        ),
        'Warning:': (
            'callout-box callout-warning',
            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'Warning',
            'stroke'
        ),
        'Important:': (
            'callout-box callout-important',
            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'Important',
            'stroke'
        ),
        'Caution:': (
            'callout-box callout-warning',
            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'Caution',
            'stroke'
        ),
        'Best Practice:': (
            'callout-box',
            'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'Best Practice',
            'stroke'
        ),
    }

    for trigger, (box_class, icon_path, label, icon_mode) in callout_configs.items():
        # Build the Bauhaus callout replacement
        if icon_mode == 'fill':
            icon_svg = (
                f'<svg class="w-5 h-5 text-orange flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">'
                f'<path fill-rule="evenodd" d="{icon_path}" clip-rule="evenodd"/>'
                f'</svg>'
            )
        else:
            icon_svg = (
                f'<svg class="w-5 h-5 text-orange flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                f'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{icon_path}"/>'
                f'</svg>'
            )

        open_callout = (
            f'<div class="{box_class}">'
            f'<div class="callout-inner">'
            f'{icon_svg}'
            f'<div>'
            f'<p class="callout-title">{label}</p>'
            f'<div class="callout-body">'
        )
        close_callout = '</div></div></div>'

        # Handle both: <blockquote>\n<p>Note: ... and <blockquote>\n<p><strong>Note:</strong> ...
        # Pattern 1: plain trigger text
        p1_open = f'<blockquote>\n<p>{trigger}'
        p1_close = '</p>\n</blockquote>'
        p1_close_alt = '</blockquote>'

        # Pattern 2: bold trigger
        p2_open = f'<blockquote>\n<p><strong>{trigger}</strong>'

        # Replace blockquotes that start with the trigger keyword
        # We do a regex replacement to catch the full blockquote block
        def make_callout_replacer(open_tag, label_text, box_cls, icon):
            def replacer(m):
                inner = m.group(1)
                # Strip the trigger text from the start of the inner content
                inner = re.sub(r'^<p>' + re.escape(trigger), '<p>', inner, count=1)
                inner = re.sub(r'^<p><strong>' + re.escape(trigger) + r'</strong>', '<p>', inner, count=1)
                inner = inner.strip()
                return (
                    f'<div class="{box_cls}">'
                    f'<div class="callout-inner">'
                    f'{icon}'
                    f'<div>'
                    f'<p class="callout-title">{label_text}</p>'
                    f'<div class="callout-body">{inner}</div>'
                    f'</div></div></div>'
                )
            return replacer

        # Match full <blockquote>...</blockquote> blocks that start with this trigger
        escaped_trigger = re.escape(trigger)
        pattern = re.compile(
            r'<blockquote>\s*(<p>' + escaped_trigger + r'.*?|<p><strong>' + escaped_trigger + r'</strong>.*?)</blockquote>',
            re.DOTALL
        )
        html = pattern.sub(make_callout_replacer(trigger, label, box_class, icon_svg), html)

    return html


def build_link_map():
    """Build a mapping of title/keywords to HTML filenames for link resolution."""
    link_map = {}

    md_files = glob.glob(f'{MD_DIR}/guides/*/*.md') + glob.glob(f'{MD_DIR}/modules/*/*.md')

    for md_path in md_files:
        with open(md_path) as f:
            meta, _ = parse_frontmatter(f.read())

        html_file = md_to_html_filename(md_path)
        title = meta.get('title', '')

        # Map by title (lowercased)
        if title:
            link_map[title.lower()] = html_file

        # Map by filename stem
        stem = os.path.basename(md_path).replace('.md', '')
        link_map[stem.lower()] = html_file

        # Map by readable name
        readable = stem.replace('-', ' ').lower()
        link_map[readable] = html_file

    return link_map


def update_overview_pages(link_map):
    """Update href='#' links in existing overview pages to point to real pages."""
    overview_files = glob.glob(f'{OUT_DIR}/*.html')

    # Build context-aware link resolution for each file
    for filepath in overview_files:
        with open(filepath, 'r') as f:
            content = f.read()

        original = content

        # Find all href="#" with surrounding context to determine target
        def resolve_link(match):
            full_match = match.group(0)
            before = match.group(1)
            after = match.group(2)

            # Extract nearby text for context
            context = (before + after).lower()

            # Try to match against known titles/names
            best_match = None
            best_score = 0

            for key, target in link_map.items():
                if len(key) < 3:
                    continue
                if key in context:
                    score = len(key)
                    if score > best_score:
                        best_score = score
                        best_match = target

            if best_match:
                return full_match.replace('href="#"', f'href="{best_match}"')
            return full_match

        # Match href="#" with surrounding context (200 chars before and after)
        pattern = r'(.{0,200})href="#"(.{0,200})'
        content = re.sub(pattern, resolve_link, content)

        if content != original:
            count = original.count('href="#"') - content.count('href="#"')
            with open(filepath, 'w') as f:
                f.write(content)
            print(f"  Updated {os.path.basename(filepath)}: {count} links resolved, {content.count('href=\"#\"')} remaining")


def _gh_get(url):
    """GET a GitHub API URL and return parsed JSON (raises on failure)."""
    req = urllib.request.Request(url, headers={
        'User-Agent': 'MagentoOpenSource-DevDocs-Generator',
        'Accept': 'application/vnd.github+json',
    })
    with urllib.request.urlopen(req, timeout=15) as resp:
        return json.load(resp)


def write_contributors_json(out_dir, limit=3):
    """Bake the documentation-platform contributors into contributors.json (consumed
    client-side by includes/contributors.js). Combines the developer/ CONTENT
    (magentoopensource/docs) with the docs-website repo (the site + generator that build
    and present the docs), aggregated per author. Best-effort: on any failure writes an
    empty list so the widget simply hides."""
    agg = {}

    def add(login, avatar, html, n):
        if not login:
            return
        entry = agg.setdefault(login, {
            'login': login, 'avatar_url': avatar or '', 'html_url': html or '#', 'contributions': 0,
        })
        entry['contributions'] += n
        if avatar and not entry['avatar_url']:
            entry['avatar_url'] = avatar
        if html and entry['html_url'] == '#':
            entry['html_url'] = html

    data = []
    try:
        # (1) developer/ CONTENT authorship in magentoopensource/docs
        for c in _gh_get('https://api.github.com/repos/magentoopensource/docs/commits?path=developer&per_page=100'):
            a = c.get('author') or {}
            add(a.get('login'), a.get('avatar_url'), a.get('html_url'), 1)
        # (2) docs-website repo (the generator + site that build/present the docs)
        for c in _gh_get('https://api.github.com/repos/magentoopensource/docs-website/contributors?per_page=100'):
            if c.get('type') == 'User':
                add(c.get('login'), c.get('avatar_url'), c.get('html_url'), c.get('contributions', 0))
        data = sorted(agg.values(), key=lambda x: x['contributions'], reverse=True)[:limit]
        print(f"  Contributors: {len(data)} (dev-docs content + docs-website, combined)")
    except Exception as e:
        print(f"  Contributors: fetch failed ({e}) — writing empty list (widget hides)")
    with open(os.path.join(out_dir, 'contributors.json'), 'w') as f:
        json.dump(data, f)


if __name__ == '__main__':
    print("=" * 60)
    print("Building HTML pages from markdown")
    print("=" * 60)

    # Collect all markdown files
    md_files = sorted(glob.glob(f'{MD_DIR}/guides/*/*.md') + glob.glob(f'{MD_DIR}/modules/*/*.md'))
    print(f"\nFound {len(md_files)} markdown files to convert\n")

    os.makedirs(OUT_DIR, exist_ok=True)

    # Bake the contributor widget data (best-effort; widget hides if empty).
    write_contributors_json(OUT_DIR)

    generated = 0
    errors = 0

    for md_path in md_files:
        try:
            html_filename, html_content = build_page(md_path)
            out_path = os.path.join(OUT_DIR, html_filename)
            with open(out_path, 'w') as f:
                f.write(html_content)
            rel = os.path.relpath(md_path, MD_DIR)
            print(f"  OK  {rel} -> {html_filename}")
            generated += 1
        except Exception as e:
            print(f"  FAIL {md_path}: {e}")
            errors += 1

    print(f"\n{generated} pages generated, {errors} errors")

    # Build link map and update overview pages
    print(f"\nUpdating overview page links...")
    link_map = build_link_map()
    print(f"  Built link map with {len(link_map)} entries")
    update_overview_pages(link_map)

    # Summary
    total_files = len(glob.glob(f'{OUT_DIR}/*.html'))
    print(f"\nTotal files in {OUT_DIR}: {total_files}")
    print("Done!")
