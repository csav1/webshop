<?php

/**
 * NBA Webshop - Front Controller
 * 
 * Alle Anfragen werden durch diese Datei geleitet.
 */

// PHP Version prüfen
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die('PHP 8.0 oder höher ist erforderlich. Aktuelle Version: ' . PHP_VERSION);
}

// Basis-Pfad definieren
define('BASE_PATH', dirname(__DIR__));

// Error Reporting (in Produktion deaktivieren)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Zeitzone setzen
date_default_timezone_set('Europe/Berlin');

// Charset explizit setzen
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');



// ================================
// Autoloader
// ================================
spl_autoload_register(function ($class) {
    // Namespace-Separator zu Verzeichnis-Separator konvertieren
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ================================
// Helper-Funktionen laden
// ================================
$helpersPath = __DIR__ . '/../src/Helpers/';
foreach (glob($helpersPath . '*.php') as $helper) {
    require_once $helper;
}

// ================================
// Konfiguration laden
// ================================
// Debug Logging
file_put_contents(__DIR__ . '/debug.txt', date('Y-m-d H:i:s') . " Request: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

// Konfiguration laden
$config = require __DIR__ . '/../config/app.php';

// Session Start (sicherstellen, dass dies korrekt geschieht)
Core\Session::start();

// Views-Pfad setzen
Core\View::setViewsPath(__DIR__ . '/../views');

// Globale View-Daten
Core\View::share('config', $config);
Core\View::share('cartCount', Models\Cart::count());

// ================================
// Request verarbeiten
// ================================

// URL aus Query-Parameter (via .htaccess) oder REQUEST_URI holen
$uri = $_GET['url'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Basispfad automatisch erkennen
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = str_replace('\\', '/', $scriptDir); // Windows Backslash Fix

// Logging für Debugging
$method = $_SERVER['REQUEST_METHOD'];
$logFile = __DIR__ . '/request.log';
$logData = date('Y-m-d H:i:s') . " | Method: $method | URI: " . $_SERVER['REQUEST_URI'] . " | BasePath: $basePath | URL Param: " . ($_GET['url'] ?? 'NULL') . "\n";
file_put_contents($logFile, $logData, FILE_APPEND);

// URI bereinigen
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
} elseif (strpos($uri, strtolower($basePath)) === 0) { // Fallback für Case-Insensitive
    $uri = substr($uri, strlen($basePath));
}

if ($uri === '' || $uri === false) {
    $uri = '/';
} elseif ($uri[0] !== '/') {
    $uri = '/' . $uri;
}

// ================================
// Router initialisieren und dispatchen
// ================================
try {
    // Router laden
    $router = require __DIR__ . '/../config/routes.php';

    // Helper laden
require_once __DIR__ . '/../src/Helpers/url.php';
require_once __DIR__ . '/../src/Helpers/seo.php';
require_once __DIR__ . '/../src/Helpers/format.php';

    // Request verarbeiten
    $router->dispatch($uri, $method);

} catch (PDOException $e) {
    // Datenbankfehler
    http_response_code(500);
    if ($config['debug']) {
        echo '<h1>Datenbankfehler</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Ein Fehler ist aufgetreten</h1>';
        echo '<p>Bitte versuchen Sie es später erneut.</p>';
    }
} catch (Exception $e) {
    // Allgemeiner Fehler
    http_response_code(500);
    if ($config['debug']) {
        echo '<h1>Fehler</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Ein Fehler ist aufgetreten</h1>';
        echo '<p>Bitte versuchen Sie es später erneut.</p>';
    }
}
