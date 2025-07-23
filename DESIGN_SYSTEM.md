# Magento Merchant Documentation Design System Rules

This document defines the design system rules for the Magento Merchant Documentation website based on the Figma design files and current implementation.

## Design System Structure

### 1. Token Definitions

**Colors**: Defined in `tailwind.config.js` with a comprehensive palette including:
- **Primary**: Orange (`#ff6500` - orange-500) for CTA buttons and key actions
- **Secondary**: Red (`#ec0e00` - red-700) for errors and alerts
- **Grays**: Extensive 50-900 scale for text and backgrounds
- **Dark Mode**: Dedicated dark color palette (dark-900 to dark-500)
- **Brand Colors**: Laravel ecosystem colors (vapor, forge, etc.)

**Typography**: Montserrat font family with custom font sizes including `6.5xl` (4rem)

**Spacing**: Custom spacing scale with `224` (56rem) for large layouts

**Shadows**: Custom shadow system with subtle, consistent elevation

### 2. Component Library

**Location**: `resources/views/components/`
**Architecture**: Laravel Blade components with Tailwind CSS classes
**Components**:
- Button variants (primary, secondary)
- Accessibility components (skip links, content wrappers)
- Tab systems
- Partner content blocks

### 3. Frameworks & Libraries

- **Backend**: Laravel (PHP framework)
- **Frontend**: Alpine.js for interactivity
- **Styling**: Tailwind CSS v3.3.0
- **Build System**: Vite v4.3.9
- **Documentation**: Markdown with GitHub Flavored Markdown support

### 4. Asset Management

- **Storage**: Static assets in `public/img/`
- **Processing**: Vite handles CSS/JS compilation
- **Optimization**: PostCSS with autoprefixer
- **Logo**: SVG format for scalability (`Mage-OSLogoOrange.svg`)

### 5. Icon System

- **Storage**: Inline SVG in Blade templates
- **Usage**: Direct embedding with Tailwind utility classes
- **Styling**: CurrentColor for theme consistency

### 6. Styling Approach

- **Methodology**: Utility-first with Tailwind CSS
- **Global Styles**: Imported via `resources/css/app.css`
- **Modular CSS**: Separate files for different concerns:
  - `_typography.css`: Text styling
  - `_code.css`: Code block styling
  - `_sidebar_layout.css`: Navigation layout
  - `_search.css`: Search component styling
  - `_docs.css`: Documentation-specific styles
  - `_accessibility.css`: A11y enhancements
  - `_dark_mode.css`: Dark theme overrides

### 7. Project Structure

```
resources/
├── css/
│   ├── app.css (main entry point)
│   ├── _typography.css
│   ├── _code.css
│   ├── _sidebar_layout.css
│   ├── _search.css
│   ├── _docs.css
│   ├── _accessibility.css
│   ├── _dark_mode.css
│   └── _gradient-box.css
├── views/
│   ├── components/
│   │   ├── accessibility/
│   │   ├── button/
│   │   ├── partners/
│   │   └── tabs/
│   ├── partials/
│   │   ├── header.blade.php
│   │   ├── footer.blade.php
│   │   └── layout.blade.php
│   └── *.blade.php (page templates)
└── js/
    └── app.js
```

## Component Patterns

### Button Components
- **Primary**: Orange background with hover transforms
- **Secondary**: Outlined style for secondary actions
- **Accessibility**: Focus states and proper labeling

### Layout Components
- **Header**: Responsive with mobile hamburger menu
- **Navigation**: Collapsible sidebar with nested items
- **Content**: Max-width containers with proper spacing

### Typography Scale
- Consistent heading hierarchy
- Code syntax highlighting via Torchlight
- Responsive font sizes

## Responsive Design
- **Breakpoints**: Standard Tailwind breakpoints
- **Mobile-first**: Progressive enhancement approach
- **Flexible Layouts**: CSS Grid and Flexbox

## Dark Mode
- **Implementation**: Class-based toggle (`darkMode: "class"`)
- **Colors**: Dedicated dark color palette
- **Components**: All components support dark mode variants

## Performance Considerations
- **Caching**: 5-minute cache for pages, 1-hour for index
- **Asset Optimization**: Vite build process
- **Font Loading**: Web font optimization
- **Image Optimization**: SVG for scalable graphics

## Accessibility Features
- **Skip Links**: Jump to main content
- **Focus Management**: Keyboard navigation support
- **ARIA Labels**: Proper semantic markup
- **Color Contrast**: WCAG compliant color combinations