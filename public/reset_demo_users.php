<?php
define('BASE_PATH', __DIR__ . '/..');

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

$config = require __DIR__ . '/../config/app.php';

use Core\Database;

try {
    $db = Database::getInstance();
    
    $pw = 'user123';
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    
    // Max Mustermann
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'max.mustermann@email.de'");
    $stmt->execute([$hash]);
    
    echo "Passwort für max.mustermann@email.de auf '$pw' gesetzt.\n";
    
    // Anna Schmidt
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'anna.schmidt@email.de'");
    $stmt->execute([$hash]);

    echo "Passwort für anna.schmidt@email.de auf '$pw' gesetzt.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
