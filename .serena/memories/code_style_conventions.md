# Code Style & Conventions

## PHP Style (Laravel 10 Structure)
- **Type declarations**: Always use explicit return types and parameter types
- **Constructor injection**: Use dependency injection in constructors with type hints
- **PHPDoc**: Use comprehensive PHPDoc blocks for class properties and methods
- **Constants**: Use class constants with UPPER_CASE naming (e.g., `DEFAULT_META_TITLE`)
- **Properties**: Use `protected` visibility with PHPDoc type annotations
- **Naming**: Use descriptive method names (e.g., `showRootPage()`, `getDocVersions()`)

## Laravel 10 Structure (Not migrated to Laravel 11+ structure)
- Middleware in `app/Http/Middleware/`
- Service providers in `app/Providers/`
- No `bootstrap/app.php` configuration file
- Middleware registration in `app/Http/Kernel.php`
- Exception handling in `app/Exceptions/Handler.php`
- Console commands in `app/Console/Kernel.php`

## Frontend Conventions
- **Tailwind CSS**: v3 classes with extensive custom color palette
- **Blade templates**: Organized in `resources/views/` with component structure
- **JavaScript**: Alpine.js for interactions, minimal custom JS
- **Asset organization**: CSS in `resources/css/`, JS in `resources/js/`

## File Organization
- Controllers follow single responsibility principle
- Static content in `public/` with organized subdirectories
- Documentation content external to main repo (synced via scripts)
- Custom Markdown parsing with `app/Markdown/` classes

## Caching Strategy
- 5-minute cache for individual pages
- 1-hour cache for documentation index
- Uses Laravel's cache system with descriptive cache keys