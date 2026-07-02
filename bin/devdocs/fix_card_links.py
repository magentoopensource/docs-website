#!/usr/bin/env python3
"""
Fix card-style href="#" links by matching the badge/span text inside the card.
Also handles guide listing cards on the index page by matching the title h3 text.
"""
import os
import re
import glob

OUT_DIR = os.environ.get("DEVDOCS_OUT_DIR") or os.path.normpath(
    os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "..", "..", "deploy-magento-association")
)

# Badge text -> doc type suffix mapping
BADGE_TO_SUFFIX = {
    'overview': 'overview',
    'architecture': 'architecture',
    'execution-flows': 'execution-flows',
    'plugins-observers': 'plugins-and-observers',
    'integrations': 'integrations',
    'anti-patterns': 'anti-patterns',
    'known-issues': 'known-issues',
    'version-compat': 'version-compatibility',
    'performance': 'performance',
}

# Guide title -> html file
GUIDE_TITLE_MAP = {
    # Tutorials
    'docker development environment': 'guide-tutorial-docker-development-environment.html',
    'docker development environment setup': 'guide-tutorial-docker-development-environment.html',
    'declarative schema': 'guide-tutorial-declarative-schema-data-patches.html',
    'declarative schema &amp; data patches': 'guide-tutorial-declarative-schema-data-patches.html',
    'plugin system deep dive': 'guide-tutorial-plugin-system-deep-dive.html',
    'product types': 'guide-tutorial-product-types.html',
    'product types development': 'guide-tutorial-product-types.html',
    'custom payment method': 'guide-tutorial-custom-payment-method.html',
    'custom payment': 'guide-tutorial-custom-payment-method.html',
    'custom shipping method': 'guide-tutorial-custom-shipping-method.html',
    'custom shipping': 'guide-tutorial-custom-shipping-method.html',

    # How-to
    'admin ui components': 'guide-howto-admin-ui-components.html',
    'admin ui': 'guide-howto-admin-ui-components.html',
    'ci/cd deployment': 'guide-howto-cicd-deployment.html',
    'ci/cd': 'guide-howto-cicd-deployment.html',
    'ci/cd &amp; deployment': 'guide-howto-cicd-deployment.html',
    'cron jobs': 'guide-howto-cron-jobs.html',
    'cron': 'guide-howto-cron-jobs.html',
    'email templates': 'guide-howto-email-templates.html',
    'email': 'guide-howto-email-templates.html',
    'erp integration': 'guide-howto-erp-integration.html',
    'erp': 'guide-howto-erp-integration.html',
    'full page cache strategy': 'guide-howto-full-page-cache-strategy.html',
    'full page cache': 'guide-howto-full-page-cache-strategy.html',
    'full-page cache': 'guide-howto-full-page-cache-strategy.html',
    'import/export': 'guide-howto-import-export.html',
    'import &amp; export': 'guide-howto-import-export.html',
    'import export': 'guide-howto-import-export.html',
    'layout xml deep dive': 'guide-howto-layout-xml-deep-dive.html',
    'layout xml': 'guide-howto-layout-xml-deep-dive.html',
    'multi-store setup': 'guide-howto-multi-store-setup.html',
    'multi-store': 'guide-howto-multi-store-setup.html',
    'security checklist': 'guide-howto-security-checklist.html',
    'security': 'guide-howto-security-checklist.html',
    'testing strategies': 'guide-howto-testing-strategies.html',
    'testing': 'guide-howto-testing-strategies.html',

    # Explanations
    'b2b features': 'guide-explanation-b2b-features.html',
    'b2b': 'guide-explanation-b2b-features.html',
    'eav system': 'guide-explanation-eav-system.html',
    'eav': 'guide-explanation-eav-system.html',
    'graphql resolver patterns': 'guide-explanation-graphql-resolver-patterns.html',
    'graphql': 'guide-explanation-graphql-resolver-patterns.html',
    'graphql resolvers': 'guide-explanation-graphql-resolver-patterns.html',
    'indexer system': 'guide-explanation-indexer-system.html',
    'indexer': 'guide-explanation-indexer-system.html',
    'indexers': 'guide-explanation-indexer-system.html',
    'message queue architecture': 'guide-explanation-message-queue-architecture.html',
    'message queue': 'guide-explanation-message-queue-architecture.html',
    'message queues': 'guide-explanation-message-queue-architecture.html',
    'service contracts &amp; repositories': 'guide-explanation-service-contracts-repositories.html',
    'service contracts': 'guide-explanation-service-contracts-repositories.html',
    'repositories': 'guide-explanation-service-contracts-repositories.html',

    # References
    'cli command reference': 'guide-reference-cli-command-reference.html',
    'cli commands': 'guide-reference-cli-command-reference.html',
    'cli reference': 'guide-reference-cli-command-reference.html',
    'upgrade guide': 'guide-reference-upgrade-guide-247-248.html',
    'upgrade guide 2.4.7': 'guide-reference-upgrade-guide-247-248.html',
    '2.4.7 to 2.4.8': 'guide-reference-upgrade-guide-247-248.html',
}

def fix_module_pages():
    """Fix card links on module overview pages using badge text."""
    for module in ['catalog', 'checkout', 'customer', 'quote', 'sales']:
        filepath = os.path.join(OUT_DIR, f'module-{module}.html')
        if not os.path.exists(filepath):
            continue

        with open(filepath, 'r') as f:
            content = f.read()

        original = content

        # Match: <a href="#" ...> ... badge text ... </a>
        # Use multiline to capture the whole card block
        def replace_card(match):
            card = match.group(0)
            # Find badge text
            badge_match = re.search(r'<span[^>]*>([^<]+)</span>\s*</div>\s*</a>', card)
            if badge_match:
                badge = badge_match.group(1).strip().lower()
                suffix = BADGE_TO_SUFFIX.get(badge)
                if suffix:
                    target = f'module-{module}-{suffix}.html'
                    return card.replace('href="#"', f'href="{target}"', 1)
            # Try h3 title
            h3_match = re.search(r'<h3[^>]*>([^<]+)</h3>', card)
            if h3_match:
                title = h3_match.group(1).strip().lower()
                for key, suffix in BADGE_TO_SUFFIX.items():
                    if key.replace('-', ' ') in title.lower() or title.lower() in key.replace('-', ' '):
                        target = f'module-{module}-{suffix}.html'
                        return card.replace('href="#"', f'href="{target}"', 1)
            return card

        # Match whole card blocks: <a href="#" ...>...</a>
        content = re.sub(
            r'<a\s+href="#"[^>]*class="[^"]*group\s+block[^"]*"[^>]*>.*?</a>',
            replace_card,
            content,
            flags=re.DOTALL
        )

        if content != original:
            with open(filepath, 'w') as f:
                f.write(content)
            fixed = original.count('href="#"') - content.count('href="#"')
            print(f"  module-{module}.html: {fixed} card links fixed")

def fix_index_page():
    """Fix guide listing links on the index page."""
    filepath = os.path.join(OUT_DIR, 'index.html')
    if not os.path.exists(filepath):
        # index.html is a static nav page, not generated by build_html_pages.py.
        # Skip gracefully when running a fresh content-only build.
        return
    with open(filepath, 'r') as f:
        content = f.read()

    original = content

    # Fix guide card links by matching the h3/h4 title inside
    def replace_guide_card(match):
        card = match.group(0)
        # Find title text
        title_match = re.search(r'<(?:h3|h4|span)[^>]*class="[^"]*font-(?:bold|semibold|medium)[^"]*"[^>]*>([^<]+)<', card)
        if title_match:
            title = title_match.group(1).strip().lower()
            # Try exact match first
            if title in GUIDE_TITLE_MAP:
                target = GUIDE_TITLE_MAP[title]
                return card.replace('href="#"', f'href="{target}"', 1)
            # Try partial match
            for key, target in sorted(GUIDE_TITLE_MAP.items(), key=lambda x: -len(x[0])):
                if key in title or title in key:
                    return card.replace('href="#"', f'href="{target}"', 1)
        return card

    # Match card blocks
    content = re.sub(
        r'<a\s+href="#"[^>]*>.*?</a>',
        replace_guide_card,
        content,
        flags=re.DOTALL
    )

    if content != original:
        with open(filepath, 'w') as f:
            f.write(content)
        fixed = original.count('href="#"') - content.count('href="#"')
        print(f"  index.html: {fixed} card links fixed")

def fix_other_pages():
    """Fix remaining guide links on how-to, architecture, references, learning-paths pages."""
    for filepath in glob.glob(f'{OUT_DIR}/*.html'):
        basename = os.path.basename(filepath)
        if basename.startswith('module-') or basename.startswith('guide-'):
            continue

        with open(filepath, 'r') as f:
            content = f.read()

        original = content

        def replace_card(match):
            card = match.group(0)
            # Find title
            title_match = re.search(r'<(?:h3|h4|a)[^>]*class="[^"]*(?:font-bold|font-semibold|text-xl|text-lg)[^"]*"[^>]*>([^<]+)<', card)
            if not title_match:
                title_match = re.search(r'>([^<]{5,60})</(?:h3|h4|a|span)>', card)
            if title_match:
                title = title_match.group(1).strip().lower()
                if title in GUIDE_TITLE_MAP:
                    return card.replace('href="#"', f'href="{GUIDE_TITLE_MAP[title]}"', 1)
                for key, target in sorted(GUIDE_TITLE_MAP.items(), key=lambda x: -len(x[0])):
                    if len(key) >= 4 and key in title:
                        return card.replace('href="#"', f'href="{target}"', 1)
            return card

        content = re.sub(
            r'<a\s+href="#"[^>]*>.*?</a>',
            replace_card,
            content,
            flags=re.DOTALL
        )

        if content != original:
            with open(filepath, 'w') as f:
                f.write(content)
            fixed = original.count('href="#"') - content.count('href="#"')
            remaining = content.count('href="#"')
            print(f"  {basename}: {fixed} links fixed, {remaining} remaining")

if __name__ == '__main__':
    print("Fixing card-style links...\n")
    fix_module_pages()
    fix_index_page()
    fix_other_pages()

    # Final count
    total = 0
    for f in glob.glob(f'{OUT_DIR}/*.html'):
        with open(f) as fh:
            c = fh.read().count('href="#"')
            if c > 0:
                total += c
                print(f"  {os.path.basename(f)}: {c} remaining")
    print(f"\nTotal remaining: {total}")
