#!/usr/bin/env python3
"""
Fix remaining href="#" placeholder links in overview pages with targeted replacements.
"""
import os
import re
import glob

OUT_DIR = os.environ.get("DEVDOCS_OUT_DIR") or os.path.normpath(
    os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "..", "..", "deploy-magento-association")
)

# Exact text -> target URL mappings for common navigation links
NAV_REPLACEMENTS = {
    # Header/mobile nav
    '>Modules</a>': ('href="#"', 'href="modules.html"'),
    '>References</a>': ('href="#"', 'href="references.html"'),
    '>Tutorials</a>': ('href="#"', 'href="tutorials.html"'),
    '>How-To Guides</a>': ('href="#"', 'href="how-to-guides.html"'),
    '>Architecture</a>': ('href="#"', 'href="architecture.html"'),
    '>Learning Paths</a>': ('href="#"', 'href="learning-paths.html"'),
    '>Home</a>': ('href="#"', 'href="index.html"'),

    # Footer module links
    '>Catalog</a>': ('href="#"', 'href="module-catalog.html"'),
    '>Checkout</a>': ('href="#"', 'href="module-checkout.html"'),
    '>Sales</a>': ('href="#"', 'href="module-sales.html"'),
    '>Customer</a>': ('href="#"', 'href="module-customer.html"'),
    '>Quote</a>': ('href="#"', 'href="module-quote.html"'),

    # Merchant docs
    '>merchant documentation</a>': ('href="#"', 'href="index.html"'),
}

# Title-based replacements for tutorial/guide cards
TITLE_LINK_MAP = {
    'Docker Development Environment': 'guide-tutorial-docker-development-environment.html',
    'Declarative Schema &amp; Data Patches': 'guide-tutorial-declarative-schema-data-patches.html',
    'Declarative Schema & Data Patches': 'guide-tutorial-declarative-schema-data-patches.html',
    'Plugin System Deep Dive': 'guide-tutorial-plugin-system-deep-dive.html',
    'Product Types Development': 'guide-tutorial-product-types.html',
    'Product Types': 'guide-tutorial-product-types.html',
    'Custom Payment Method': 'guide-tutorial-custom-payment-method.html',
    'Custom Payment Method Development': 'guide-tutorial-custom-payment-method.html',
    'Custom Shipping Method': 'guide-tutorial-custom-shipping-method.html',
    'Custom Shipping Method Development': 'guide-tutorial-custom-shipping-method.html',

    # How-to guides
    'Admin UI Components': 'guide-howto-admin-ui-components.html',
    'CI/CD Deployment': 'guide-howto-cicd-deployment.html',
    'CI/CD &amp; Deployment': 'guide-howto-cicd-deployment.html',
    'Cron Jobs': 'guide-howto-cron-jobs.html',
    'Email Templates': 'guide-howto-email-templates.html',
    'ERP Integration': 'guide-howto-erp-integration.html',
    'Full Page Cache Strategy': 'guide-howto-full-page-cache-strategy.html',
    'Full-Page Cache Strategy': 'guide-howto-full-page-cache-strategy.html',
    'Import/Export': 'guide-howto-import-export.html',
    'Import &amp; Export': 'guide-howto-import-export.html',
    'Layout XML Deep Dive': 'guide-howto-layout-xml-deep-dive.html',
    'Layout XML': 'guide-howto-layout-xml-deep-dive.html',
    'Multi-Store Setup': 'guide-howto-multi-store-setup.html',
    'Security Checklist': 'guide-howto-security-checklist.html',
    'Testing Strategies': 'guide-howto-testing-strategies.html',

    # Explanations
    'B2B Features': 'guide-explanation-b2b-features.html',
    'EAV System': 'guide-explanation-eav-system.html',
    'GraphQL Resolver Patterns': 'guide-explanation-graphql-resolver-patterns.html',
    'Indexer System': 'guide-explanation-indexer-system.html',
    'Message Queue Architecture': 'guide-explanation-message-queue-architecture.html',
    'Service Contracts &amp; Repositories': 'guide-explanation-service-contracts-repositories.html',
    'Service Contracts': 'guide-explanation-service-contracts-repositories.html',

    # References
    'CLI Command Reference': 'guide-reference-cli-command-reference.html',
    'Upgrade Guide': 'guide-reference-upgrade-guide-247-248.html',
    'Upgrade Guide 2.4.7 → 2.4.8': 'guide-reference-upgrade-guide-247-248.html',

    # Module sub-pages
    'Architecture': 'architecture.html',
    'Execution Flows': '',  # context-dependent
    'Plugins &amp; Observers': '',
    'Extension Points': '',
    'Integrations': '',
    'Anti-Patterns': '',
    'Performance': '',
    'Known Issues': '',
    'Version Compatibility': '',
}


def fix_file(filepath):
    """Fix placeholder links in a single HTML file."""
    with open(filepath, 'r') as f:
        content = f.read()

    original = content
    basename = os.path.basename(filepath)

    # 1. Fix navigation links by matching link text
    for text_pattern, (old, new) in NAV_REPLACEMENTS.items():
        # Find href="#" that is followed by the matching text on the same line
        # We need to be careful to only replace the specific instances
        pattern = re.escape(old) + r'([^>]*?)' + re.escape(text_pattern)
        replacement = new + r'\1' + text_pattern
        content = re.sub(pattern, replacement, content)

    # 2. Fix title-based links (tutorial/guide card titles)
    for title, target in TITLE_LINK_MAP.items():
        if not target:
            continue
        # Match: href="#" ... >Title</a>
        # The title might be wrapped in various HTML
        escaped_title = re.escape(title)
        pattern = rf'href="#"(.*?>{escaped_title}</a>)'
        replacement = rf'href="{target}"\1'
        content = re.sub(pattern, replacement, content)

    # 3. Fix module sub-page links on module overview pages
    if basename.startswith('module-') and not basename.count('-') > 1:
        # e.g., module-catalog.html -> fix links to module-catalog-*.html
        module = basename.replace('module-', '').replace('.html', '')
        module_sub_map = {
            'Architecture': f'module-{module}-architecture.html',
            'Execution Flows': f'module-{module}-execution-flows.html',
            'Plugins &amp; Observers': f'module-{module}-plugins-and-observers.html',
            'Extension Points': f'module-{module}-plugins-and-observers.html',
            'Integrations': f'module-{module}-integrations.html',
            'Anti-Patterns': f'module-{module}-anti-patterns.html',
            'Performance': f'module-{module}-performance.html',
            'Known Issues': f'module-{module}-known-issues.html',
            'Version Compatibility': f'module-{module}-version-compatibility.html',
            'Overview': f'module-{module}-overview.html',
        }
        for title, target in module_sub_map.items():
            escaped_title = re.escape(title)
            pattern = rf'href="#"([^>]*>(?:<[^>]*>)*\s*{escaped_title})'
            replacement = rf'href="{target}"\1'
            content = re.sub(pattern, replacement, content)

    # 4. Fix index page module section links
    if basename == 'index.html':
        # Module cards on index
        for mod in ['catalog', 'checkout', 'customer', 'quote', 'sales']:
            mod_cap = mod.capitalize()
            pattern = rf'href="#"([^>]*>{mod_cap}\s*Module)'
            replacement = rf'href="module-{mod}.html"\1'
            content = re.sub(pattern, replacement, content, flags=re.IGNORECASE)

    # 5. Fix absolute path links
    content = content.replace('href="/"', 'href="index.html"')
    content = content.replace('href="/modules"', 'href="modules.html"')
    content = content.replace('href="/guides"', 'href="index.html"')
    content = content.replace('href="/guides/tutorials"', 'href="tutorials.html"')

    if content != original:
        with open(filepath, 'w') as f:
            f.write(content)
        remaining = content.count('href="#"')
        fixed = original.count('href="#"') - remaining
        print(f"  {basename}: {fixed} links fixed, {remaining} remaining")
    else:
        remaining = content.count('href="#"')
        if remaining:
            print(f"  {basename}: no changes, {remaining} # links remain")


if __name__ == '__main__':
    print("Fixing placeholder links...\n")

    html_files = sorted(glob.glob(f'{OUT_DIR}/*.html'))
    for f in html_files:
        fix_file(f)

    # Count totals
    total_remaining = 0
    for f in glob.glob(f'{OUT_DIR}/*.html'):
        with open(f) as fh:
            total_remaining += fh.read().count('href="#"')

    print(f"\nTotal remaining href='#' links: {total_remaining}")
