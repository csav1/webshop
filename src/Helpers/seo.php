<?php

/**
 * SEO Helper Funktionen
 */

/**
 * Alle Meta-Tags generieren
 */
function metaTags(array $data): string
{
    $html = '';
    
    // Title
    $title = $data['title'] ?? 'NBA Shop';
    $html .= '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>' . PHP_EOL;
    
    // Description
    $description = $data['description'] ?? 'Offizielles NBA Merchandise - Trikots, Caps, Basketbälle und Sneakers.';
    $html .= '<meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
    
    // Open Graph Tags
    $html .= og_tags([
        'title' => $title,
        'description' => $description,
        'image' => $data['image'] ?? '/assets/images/og-image.jpg',
        'url' => $data['url'] ?? (isset($_SERVER['REQUEST_URI']) ? url($_SERVER['REQUEST_URI']) : url('/'))
    ]);
    
    // Canonical
    $canonical = $data['canonical'] ?? (isset($_SERVER['REQUEST_URI']) ? url($_SERVER['REQUEST_URI']) : url('/'));
    $html .= '<link rel="canonical" href="' . htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
    
    return $html;
}

/**
 * Meta Title generieren
 */
function seo_title(string $title, string $suffix = 'NBA Shop'): string
{
    return htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' | ' . $suffix;
}

/**
 * Meta Description kürzen und escapen
 */
function seo_description(string $text, int $maxLength = 160): string
{
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);

    if (mb_strlen($text) > $maxLength) {
        $text = mb_substr($text, 0, $maxLength - 3) . '...';
    }

    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Canonical URL generieren
 */
function canonical_url(?string $path = null): string
{
    if ($path === null) {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }
    return url($path);
}

/**
 * Open Graph Tags generieren
 */
function og_tags(array $data): string
{
    $defaults = [
        'type' => 'website',
        'locale' => 'de_DE',
        'site_name' => 'NBA Shop'
    ];

    $data = array_merge($defaults, $data);
    $html = '';

    foreach ($data as $property => $content) {
        if (empty($content))
            continue;
        $property = htmlspecialchars($property, ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $html .= '<meta property="og:' . $property . '" content="' . $content . '">' . PHP_EOL;
    }

    return $html;
}

/**
 * JSON-LD für Produkt generieren
 */
function product_jsonld(array $product): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product['name'],
        'description' => $product['description'] ?? '',
        'image' => isset($product['image']) ? url('/assets/images/products/' . $product['image']) : '',
        'sku' => $product['sku'] ?? '',
        'offers' => [
            '@type' => 'Offer',
            'price' => $product['sale_price'] ?? $product['price'],
            'priceCurrency' => 'EUR',
            'availability' => $product['stock'] > 0
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller' => [
                '@type' => 'Organization',
                'name' => 'NBA Shop'
            ]
        ]
    ];

    if (!empty($product['rating'])) {
        $data['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $product['rating'],
            'reviewCount' => $product['review_count'] ?? 0
        ];
    }

    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

/**
 * Breadcrumb JSON-LD generieren
 */
function breadcrumb_jsonld(array $items): string
{
    $listItems = [];
    $position = 1;

    foreach ($items as $name => $url) {
        $listItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $name,
            'item' => $url ?: url('/')
        ];
    }

    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $listItems
    ];

    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
