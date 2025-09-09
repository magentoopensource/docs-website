# Merchant Docs - Project Overview

## Purpose
This is the source of the **Magento 2 Merchant Documentation website** - a comprehensive guide for managing and growing Magento 2 stores. The site serves static documentation content with dynamic rendering and caching.

## Key Characteristics
- **No database required** - This is a static documentation site using file-based content
- Documentation content is synced from external repository: https://github.com/mage-os/devdocs.git
- Content is stored in `resources/docs/{version}/` directories
- Laravel-based with Vite for asset compilation

## Documentation Categories
The site organizes content into six main merchant-focused categories:
- **Start Selling**: First products, payments, shipping setup
- **Manage Catalog**: Product catalog organization and bulk operations  
- **Handle Orders**: Order processing and fulfillment
- **Grow Store**: Marketing tools, analytics, customer retention
- **Improve UX**: Design, navigation, performance optimization
- **Stay Compliant**: Legal requirements and data protection

## Version Support
- Supports multiple doc versions: "main", "develop", "2.4.5"
- Default version: "main"
- Versions defined in `Documentation::getDocVersions()`