<?php
// Fix Config Loading for CLI
define('BASE_PATH', __DIR__ . '/..');

// Autoloader manuell da wir index.php umgehen wollen für rohen Test
spl_autoload_register(function ($class) {
    // Namespace-Separator zu Verzeichnis-Separator konvertieren
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

// Load Config
$config = require __DIR__ . '/../config/app.php';

use Core\Database;

try {
    $db = Database::getInstance();
    
    // Hash new password
    $pw = 'admin123';
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    
    // Update Admin
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@nba-shop.de'");
    $stmt->execute([$hash]);
    
    echo "Passwort für admin@nba-shop.de auf '$pw' ($hash) zurückgesetzt.\n";
    
    // Verify
    $stmt = $db->query("SELECT * FROM users WHERE email = 'admin@nba-shop.de'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "DB Check:\n";
    print_r($user);
    
    if (password_verify($pw, $user['password_hash'])) {
        echo "VERIFICATION SUCCESS: Password matches hash.\n";
    } else {
        echo "VERIFICATION FAILED: Password does NOT match hash.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
