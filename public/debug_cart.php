<?php
require_once __DIR__ . '/../config/app.php';
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

use Core\Session;
use Models\Product;
use Models\Cart;

Session::start();

echo "<h1>Cart Debug</h1>";

echo "<h2>Session Cart Content (Raw)</h2>";
$cart = Session::get('cart', []);
echo "<pre>" . print_r($cart, true) . "</pre>";

if (!empty($cart)) {
    foreach ($cart as $id => $item) {
        echo "<h3>Checking Product ID: $id</h3>";
        $product = Product::find($id);
        
        if ($product) {
            echo "Product Found!<br>";
            echo "Name: " . $product['name'] . "<br>";
            echo "Active: " . ($product['is_active'] ? 'YES (1)' : 'NO (0)') . "<br>";
            echo "Stock: " . $product['stock'] . "<br>";
            
            if (!$product['is_active']) {
                echo "<strong style='color:red'>WARNING: Product is INACTIVE. Cart request will remove it!</strong><br>";
            }
        } else {
            echo "<strong style='color:red'>ERROR: Product::find($id) returned NULL!</strong><br>";
        }
    }
} else {
    echo "Cart is empty in Session.";
}

echo "<h2>Product list (first 5)</h2>";
$products = Product::active();
foreach (array_slice($products, 0, 5) as $p) {
    echo "ID: {$p['id']} - {$p['name']} (Active: {$p['is_active']})<br>";
}
