<?php
// Mock $_SERVER vars for Session/Path detection
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/Webshop/public/';
$_SERVER['IS_TESTED_IN_CLI'] = true;

require_once __DIR__ . '/index.php'; // Boot app (loads config, helper, session)

use Core\Auth;
use Core\Session;

echo "\n--- Login Verification ---\n";

// 1. Check Session Params
$params = session_get_cookie_params();
echo "Cookie Path Configured: " . $params['path'] . "\n";

if ($params['path'] !== '/Webshop/public') {
    echo "[FAIL] Cookie Path is not '/Webshop/public'!\n";
} else {
    echo "[OK] Cookie Path correct.\n";
}

// 2. Perform Login
echo "\nAttempting Login with admin@nba-shop.de...\n";
if (Auth::attempt('admin@nba-shop.de', 'admin123')) {
    echo "[OK] Auth::attempt returned true.\n";
    
    // 3. Verify Session State
    if (Auth::check()) {
        echo "[OK] Auth::check() is true.\n";
        echo "Logged in User ID: " . Session::get('user_id') . "\n";
        
        // Admin Check
        if (Auth::isAdmin()) {
            echo "[OK] User is Admin.\n";
        } else {
            echo "[FAIL] User is NOT Admin.\n";
        }

    } else {
        echo "[FAIL] Auth::check() returned false after successful attempt!\n";
    }

} else {
    echo "[FAIL] Login failed (Wrong credentials?).\n";
    echo "Hash in DB verification needed.\n";
}

echo "\n--- Session Dump ---\n";
print_r($_SESSION);
