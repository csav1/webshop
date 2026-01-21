<?php
// Mock Orders für Max Mustermann erstellen

define('BASE_PATH', __DIR__ . '/..');

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

$config = require __DIR__ . '/../config/app.php';

use Core\Database;
use Models\User;
use Models\Order;
use Models\OrderItem;
use Models\Product;

try {
    $db = Database::getInstance();
    
    // User finden
    $user = User::findByEmail('max.mustermann@email.de');
    if (!$user) {
        die("User Max Mustermann nicht gefunden.\n");
    }
    
    echo "User gefunden: {$user['name']} (ID: {$user['id']})\n";
    
    // Produkte finden (einfach die ersten 5)
    $stmt = $db->query("SELECT * FROM products LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($products) < 2) {
        die("Nicht genügend Produkte gefunden.\n");
    }
    
    // Bestellung 1: Vor 2 Stunden, Pending
    $cartItems1 = [
        [
            'product_id' => $products[0]['id'],
            'name' => $products[0]['name'],
            'image' => $products[0]['image'],
            'price' => $products[0]['price'], // Originalpreis verwenden
            'quantity' => 1
        ],
        [
            'product_id' => $products[1]['id'],
            'name' => $products[1]['name'],
            'image' => $products[1]['image'],
            'price' => $products[1]['price'],
            'quantity' => 2
        ]
    ];
    
    $shipping1 = [
        'name' => 'Max Mustermann',
        'street' => 'Musterstraße 1',
        'city' => 'Berlin',
        'zip' => '10115',
        'country' => 'Deutschland'
    ];
    
    echo "Erstelle Bestellung 1...\n";
    $order1 = Order::createOrder($user['id'], $shipping1, 'paypal', $cartItems1);
    
    // Datum manipulieren (damit Liste sortiert aussieht)
    $stmt = $db->prepare("UPDATE orders SET created_at = DATE_SUB(NOW(), INTERVAL 2 HOUR) WHERE id = ?");
    $stmt->execute([$order1['id']]);
    
    echo "Bestellung {$order1['order_number']} erstellt.\n";
    
    
    // Bestellung 2: Vor 5 Tagen, Delivered
    $cartItems2 = [
        [
            'product_id' => $products[2]['id'],
            'name' => $products[2]['name'],
            'image' => $products[2]['image'],
            'price' => $products[2]['price'],
            'quantity' => 1
        ]
    ];
    
    echo "Erstelle Bestellung 2...\n";
    $order2 = Order::createOrder($user['id'], $shipping1, 'creditcard', $cartItems2);
    
    // Status und Datum anpassen
    $stmt = $db->prepare("UPDATE orders SET created_at = DATE_SUB(NOW(), INTERVAL 5 DAY), status = 'delivered', payment_status = 'paid' WHERE id = ?");
    $stmt->execute([$order2['id']]);
    
    echo "Bestellung {$order2['order_number']} erstellt (Delivered).\n";
    
    echo "\nFertig! 2 Bestellungen für Max Mustermann angelegt.\n";

} catch (Exception $e) {
    echo "Fehler: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
