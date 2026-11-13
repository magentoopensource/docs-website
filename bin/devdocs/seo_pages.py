"""
Search titles and descriptions for the generated developer pages.

The source Markdown (content repo) has no meta description for the 45 module pages and a wrong-length one
for most guides, and its titles are too long for a search result. The content repo is not ours to edit from
here, so the generator reads these instead. The page's own H1 (the front matter title) is not changed.

Each description was written from the headings of the page it describes. Lengths are enforced at build time
(seo_head.check_description), so a bad edit fails the build rather than shipping.
"""

# key: path of the source file under developer/  ->  (title shown in search, meta description)
GUIDES = {
    'guides/explanations/b2b-features.md': (
        'B2B Features Development',
        'How Adobe Commerce B2B works for developers: company accounts, shared catalogs, requisition lists, quick order, quote negotiation and purchase order approval.'),
    'guides/explanations/eav-system.md': (
        'EAV System Architecture',
        'How the Entity-Attribute-Value system works: table structure, custom attributes and entities, attribute sets, flat tables, and how to keep EAV queries fast.'),
    'guides/explanations/graphql-resolver-patterns.md': (
        'GraphQL Resolver Patterns',
        'Write GraphQL resolvers for Magento: schema.graphqls syntax, query and mutation resolvers, data providers, batch resolvers, custom types and authorization.'),
    'guides/explanations/indexer-system.md': (
        'Indexer System Deep Dive',
        'How indexing works: indexer architecture, changelog tables and triggers, update modes, building a custom indexer, full and partial reindex, and debugging.'),
    'guides/explanations/message-queue-architecture.md': (
        'Message Queue Architecture',
        'How Magento message queues work: RabbitMQ versus MySQL queues, writing consumers and publishers, retry and dead letter handling, tuning and monitoring.'),
    'guides/explanations/service-contracts-repositories.md': (
        'Service Contracts vs Repositories',
        'What service contracts and repositories are, when to use them instead of models, how SearchCriteria filtering works, and how to keep your API stable.'),
    'guides/how-to/admin-ui-components.md': (
        'Building Admin UI Components',
        'Build admin grids and forms with UI components: component architecture, grid and form implementation, mass actions, inline editing and AJAX operations.'),
    'guides/how-to/cicd-deployment.md': (
        'CI/CD Deployment Pipelines',
        'Set up a deployment pipeline for Magento: build artifacts, static content strategies, zero-downtime releases, health checks, rollback and a GitLab CI example.'),
    'guides/how-to/cron-jobs.md': (
        'Cron Jobs Implementation',
        'Build reliable scheduled tasks: crontab.xml configuration, cron job classes, cron groups, error handling and logging, testing, and production deployment.'),
    'guides/how-to/email-templates.md': (
        'Email Templates Customization',
        'Customise transactional emails: how templates are structured, creating your own, template variables, SMTP setup, queue management, testing and performance.'),
    'guides/how-to/erp-integration.md': (
        'ERP Integration Patterns',
        'Connect Magento to an ERP: webhooks versus polling, idempotency keys, bulk API use, order and inventory sync patterns, retry logic and audit logging.'),
    'guides/how-to/full-page-cache-strategy.md': (
        'Full Page Cache Strategy',
        'Make Magento pages fast with full page cache: cacheable blocks, ESI holes, private content, Varnish VCL, cache warming and the performance results.'),
    'guides/how-to/import-export.md': (
        'Import/Export System Deep Dive',
        'How import and export work: the architecture, writing a custom entity importer, scheduled imports, large files, validation, performance and error recovery.'),
    'guides/how-to/layout-xml-deep-dive.md': (
        'Layout XML Deep Dive',
        'Understand Magento layout XML: handles, containers versus blocks, move and remove operations, UI components, debugging, and production-grade patterns.'),
    'guides/how-to/multi-store-setup.md': (
        'Multi-Store Configuration',
        'Set up several stores in one Magento install: the website, store and store view hierarchy, scoped configuration, themes, URLs, catalogs and CLI commands.'),
    'guides/how-to/security-checklist.md': (
        'Security Checklist for Custom Modules',
        'A security checklist for custom modules: protecting against XSS, CSRF and SQL injection, using ACL correctly, PCI considerations, and how to test each one.'),
    'guides/how-to/testing-strategies.md': (
        'Testing Strategies',
        'Test Magento code at every level: the testing pyramid, PHPUnit unit tests, integration and API functional tests, MFTF, coverage targets and CI/CD.'),
    'guides/references/cli-command-reference.md': (
        'CLI Command Reference',
        'A lookup guide to bin/magento: commands by category, a cheat sheet table, common workflows such as cache, indexing and deploy, and writing custom commands.'),
    'guides/references/upgrade-guide-247-248.md': (
        'Upgrade Guide 2.4.6 to 2.4.8',
        'Upgrade from 2.4.6 to 2.4.7 or 2.4.8: version compatibility, breaking changes, PHP 8.2 and 8.3 notes, MySQL 8 requirements, Composer steps and a checklist.'),
    'guides/tutorials/custom-payment-method.md': (
        'Custom Payment Method Development',
        'Build a custom payment method: payment structure, gateway commands, request builders, response handlers and validators, HTTP client, vault and testing.'),
    'guides/tutorials/custom-shipping-method.md': (
        'Custom Shipping Method Development',
        'Build a custom shipping method: the carrier model, rate calculation, tracking integration, multi-warehouse shipping, shipping restrictions and testing.'),
    'guides/tutorials/declarative-schema-data-patches.md': (
        'Declarative Schema & Data Patches',
        'Manage the database with db_schema.xml and patches: table syntax, the whitelist file, data and schema patches, dependencies, legacy script migration, rollback.'),
    'guides/tutorials/docker-development-environment.md': (
        'Docker Development Environment Setup',
        'Build a Docker development environment for Magento step by step: Compose setup, PHP-FPM image, Nginx, MySQL, search, Redis, Xdebug and installing Magento.'),
    'guides/tutorials/plugin-system-deep-dive.md': (
        'Plugin System Deep Dive',
        'Learn Magento plugins: before, after and around types, registering them in di.xml, execution order, plugins versus observers, common mistakes and performance.'),
    'guides/tutorials/product-types.md': (
        'Product Types Development',
        'Create a custom product type: the product type architecture, registering the type, price calculation, stock handling, admin setup, rendering and checkout.'),
}

# Module pages share one structure; the description is built from the doc type. Written from the headings
# of all five modules' pages (catalog, checkout, customer, quote, sales); only claims true of all five are made.
MODULE_TYPES = {
    'overview': ('Overview', 'An overview of {m} for Magento Open Source developers: its purpose, main features, dependencies, configuration and how to test it.'),
    'architecture': ('Architecture', 'How {m} is built: its layers, main components, design patterns and extension points, explained for Magento Open Source developers.'),
    'execution-flows': ('Execution Flows', 'Step-by-step traces of the main {m} operations, showing which classes, plugins and events run, in order, for Magento Open Source developers.'),
    'plugins-and-observers': ('Plugins & Observers', 'The plugins and event observers in {m}: what each extension point does, when to use a plugin or an observer, and how to test your own.'),
    'integrations': ('Integrations', 'How {m} works with other Magento modules and services: the dependencies, data flow and integration points Magento Open Source developers need.'),
    'anti-patterns': ('Anti-Patterns', 'Common mistakes when working with {m}, with the problem code, why it fails and the correct approach, for Magento Open Source developers.'),
    'known-issues': ('Known Issues', 'Known bugs, limits and quirks in {m}, with symptoms, causes and workarounds, so Magento Open Source developers can spot and avoid them early.'),
    'performance': ('Performance', 'How to make {m} faster: database and query tuning, caching and scaling advice, written for developers running Magento Open Source stores.'),
    'version-compatibility': ('Version Compatibility', 'Which {m} APIs, dependencies and deprecations apply in Magento Open Source 2.4.7, plus upgrade advice, checked against the source code.'),
}

# Short titles for pages that are not guides or module pages (and not generated from the module table above).
