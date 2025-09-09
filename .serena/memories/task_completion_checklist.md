# Task Completion Checklist

## When Implementing Features

### Code Quality
- [ ] Follow existing Laravel 10 structure patterns
- [ ] Use dependency injection with proper type hints
- [ ] Add PHPDoc blocks for all new methods/properties
- [ ] Follow existing naming conventions (descriptive names)
- [ ] Check sibling files for consistent approaches

### Frontend Changes
- [ ] Use existing Tailwind CSS classes and color palette
- [ ] Follow existing Blade component patterns
- [ ] Test responsive design (mobile/desktop)
- [ ] Ensure dark mode support if applicable
- [ ] Use Alpine.js for JavaScript interactions

### Before Committing
- [ ] Run `php artisan cache:clear` to test cache behavior
- [ ] Run `npm run build` to ensure assets compile properly
- [ ] Test both development servers (`npm run dev` + `php artisan serve`)
- [ ] Verify content syncing works (`bash bin/checkout_latest_docs.sh`)

### Testing Notes
⚠️ **No test suite currently exists** - this project has PHPUnit configured but no actual tests written.

### Documentation Updates
- [ ] Update CLAUDE.md if architecture changes
- [ ] Only create documentation files if explicitly requested
- [ ] Consider if changes affect the setup process

### Environment Considerations
- [ ] Ensure changes work without database
- [ ] Test with/without optional services (Algolia, Torchlight)
- [ ] Verify static content serving works properly

### Performance
- [ ] Consider cache implications of changes
- [ ] Ensure external content sync still works
- [ ] Test asset compilation and loading