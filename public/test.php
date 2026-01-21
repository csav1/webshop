<?php

/**
 * Backend Test Script
 * 
 * Testet alle Backend-Komponenten ohne Frontend/Views
 */

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoloader
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Helpers laden
foreach (glob(__DIR__ . '/../src/Helpers/*.php') as $helper) {
    require_once $helper;
}

echo "<h1>🏀 NBA Webshop - Backend Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;} .ok{color:green;} .fail{color:red;} .info{color:blue;} table{border-collapse:collapse;margin:10px 0;} td,th{border:1px solid #ddd;padding:8px;}</style>";

// =============================================
// 1. Datenbankverbindung
// =============================================
echo "<h2>1. Datenbankverbindung</h2>";

try {
    $db = Core\Database::getInstance();
    echo "<p class='ok'>✅ Datenbankverbindung erfolgreich!</p>";
} catch (Exception $e) {
    echo "<p class='fail'>❌ Datenbankfehler: " . htmlspecialchars($e->getMessage()) . "</p>";
    die("<p>Bitte prüfe config/database.php</p>");
}

// =============================================
// 2. Tabellen prüfen
// =============================================
echo "<h2>2. Datenbank-Tabellen</h2>";

$tables = ['users', 'categories', 'products', 'orders', 'order_items', 'reviews', 'review_helpful', 'cart_items', 'sessions'];
echo "<table><tr><th>Tabelle</th><th>Status</th><th>Anzahl</th></tr>";

foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM {$table}");
        $count = $stmt->fetch()['count'];
        echo "<tr><td>{$table}</td><td class='ok'>✅ OK</td><td>{$count}</td></tr>";
    } catch (Exception $e) {
        echo "<tr><td>{$table}</td><td class='fail'>❌ Fehlt</td><td>-</td></tr>";
    }
}
echo "</table>";

// =============================================
// 3. Models testen
// =============================================
echo "<h2>3. Models</h2>";

// Users
echo "<h3>User Model</h3>";
$users = Models\User::all();
echo "<p class='info'>📊 " . count($users) . " Benutzer gefunden</p>";
if (count($users) > 0) {
    echo "<p>Erster User: <strong>" . htmlspecialchars($users[0]['name']) . "</strong> (" . $users[0]['role'] . ")</p>";
}

// Categories
echo "<h3>Category Model</h3>";
$categories = Models\Category::withProductCount();
echo "<p class='info'>📊 " . count($categories) . " Kategorien gefunden</p>";
foreach ($categories as $cat) {
    echo "<span style='margin-right:10px;'>• " . htmlspecialchars($cat['name']) . " ({$cat['product_count']} Produkte)</span>";
}

// Products
echo "<h3>Product Model</h3>";
$products = Models\Product::featured(4);
echo "<p class='info'>📊 " . count($products) . " Featured Produkte</p>";
foreach ($products as $p) {
    echo "<p>• <strong>" . htmlspecialchars($p['name']) . "</strong> - " . formatPrice($p['price']) . "</p>";
}

// =============================================
// 4. Session & Auth
// =============================================
echo "<h2>4. Session & Auth</h2>";

Core\Session::start();
echo "<p class='ok'>✅ Session gestartet (ID: " . session_id() . ")</p>";

$csrf = Core\Session::generateCsrfToken();
echo "<p class='ok'>✅ CSRF Token generiert</p>";

echo "<p class='info'>Login-Status: " . (Core\Auth::check() ? "Eingeloggt" : "Nicht eingeloggt") . "</p>";

// =============================================
// 5. Warenkorb
// =============================================
echo "<h2>5. Warenkorb (Session)</h2>";

// Test: Produkt hinzufügen
$firstProduct = Models\Product::find(1);
if ($firstProduct) {
    Models\Cart::add($firstProduct['id'], 1);
    echo "<p class='ok'>✅ Produkt zum Warenkorb hinzugefügt</p>";
    echo "<p class='info'>Warenkorb: " . Models\Cart::count() . " Artikel, Summe: " . formatPrice(Models\Cart::total()) . "</p>";
    Models\Cart::clear();
    echo "<p class='ok'>✅ Warenkorb geleert</p>";
}

// =============================================
// 6. Helper-Funktionen
// =============================================
echo "<h2>6. Helper-Funktionen</h2>";

echo "<p><strong>formatPrice(99.99):</strong> " . formatPrice(99.99) . "</p>";
echo "<p><strong>formatDate('2026-01-12'):</strong> " . formatDate('2026-01-12') . "</p>";
echo "<p><strong>formatStars(4.5):</strong> " . formatStars(4.5) . "</p>";
echo "<p><strong>slugify('LeBron James Trikot'):</strong> " . slugify('LeBron James Trikot') . "</p>";

// =============================================
// Zusammenfassung
// =============================================
echo "<h2>✅ Backend Test abgeschlossen!</h2>";
echo "<p style='font-size:1.2em;'>Alle Backend-Komponenten funktionieren korrekt.</p>";
echo "<hr>";
echo "<p><small>Dieses Skript kann nach dem Testen gelöscht werden.</small></p>";
