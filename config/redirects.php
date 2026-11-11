<?php

/*
 * Permanent (301) redirects, checked by App\Services\Redirects before a request is allowed to 404.
 *
 * Keys are request paths without query string. Values are the destination (a path, or an absolute URL).
 * 'exact' is a straight lookup; 'patterns' are regular expressions with a replacement, tried in order.
 *
 * This is data, not code: the slug clean-up (SEO plan D2) adds one line per renamed guide here, for example
 *   '/merchant/getting-started/howto-how-to-configure-ssl-and-security' => '/merchant/getting-started/how-to-configure-ssl-and-security',
 *
 * IMPORTANT: files under public/ (the /developer and /the-core pages) are served by the web server before
 * Laravel runs, so a redirect for one of those paths only fires once its file has been deleted. The
 * index.html forms below are listed for the day they are removed; while the files exist the web server
 * answers them and the pages' rel=canonical points at the folder URL instead.
 */
return [
    'exact' => [
        // Legacy developer pages replaced by their newer versions (files removed from public/developer).
        '/developer/catalog-overview.html' => '/developer/module-catalog-overview.html',
        '/developer/plugin-system-deep-dive.html' => '/developer/guide-tutorial-plugin-system-deep-dive.html',
        '/developer/contributors.html' => '/contributors',

        // Merchant slug clean-up (SEO plan D2): the doubled type prefix is gone from every guide URL.
        // Generated from the renames in the content repo; bin/checks/slug-redirects.js reads this block.
        '/merchant/getting-started/howto-how-to-configure-domain-and-email' => '/merchant/getting-started/how-to-configure-domain-and-email',
        '/merchant/getting-started/howto-how-to-configure-ssl-and-security' => '/merchant/getting-started/how-to-configure-ssl-and-security',
        '/merchant/grow-store/howto-how-to-connect-with-online-marketplaces' => '/merchant/grow-store/how-to-connect-with-online-marketplaces',
        '/merchant/grow-store/howto-how-to-set-up-cross-selling-and-upselling' => '/merchant/grow-store/how-to-set-up-cross-selling-and-upselling',
        '/merchant/grow-store/howto-how-to-set-up-google-analytics-4' => '/merchant/grow-store/how-to-set-up-google-analytics-4',
        '/merchant/grow-store/howto-how-to-track-sales-performance' => '/merchant/grow-store/how-to-track-sales-performance',
        '/merchant/grow-store/reference-promotions-and-discounts-reference' => '/merchant/grow-store/promotions-and-discounts-reference',
        '/merchant/grow-store/tutorial-getting-started-with-create-shopping-cart-price-ru' => '/merchant/grow-store/tutorial-create-shopping-cart-price-rules',
        '/merchant/handle-orders/tutorial-getting-started-with-create-and-manage-invoices' => '/merchant/handle-orders/tutorial-create-and-manage-invoices',
        '/merchant/improve-ux/howto-how-to-improve-site-loading-speed' => '/merchant/improve-ux/how-to-improve-site-loading-speed',
        '/merchant/improve-ux/howto-how-to-make-your-store-accessible' => '/merchant/improve-ux/how-to-make-your-store-accessible',
        '/merchant/improve-ux/howto-how-to-optimize-checkout-for-higher-conversions' => '/merchant/improve-ux/how-to-optimize-checkout-for-higher-conversions',
        '/merchant/improve-ux/howto-how-to-set-up-site-search-and-navigation' => '/merchant/improve-ux/how-to-set-up-site-search-and-navigation',
        '/merchant/manage-catalog/howto-how-to-optimize-product-images' => '/merchant/manage-catalog/how-to-optimize-product-images',
        '/merchant/manage-catalog/howto-how-to-optimize-products-for-search' => '/merchant/manage-catalog/how-to-optimize-products-for-search',
        '/merchant/manage-catalog/howto-how-to-set-up-inventory-tracking' => '/merchant/manage-catalog/how-to-set-up-inventory-tracking',
        '/merchant/manage-catalog/reference-product-types-and-attributes-reference' => '/merchant/manage-catalog/product-types-and-attributes-reference',
        '/merchant/manage-catalog/tutorial-getting-started-with-manage-product-categories' => '/merchant/manage-catalog/tutorial-manage-product-categories',
        '/merchant/manage-catalog/tutorial-getting-started-with-product-import-and-export' => '/merchant/manage-catalog/tutorial-product-import-and-export',
        '/merchant/reports-and-analytics/howto-how-to-generate-financial-reports' => '/merchant/reports-and-analytics/how-to-generate-financial-reports',
        '/merchant/reports-and-analytics/reference-built-in-reports-and-analytics-reference' => '/merchant/reports-and-analytics/built-in-reports-and-analytics-reference',
        '/merchant/start-selling/howto-how-to-set-up-payment-processing' => '/merchant/start-selling/how-to-set-up-payment-processing',
        '/merchant/start-selling/howto-how-to-set-up-shipping-and-fulfillment' => '/merchant/start-selling/how-to-set-up-shipping-and-fulfillment',
        '/merchant/stay-compliant/howto-how-to-add-legal-pages-and-compliance' => '/merchant/stay-compliant/how-to-add-legal-pages-and-compliance',
        '/merchant/stay-compliant/howto-how-to-implement-gdpr-compliance' => '/merchant/stay-compliant/how-to-implement-gdpr-compliance',
        '/merchant/stay-compliant/howto-how-to-maintain-pci-compliance' => '/merchant/stay-compliant/how-to-maintain-pci-compliance',
        '/merchant/stay-compliant/reference-security-features-and-configuration-reference' => '/merchant/stay-compliant/security-features-and-configuration-reference',
        '/merchant/support-and-resources/howto-how-to-automate-business-workflows' => '/merchant/support-and-resources/how-to-automate-business-workflows',
        '/merchant/support-and-resources/howto-how-to-integrate-with-erp-systems' => '/merchant/support-and-resources/how-to-integrate-with-erp-systems',
        '/merchant/support-and-resources/howto-how-to-manage-multiple-stores' => '/merchant/support-and-resources/how-to-manage-multiple-stores',
        '/merchant/support-and-resources/reference-advanced-configuration-options-reference' => '/merchant/support-and-resources/advanced-configuration-options-reference',
        '/merchant/support-and-resources/reference-rest-and-graphql-api-reference' => '/merchant/support-and-resources/rest-and-graphql-api-reference',

        // Folder URL is the canonical form of a hub.
        '/developer/index.html' => '/developer/',
        '/the-core/index.html' => '/the-core/',
    ],

    'patterns' => [
        // Every The Core index page: /the-core/<...>/index.html -> /the-core/<...>/
        '#^(/the-core/(?:.+/)?)index\.html$#' => '$1',
    ],
];
