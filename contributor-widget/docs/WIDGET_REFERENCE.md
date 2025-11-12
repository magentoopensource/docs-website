# Widget Reference Guide

## Quick Links

- **Live Demo:** Open `demo/widget-demo.html` in your browser
- **Widget File:** `public/widget.php`
- **Styles:** `public/css/github-contributors.css`

## Installation

### 1. Basic Setup

```php
<?php
// Include the widget anywhere in your PHP page
include 'path/to/public/widget.php';
?>
```

That's it! The widget will display with default settings.

### 2. Custom Configuration

Set configuration variables **before** including the widget:

```php
<?php
// Customize widget appearance
$widgetTitle = 'Our Top Contributors';
$periodLabel = 'Last 30 Days';
$style = 'grid'; // Options: grid, list, inline
$darkMode = false; // Options: true, false
$showFooter = true; // Options: true, false
$showPeriod = true; // Options: true, false

// Include the widget
include 'path/to/public/widget.php';
?>
```

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `$widgetTitle` | string | `'Top Contributors'` | Widget heading text |
| `$periodLabel` | string | `'This Month'` | Time period label |
| `$style` | string | `'grid'` | Display style: `grid`, `list`, or `inline` |
| `$darkMode` | bool | `false` | Enable dark mode styling |
| `$showFooter` | bool | `true` | Show "View All Contributors" link |
| `$showPeriod` | bool | `true` | Show period label |

## Display Styles

### Grid (Default)
Perfect for showcasing contributors in a responsive grid layout.

```php
<?php
$style = 'grid';
include 'path/to/public/widget.php';
?>
```

**Best for:**
- Main contributor showcase
- Homepage widgets
- Dedicated contributors pages

**Responsive behavior:**
- Mobile (< 640px): 2 columns
- Tablet (640px - 1024px): 3 columns
- Desktop (> 1024px): 5 columns

### List
Vertical list with larger avatars and more detail.

```php
<?php
$style = 'list';
include 'path/to/public/widget.php';
?>
```

**Best for:**
- Sidebar widgets
- Narrow columns
- Detailed contributor info

### Inline
Compact horizontal layout with smaller avatars.

```php
<?php
$style = 'inline';
include 'path/to/public/widget.php';
?>
```

**Best for:**
- Footer areas
- Compact spaces
- Quick contributor preview

## Examples

### Example 1: Homepage Hero Section

```php
<?php
$widgetTitle = 'Meet Our Amazing Contributors';
$periodLabel = 'This Month';
$style = 'grid';
$darkMode = false;
$showFooter = true;

include 'public/widget.php';
?>
```

### Example 2: Sidebar Widget

```php
<?php
$widgetTitle = 'Top Contributors';
$periodLabel = 'Last 7 Days';
$style = 'list';
$showPeriod = true;
$showFooter = false; // Compact sidebar view

include 'public/widget.php';
?>
```

### Example 3: Dark Mode Footer

```php
<?php
$widgetTitle = 'Contributors';
$style = 'inline';
$darkMode = true;
$showPeriod = false;
$showFooter = true;

include 'public/widget.php';
?>
```

### Example 4: Magento 2 CMS Block

Create a CMS block in Magento admin:

**Block Title:** GitHub Contributors Widget
**Identifier:** `github_contributors_widget`
**Content:**

```php
{{block class="Magento\Framework\View\Element\Template"
        name="github.contributors.widget"
        template="path/to/widget.phtml"}}
```

**Insert in Layout XML:**

```xml
<referenceContainer name="content.top">
    <block class="Magento\Framework\View\Element\Template"
           name="github.contributors"
           template="Vendor_Module::contributors/widget.phtml"
           before="-">
        <arguments>
            <argument name="widget_title" xsi:type="string">Top Contributors</argument>
            <argument name="period_label" xsi:type="string">This Month</argument>
            <argument name="style" xsi:type="string">grid</argument>
        </arguments>
    </block>
</referenceContainer>
```

## Styling Customization

### Override Tailwind Classes

You can customize colors, sizes, and spacing by modifying `public/css/github-contributors.css`:

```css
/* Change primary color from purple to your brand color */
.widget-title svg {
    @apply w-6 h-6 text-blue-600; /* Changed from purple-600 */
}

.view-all-link {
    @apply text-blue-600 font-medium; /* Changed from purple-600 */
}

/* Customize rank badge colors */
.rank-badge {
    @apply bg-gradient-to-br from-blue-600 to-indigo-600;
}
```

### Add Custom CSS

```html
<style>
    /* Increase widget max width */
    .github-contributors-widget {
        max-width: 1200px;
    }

    /* Custom hover effect */
    .contributor-item:hover {
        transform: scale(1.05);
    }

    /* Custom rank badge for #1 */
    .rank-badge.rank-1 {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
    }
</style>
```

## Accessibility Features

The widget includes comprehensive accessibility features:

- **ARIA labels** on all interactive elements
- **Semantic HTML** (proper headings, landmarks)
- **Focus indicators** for keyboard navigation
- **Alt text** on all images
- **Screen reader support** with descriptive text
- **WCAG 2.1 AA compliant** color contrast

### Keyboard Navigation

- **Tab**: Navigate through contributors
- **Enter**: Open contributor profile
- **Shift + Tab**: Navigate backwards

## Performance

### Expected Response Times

| Cache Tier | Response Time | Hit Rate |
|------------|---------------|----------|
| Memory Cache | < 1ms | ~95% |
| Database Cache | 5-10ms | ~4.9% |
| API Call (Fresh) | 500-1000ms | ~0.1% |

### Optimization Tips

1. **Enable OpCache** (PHP bytecode caching)
```ini
opcache.enable=1
opcache.memory_consumption=128
```

2. **Use MySQL Query Cache**
```sql
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL query_cache_type = 1;
```

3. **CDN for Tailwind CSS**
For production, use your own Tailwind build instead of CDN:
```bash
npm install -D tailwindcss
npx tailwindcss -i ./public/css/github-contributors.css -o ./public/css/output.css --minify
```

## Widget States

### Success State
Displays top 5 contributors with avatars, names, and contribution counts.

### Empty State
Shows when no contributor data is available:
- Friendly icon
- "No contributor data available" message
- Graceful degradation

### Error State
Displays when an error occurs:
- Error icon
- "Unable to Load Contributors" message
- Detailed error message (for debugging)

### Loading State
Shows while fetching data:
- Animated spinner
- "Loading contributors..." message

## Security Features

### XSS Prevention
All output is escaped using `htmlspecialchars()`:

```php
echo esc($username); // Safe output

function esc(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
```

### SQL Injection Prevention
All database queries use PDO prepared statements:

```php
$stmt = $db->execute(
    "SELECT * FROM widget_cache WHERE cache_key = ?",
    [$cacheKey]
);
```

### Token Security
- GitHub token stored in `.env` file (never in code)
- `.env` excluded from version control (`.gitignore`)
- Token validated on startup

## Troubleshooting

### Widget Not Displaying

**Check 1:** Verify dependencies are installed
```bash
composer install
```

**Check 2:** Verify database connection
```bash
php demo/test-setup.php
```

**Check 3:** Check logs
```bash
tail -f storage/logs/github-widget.log
```

### "No contributor data available"

**Possible causes:**
1. Cache is empty (run cron job to populate)
2. GitHub API error (check logs)
3. Invalid GitHub token (verify `.env`)

**Solution:**
```bash
# Run services test to populate cache
php demo/test-services.php
```

### Styling Issues

**Check 1:** Verify Tailwind CSS is loaded
```html
<!-- In page head -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="path/to/css/github-contributors.css">
```

**Check 2:** Clear browser cache
```
Ctrl+Shift+R (or Cmd+Shift+R on Mac)
```

### Slow Performance

**Check 1:** Verify cache is working
```bash
mysql -u widget_user -p github_contributors -e "SELECT COUNT(*) FROM widget_cache;"
```

**Check 2:** Check cache hit rate
```php
$cache->getStats(); // Should show high hit rate (> 95%)
```

**Check 3:** Enable PHP OpCache
```bash
php -i | grep opcache
```

## API Reference

### Widget Functions

#### `esc(string $str): string`
Escapes HTML output to prevent XSS attacks.

```php
echo esc($user_input); // Always use for user-generated content
```

#### `formatNumber(int $num): string`
Formats numbers with commas.

```php
echo formatNumber(1234); // Output: "1,234"
```

## Advanced Usage

### Multiple Widgets on Same Page

You can include multiple widgets with different configurations:

```php
<!-- Widget 1: Current Month -->
<?php
$widgetTitle = 'Top Contributors This Month';
$periodLabel = 'October 2025';
$style = 'grid';
include 'public/widget.php';
?>

<!-- Widget 2: All Time -->
<?php
$widgetTitle = 'All-Time Top Contributors';
$periodLabel = 'All Time';
$style = 'list';
include 'public/widget.php';
?>
```

### Custom Data Source

To use custom contributor data instead of cache:

```php
<?php
// Fetch your own data
$contributors = [
    [
        'login' => 'username',
        'contributions' => 1234,
        'avatar_url' => 'https://...',
        'html_url' => 'https://github.com/username',
        'type' => 'User'
    ],
    // ... more contributors
];

// Then include the widget
// It will use the $contributors variable if already set
include 'public/widget.php';
?>
```

## Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Testing

### Run Test Suite

```bash
# Test configuration and setup
php demo/test-setup.php

# Test services layer
php demo/test-services.php

# Test GitHub API integration
php demo/test-github-api.php
```

### View Demo

```bash
# Open demo page in browser
open demo/widget-demo.html
```

## Support

For issues or questions:

1. Check logs: `storage/logs/github-widget.log`
2. Run tests: `php demo/test-services.php`
3. Review documentation: `TECHNICAL_SPECIFICATION.md`

---

**Version:** 1.0.0
**Last Updated:** October 2025
**Repository:** ukmeds/magento2-docs
