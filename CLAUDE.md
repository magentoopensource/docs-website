# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Initial Setup (first time only)
```bash
bash bin/setup.sh          # Complete project setup including dependencies and docs
```

### Daily Development Workflow
```bash
npm run dev                 # Start Vite development server for asset compilation
php artisan serve           # Start Laravel development server (separate terminal)
```

### Build and Update Commands
```bash
npm run build               # Build production assets
bash bin/update.sh          # Update dependencies and sync latest docs
bash bin/checkout_latest_docs.sh  # Sync documentation content only
```

### Laravel Commands
```bash
php artisan cache:clear     # Clear application cache
php artisan route:list      # List all application routes
php artisan sitemap:generate # Generate sitemap (custom command)
```

## Architecture Overview

This is a Laravel-based documentation website for Magento 2 Merchant Documentation. The application serves static Markdown documentation with dynamic rendering and caching.

### Core Components

**Documentation System** (`app/Documentation.php`):
- Manages versioned documentation from `resources/docs/{version}/` directories
- Handles Markdown parsing with GitHub Flavored Markdown via `app/Markdown/GithubFlavoredMarkdownConverter.php`
- Implements caching for performance (5-minute cache for pages, 1-hour for index)
- Supports front matter metadata extraction

**Content Structure**:
- Docs are pulled from external repository: https://github.com/mage-os/devdocs.git
- Main content in `resources/docs/main/` (synchronized via git submodule approach)
- Supports multiple versions: "main", "develop", "2.4.5" (defined in `Documentation::getDocVersions()`)

**Controllers** (`app/Http/Controllers/DocsController.php`):
- Handles documentation routing and rendering
- Manages category pages with predefined merchant-focused articles
- Provides 404 handling with version suggestions

### Key Features

**Syntax Highlighting**: Uses Torchlight service (requires TORCHLIGHT_TOKEN in .env)
**Search**: Algolia integration (requires ALGOLIA_ID and ALGOLIA_SEARCH_KEY)
**Asset Pipeline**: Vite for CSS/JS compilation with Laravel Vite plugin

### Content Categories
- **Start Selling**: First products, payments, shipping setup
- **Manage Catalog**: Product catalog organization and bulk operations  
- **Handle Orders**: Order processing and fulfillment
- **Grow Store**: Marketing tools, analytics, customer retention
- **Improve UX**: Design, navigation, performance optimization
- **Stay Compliant**: Legal requirements and data protection

### File Structure Notes
- `resources/views/` contains Blade templates
- `resources/css/` and `resources/js/` contain frontend assets
- `routes/web.php` defines all application routes
- Documentation content automatically synchronized from external repository