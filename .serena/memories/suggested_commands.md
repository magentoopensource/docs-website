# Essential Development Commands

## Initial Setup (First Time Only)
```bash
bash bin/setup.sh          # Complete project setup including dependencies and docs
```

## Daily Development Workflow
```bash
# Start both servers (use separate terminals)
npm run dev                 # Start Vite dev server for asset compilation with hot reload
php artisan serve           # Start Laravel development server (usually http://localhost:8000)
```

## Content Management
```bash
bash bin/checkout_latest_docs.sh    # Sync documentation content from external repo
bash bin/update.sh                  # Update dependencies AND sync latest docs
```

## Asset Building
```bash
npm run build               # Build production assets
npm install                 # Install/update Node dependencies
composer install            # Install/update PHP dependencies
```

## Laravel Commands
```bash
php artisan cache:clear     # Clear application cache (useful when content not updating)
php artisan route:list      # List all application routes
php artisan sitemap:generate # Generate sitemap (custom command)
php artisan key:generate    # Generate new application key
```

## Testing
**Note**: This project currently has NO test suite configured. PHPUnit is available but no tests exist.

## Environment Setup
- Copy `.env.example` to `.env` if not exists
- Set `TORCHLIGHT_TOKEN` in .env (get free token from https://torchlight.dev/)
- Optional: Set `ALGOLIA_ID` and `ALGOLIA_SEARCH_KEY` for search functionality