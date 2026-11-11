"""
Shared SEO head helpers for the static documentation pages (/developer and /the-core).

One implementation, two callers:
  - build_html_pages.py   (generated developer pages, rebuilt on every deploy)
  - apply_static_seo.py   (hand-written developer pages and the-core, edited in place)

Everything that ends up in <head> for search and sharing comes from here, so the generated and the
hand-written pages cannot drift apart: canonical, Open Graph, Twitter card and JSON-LD.

Decisions this encodes (SEO delivery plan, section 3):
  D1  product name is "Magento Open Source"
  D4  author is the "Magento Association community" Organization, never a person
  D8  "Last updated" is the git commit date of the source file
"""

import datetime
import html
import json
import re
import subprocess

SITE = 'https://docs.magento-opensource.com'
OG_IMAGE = SITE + '/img/merchant-docs-og.png'
LOGO = SITE + '/img/favicon/favicon-196x196.png'
SAME_AS = [
    'https://www.magentoassociation.org/home',
    'https://github.com/magentoopensource',
]
SITE_NAME = 'Magento Open Source Documentation'
ORG_ID = SITE + '/#organization'
WEBSITE_ID = SITE + '/#website'

TITLE_SUFFIX = ' | Magento Open Source'
TITLE_MAX = 60
DESC_MIN, DESC_MAX = 120, 160

START = '<!-- seo:start -->'
END = '<!-- seo:end -->'


def esc(text):
    """Escape for use inside a double-quoted HTML attribute."""
    return html.escape(str(text), quote=True)


def abs_url(path):
    return SITE + path


def with_suffix(page_title):
    """'{Page} | Magento Open Source'. Raises if the result would not fit, so a long title is caught at build time."""
    title = page_title + TITLE_SUFFIX
    if len(title) > TITLE_MAX:
        raise ValueError('title over %d characters (%d): %r' % (TITLE_MAX, len(title), title))
    return title


def check_description(desc, where=''):
    if not (DESC_MIN <= len(desc) <= DESC_MAX):
        raise ValueError('description %d characters, want %d-%d%s: %r' % (len(desc), DESC_MIN, DESC_MAX, ' (' + where + ')' if where else '', desc))
    return desc


def git_date(path, cwd):
    """Committer date of the last commit that touched `path`, as ISO 8601, or None when git cannot say."""
    try:
        out = subprocess.run(
            ['git', 'log', '-1', '--format=%cI', '--', path],
            cwd=cwd, capture_output=True, text=True, timeout=30, check=True,
        ).stdout.strip()
    except (subprocess.SubprocessError, OSError):
        return None
    return out or None


def human_date(iso):
    d = datetime.date.fromisoformat(iso[:10])
    return '%d %s %d' % (d.day, d.strftime('%B'), d.year)


def time_tag(iso):
    return '<time datetime="%s">%s</time>' % (iso[:10], human_date(iso))


def organization():
    return {
        '@type': 'Organization',
        '@id': ORG_ID,
        'name': 'Magento Association',
        'url': 'https://www.magentoassociation.org/home',
        'logo': LOGO,
        'sameAs': SAME_AS,
    }


def website():
    return {
        '@type': 'WebSite',
        '@id': WEBSITE_ID,
        'url': SITE + '/',
        'name': SITE_NAME,
        'inLanguage': 'en',
        'publisher': {'@id': ORG_ID},
    }


def breadcrumb(items):
    """items: [(name, absolute_url), ...] from the site root down to the current page."""
    return {
        '@type': 'BreadcrumbList',
        'itemListElement': [
            {'@type': 'ListItem', 'position': i + 1, 'name': name, 'item': url}
            for i, (name, url) in enumerate(items)
        ],
    }


def tech_article(headline, description, url, date_modified):
    node = {
        '@type': 'TechArticle',
        '@id': url + '#article',
        'headline': headline[:110],
        'description': description,
        'url': url,
        'mainEntityOfPage': url,
        'inLanguage': 'en',
        'image': OG_IMAGE,
        'author': {'@type': 'Organization', 'name': 'Magento Association community'},
        'publisher': {'@id': ORG_ID},
        'isPartOf': {'@id': WEBSITE_ID},
    }
    if date_modified:
        node['dateModified'] = date_modified
    return node


def json_ld(crumbs, headline, description, url, date_modified):
    graph = {
        '@context': 'https://schema.org',
        '@graph': [organization(), website(), breadcrumb(crumbs), tech_article(headline, description, url, date_modified)],
    }
    # "<" is escaped so the block can never end the <script> element early. Still valid JSON.
    body = json.dumps(graph, ensure_ascii=False, indent=2).replace('<', '\\u003c')
    return '<script type="application/ld+json">\n' + body + '\n    </script>'


def seo_block(title, description, canonical_path, headline, crumbs, date_modified, canonical_override=None):
    """Canonical, Open Graph, Twitter card and JSON-LD for one page, wrapped in markers so it can be re-applied."""
    page_url = abs_url(canonical_path)
    canonical = canonical_override or page_url
    t, d = esc(title), esc(description)
    lines = [
        START,
        '<link rel="canonical" href="%s">' % esc(canonical),
        '<meta property="og:type" content="article">',
        '<meta property="og:site_name" content="%s">' % esc(SITE_NAME),
        '<meta property="og:title" content="%s">' % t,
        '<meta property="og:description" content="%s">' % d,
        '<meta property="og:url" content="%s">' % esc(canonical),
        '<meta property="og:image" content="%s">' % esc(OG_IMAGE),
        '<meta name="twitter:card" content="summary_large_image">',
        '<meta name="twitter:title" content="%s">' % t,
        '<meta name="twitter:description" content="%s">' % d,
        '<meta name="twitter:image" content="%s">' % esc(OG_IMAGE),
        json_ld(crumbs, headline, description, canonical, date_modified),
        END,
    ]
    return '\n    '.join(lines)


def strip_block(page_html):
    """Remove a previously applied seo block, so applying twice gives the same file."""
    return re.sub(r'\n?[ \t]*' + re.escape(START) + r'.*?' + re.escape(END), '', page_html, flags=re.S)
