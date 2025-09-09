# Project Architecture

## Core Structure
```
app/
├── Console/Commands/          # Custom Artisan commands (GenerateSitemap.php)
├── Http/Controllers/          # Route controllers (DocsController.php)
├── Markdown/                  # Custom markdown processing classes
├── Documentation.php          # Main documentation service class
└── [Laravel 10 structure]     # Standard Laravel directories

resources/
├── docs/{version}/            # External documentation content (synced)
├── views/                     # Blade templates
├── css/                       # Tailwind CSS files
└── js/                        # Alpine.js and utility scripts

public/
├── assets/                    # Static assets (images, icons)
└── build/                     # Compiled assets from Vite
```

## Key Components

### Documentation System (`app/Documentation.php`)
- Manages versioned documentation from external repository
- Handles GitHub Flavored Markdown parsing 
- Implements intelligent caching (5-min pages, 1-hour index)
- Supports front matter metadata extraction
- File-based content system (no database)

### Controller Architecture (`app/Http/Controllers/DocsController.php`)
- Single controller handles all documentation routes
- Category-based page organization for merchants
- SEO-optimized with consistent meta tags
- 404 handling with version suggestions

### Asset Pipeline
- **Vite** for modern asset compilation with hot reload
- **Tailwind CSS** with extensive custom design system
- **Alpine.js** for JavaScript interactions
- **PostCSS** with autoprefixer

### External Content Management
- Documentation pulled from: `https://github.com/mage-os/devdocs.git`
- Content synchronized via bash scripts (`bin/checkout_latest_docs.sh`)
- Multiple version support (main, develop, 2.4.5)
- Content stored in `resources/docs/{version}/`

### Caching Strategy
- Laravel cache system for performance
- Differentiated cache durations by content type
- Cache keys follow semantic naming patterns