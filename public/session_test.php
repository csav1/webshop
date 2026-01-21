<?php
session_start();
if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 0;
}
$_SESSION['test']++;
echo "Session ID: " . session_id() . "<br>";
echo "Test Counter: " . $_SESSION['test'] . "<br>";
echo '<a href="session_test.php">Reload</a>';
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
