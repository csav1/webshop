<?php

/**
 * Format Helper Funktionen
 */

/**
 * Preis formatieren
 */
function formatPrice(float $price, string $currency = '€'): string
{
    return number_format($price, 2, ',', '.') . ' ' . $currency;
}

/**
 * Preis mit Sale-Badge
 */
function formatPriceWithSale(float $price, ?float $salePrice, string $currency = '€'): string
{
    if ($salePrice && $salePrice > 0 && $salePrice < $price) {
        return '<span class="price-sale">' . formatPrice($salePrice, $currency) . '</span> ' .
            '<span class="price-original">' . formatPrice($price, $currency) . '</span>';
    }
    return formatPrice($price, $currency);
}

/**
 * Rabatt berechnen
 */
function calculateDiscount(float $originalPrice, float $salePrice): int
{
    if ($originalPrice <= 0 || $salePrice >= $originalPrice) {
        return 0;
    }
    return (int) round(100 - ($salePrice / $originalPrice * 100));
}

/**
 * Datum formatieren (deutsch)
 */
function formatDate(string $date, string $format = 'd.m.Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Datum und Zeit formatieren
 */
function formatDateTime(string $date, string $format = 'd.m.Y H:i'): string
{
    return date($format, strtotime($date));
}

/**
 * Relatives Datum (vor X Tagen)
 */
function formatRelativeDate(string $date): string
{
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'gerade eben';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return "vor {$mins} Minute" . ($mins > 1 ? 'n' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "vor {$hours} Stunde" . ($hours > 1 ? 'n' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "vor {$days} Tag" . ($days > 1 ? 'en' : '');
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return "vor {$weeks} Woche" . ($weeks > 1 ? 'n' : '');
    } else {
        return formatDate($date);
    }
}

/**
 * Text kürzen
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    $text = strip_tags($text);

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * HTML sicher ausgeben
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Sternebewertung als HTML
 */
function formatStars(float $rating, int $max = 5): string
{
    $html = '<span class="stars" aria-label="' . $rating . ' von ' . $max . ' Sternen">';

    for ($i = 1; $i <= $max; $i++) {
        if ($i <= $rating) {
            $html .= '<span class="star star-filled">★</span>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<span class="star star-half">★</span>';
        } else {
            $html .= '<span class="star star-empty">☆</span>';
        }
    }

    $html .= '</span>';
    return $html;
}

/**
 * Bestellstatus Farbe
 */
function orderStatusColor(string $status): string
{
    return match ($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'shipped' => 'bg-indigo-100 text-indigo-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800'
    };
}

/**
 * Bestellstatus als Badge
 */
function formatOrderStatus(string $status): string
{
    $labels = [
        'pending' => ['Ausstehend', 'badge-warning'],
        'processing' => ['In Bearbeitung', 'badge-info'],
        'shipped' => ['Versendet', 'badge-primary'],
        'delivered' => ['Zugestellt', 'badge-success'],
        'cancelled' => ['Storniert', 'badge-danger']
    ];

    $label = $labels[$status] ?? [$status, 'badge-secondary'];
    return '<span class="badge ' . $label[1] . '">' . e($label[0]) . '</span>';
}

/**
 * Zahlungsstatus als Badge
 */
function formatPaymentStatus(string $status): string
{
    $labels = [
        'pending' => ['Ausstehend', 'badge-warning'],
        'paid' => ['Bezahlt', 'badge-success'],
        'failed' => ['Fehlgeschlagen', 'badge-danger'],
        'refunded' => ['Erstattet', 'badge-info']
    ];

    $label = $labels[$status] ?? [$status, 'badge-secondary'];
    return '<span class="badge ' . $label[1] . '">' . e($label[0]) . '</span>';
}

/**
 * Pluralisierung
 */
function pluralize(int $count, string $singular, string $plural): string
{
    return $count === 1 ? $singular : $plural;
}

/**
 * Bytes formatieren
 */
function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}
