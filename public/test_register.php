<?php
require_once __DIR__ . '/../config/app.php';
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require_once $file;
});

use Core\Session;
use Core\Auth;
use Models\User;
use Core\Database;

Session::start();

echo "<h1>Registration Test</h1>";

// 1. Cleanup old test user
$testEmail = 'test_reg_' . time() . '@example.com';
echo "Test Email: $testEmail<br>";

try {
    // 2. Register
    echo "<h2>Attempting Registration...</h2>";
    $userData = [
        'name' => 'Registration Test User',
        'email' => $testEmail,
        'password' => 'Test1234!'
    ];
    
    $userId = User::register($userData);
    echo "User::register returned ID: " . var_export($userId, true) . "<br>";
    
    if (!$userId) {
        throw new Exception("User::register returned falsy value!");
    }

    // 3. Verify DB
    echo "<h2>Verifying Database Entry...</h2>";
    $user = User::find($userId);
    if ($user) {
        echo "<span style='color:green'>User found in DB!</span><br>";
        echo "<pre>" . print_r($user, true) . "</pre>";
    } else {
        throw new Exception("User created but NOT found via User::find($userId)!");
    }

    // 4. Test Login Logic
    echo "<h2>Testing Auth::login...</h2>";
    Auth::login($user);
    
    echo "Auth::check() after login: " . (Auth::check() ? 'YES' : 'NO') . "<br>";
    echo "Session User ID: " . Session::get('user_id') . "<br>";
    
    if (Auth::check()) {
        echo "<h2 style='color:green'>SUCCESS: Registration and Login Logic works!</h2>";
    } else {
        echo "<h2 style='color:red'>FAILURE: Auth::login did not persist session!</h2>";
    }

} catch (Exception $e) {
    echo "<h2 style='color:red'>ERROR: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    
    if ($e instanceof PDOException) {
         echo "PDO Info: " . print_r($e->errorInfo, true);
    }
}
