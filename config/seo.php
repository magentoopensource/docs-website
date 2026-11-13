<?php

/*
 * Site identity used in structured data (JSON-LD). Keep in step with bin/devdocs/seo_head.py on the static
 * pages (/developer, /the-core): same @id values, same name, same url, same sameAs, so search engines merge
 * the Laravel pages and the static pages into one Organization and one WebSite.
 */
return [
    'site' => 'https://docs.magento-opensource.com',
    'site_name' => 'Magento Open Source Documentation',

    'organization' => [
        'id' => 'https://docs.magento-opensource.com/#organization',
        'name' => 'Magento Association',
        'url' => 'https://www.magentoassociation.org/home',
        'logo' => 'https://docs.magento-opensource.com/img/favicon/favicon-196x196.png',
        'same_as' => [
            'https://www.magentoassociation.org/home',
            'https://github.com/magentoopensource',
            'https://www.magento-opensource.com/',
        ],
    ],

    'website_id' => 'https://docs.magento-opensource.com/#website',

    // Guides are credited to the community, never to named people (decision D4).
    'author_name' => 'Magento Association community',

    'image' => 'https://docs.magento-opensource.com/img/merchant-docs-og.png',
];
