<?php

/**
 * URL Helper Funktionen
 */

/**
 * Vollständige URL generieren
 */
/**
 * Vollständige URL generieren
 */
function url(string $path = '', bool $isAsset = false): string
{
    // Caching der Config verhindern wir hier der Einfachheit halber nicht, 
    // aber wir laden sie.
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../../config/app.php';
    }
    
    $baseUrl = $config['url'] ?? '';

    if (empty($baseUrl)) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Basispfad für Unterverzeichnis automatisch erkennen
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = str_replace('\\', '/', $scriptDir);
        $baseUrl = $protocol . '://' . $host . $basePath;
    }

    $url = rtrim($baseUrl, '/');

    // Nginx-Fix für lab03: Query-Routing nutzen, wenn wir auf dem Server sind
    // und es kein Asset oder die Startseite ist.
    $host = $_SERVER['HTTP_HOST'] ?? '';
    // Wir aktivieren dies pauschal für die Production URL oder wenn der User es braucht via Config
    // Hier hardcoded wir den Check auf die Domain oder aktivieren es, wenn ein Query-Routing benötigt wird.
    // Nginx-Fix für lab03: Query-Routing nutzen, wenn wir auf dem Server sind
    $needsQueryRouting = str_contains(strtolower($host), 'htl-md.dev');

    if ($needsQueryRouting && !$isAsset && !empty($path) && $path !== '/') {
        // Sicherstellen, dass wir nicht doppelt index.php haben
        if (!str_contains($url, 'index.php')) {
             return $url . '/index.php?url=' . ltrim($path, '/');
        }
    }

    return $url . '/' . ltrim($path, '/');
}

/**
 * Asset URL generieren
 */
function asset(string $path): string
{
    // Assets sind echte Dateien, daher kein Query-Routing ($isAsset = true)
    return url('/assets/' . ltrim($path, '/'), true);
}

/**
 * Redirect
 */
function redirect(string $path): never
{
    if (!str_starts_with($path, 'http')) {
        $path = url($path);
    }
    header('Location: ' . $path);
    exit;
}

/**
 * Zurück zum Referrer
 */
function back(): never
{
    $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referrer);
}

/**
 * Aktuelle URL abrufen
 */
function current_url(): string
{
    return url($_SERVER['REQUEST_URI'] ?? '/');
}

/**
 * Request URI abrufen
 */
function request_uri(): string
{
    return $_SERVER['REQUEST_URI'] ?? '/';
}

/**
 * Prüfen ob aktuelle Route
 */
function is_route(string $path): bool
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $currentPath === $path;
}

/**
 * Prüfen ob Route mit Pattern beginnt
 */
function is_route_prefix(string $prefix): bool
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return str_starts_with($currentPath, $prefix);
}

/**
 * Active Class für Navigation
 */
function active_class(string $path, string $class = 'active'): string
{
    return is_route($path) ? $class : '';
}

/**
 * Active Class für Prefix
 */
function active_prefix_class(string $prefix, string $class = 'active'): string
{
    return is_route_prefix($prefix) ? $class : '';
}

/**
 * Slug aus String generieren
 */
function slugify(string $text): string
{
    $text = mb_strtolower($text);
    $text = preg_replace('/[äÄ]/', 'ae', $text);
    $text = preg_replace('/[öÖ]/', 'oe', $text);
    $text = preg_replace('/[üÜ]/', 'ue', $text);
    $text = preg_replace('/ß/', 'ss', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}
