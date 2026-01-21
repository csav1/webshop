<?php

use Core\Router;

/**
 * Routen-Definitionen
 * 
 * Alle Routen werden hier zentral definiert.
 */

$router = new Router();

// ================================
// Öffentliche Routen
// ================================

// Startseite
$router->get('/', [Controllers\HomeController::class, 'index']);
$router->get('/ueber-uns', [Controllers\HomeController::class, 'about']);
$router->get('/kontakt', [Controllers\HomeController::class, 'contact']);

// Produkte
$router->get('/produkte', [Controllers\ProductController::class, 'index']);
$router->get('/produkt/{slug}', [Controllers\ProductController::class, 'show']);
$router->get('/kategorie/{slug}', [Controllers\ProductController::class, 'category']);
$router->get('/suche', [Controllers\ProductController::class, 'search']);

// Warenkorb
$router->get('/warenkorb', [Controllers\CartController::class, 'index']);
$router->post('/warenkorb/hinzufuegen', [Controllers\CartController::class, 'add']);
$router->post('/warenkorb/aktualisieren', [Controllers\CartController::class, 'update']);
$router->post('/warenkorb/entfernen', [Controllers\CartController::class, 'remove']);
$router->post('/warenkorb/leeren', [Controllers\CartController::class, 'clear']);



// ================================
// Authentifizierung (Gast-Routen)
// ================================

$router->group(['middleware' => 'guest'], function ($router) {
    $router->get('/anmelden', [Controllers\AuthController::class, 'showLogin']);
    $router->post('/anmelden', [Controllers\AuthController::class, 'login']);
    $router->get('/registrieren', [Controllers\AuthController::class, 'showRegister']);
    $router->post('/registrieren', [Controllers\AuthController::class, 'register']);

    // Google OAuth
    $router->get('/auth/google', [Controllers\AuthController::class, 'googleRedirect']);
    $router->get('/auth/google/callback', [Controllers\AuthController::class, 'googleCallback']);
});

// Logout (benötigt Auth)
$router->get('/abmelden', [Controllers\AuthController::class, 'logout']);

// ================================
// Geschützte Routen (Auth erforderlich)
// ================================

$router->group(['middleware' => 'auth'], function ($router) {
    // Checkout
    $router->get('/kasse', [Controllers\OrderController::class, 'checkout']);
    $router->post('/kasse', [Controllers\OrderController::class, 'store']);

    // Profil
    $router->get('/profil', [Controllers\AuthController::class, 'profile']);
    $router->post('/profil', [Controllers\AuthController::class, 'updateProfile']);
    $router->post('/profil/passwort', [Controllers\AuthController::class, 'changePassword']);



    // Bestellungen
    $router->get('/bestellungen', [Controllers\OrderController::class, 'index']);
    $router->get('/bestellungen/{orderNumber}', [Controllers\OrderController::class, 'show']);
    $router->post('/bestellungen/{orderNumber}/stornieren', [Controllers\OrderController::class, 'cancel']);

    // Bewertungen
    $router->post('/bewertungen', [Controllers\ReviewController::class, 'store']);
    $router->post('/bewertungen/hilfreich', [Controllers\ReviewController::class, 'helpful']);
    $router->post('/bewertungen/loeschen', [Controllers\ReviewController::class, 'delete']);

    // Payment
    $router->get('/zahlung/kreditkarte/{orderNumber}', [Controllers\PaymentController::class, 'creditCard']);
    $router->post('/zahlung/kreditkarte/{orderNumber}', [Controllers\PaymentController::class, 'processCreditCard']);
    $router->get('/zahlung/paypal/{orderNumber}', [Controllers\PaymentController::class, 'paypal']);
    $router->post('/zahlung/paypal/{orderNumber}', [Controllers\PaymentController::class, 'processPaypal']);
});

// ================================
// Admin-Routen
// ================================

$router->group(['prefix' => '/admin', 'middleware' => 'admin'], function ($router) {
    // Dashboard
    $router->get('', [Controllers\Admin\DashboardController::class, 'index']);
    $router->get('/dashboard', [Controllers\Admin\DashboardController::class, 'index']);

    // Produkte
    $router->get('/produkte', [Controllers\Admin\ProductController::class, 'index']);
    $router->get('/produkte/neu', [Controllers\Admin\ProductController::class, 'create']);
    $router->post('/produkte', [Controllers\Admin\ProductController::class, 'store']);
    $router->get('/produkte/{id}/bearbeiten', [Controllers\Admin\ProductController::class, 'edit']);
    $router->post('/produkte/{id}', [Controllers\Admin\ProductController::class, 'update']);
    $router->post('/produkte/{id}/loeschen', [Controllers\Admin\ProductController::class, 'delete']);

    // Kategorien
    $router->get('/kategorien', [Controllers\Admin\CategoryController::class, 'index']);
    $router->get('/kategorien/neu', [Controllers\Admin\CategoryController::class, 'create']);
    $router->post('/kategorien', [Controllers\Admin\CategoryController::class, 'store']);
    $router->get('/kategorien/{id}/bearbeiten', [Controllers\Admin\CategoryController::class, 'edit']);
    $router->post('/kategorien/{id}', [Controllers\Admin\CategoryController::class, 'update']);
    $router->post('/kategorien/{id}/loeschen', [Controllers\Admin\CategoryController::class, 'delete']);
    $router->post('/kategorien/{id}/status', [Controllers\Admin\CategoryController::class, 'toggleActive']);

    // Bestellungen
    $router->get('/bestellungen', [Controllers\Admin\OrderController::class, 'index']);
    $router->get('/bestellungen/{id}', [Controllers\Admin\OrderController::class, 'show']);
    $router->post('/bestellungen/{id}/status', [Controllers\Admin\OrderController::class, 'updateStatus']);
    $router->post('/bestellungen/{id}/zahlung', [Controllers\Admin\OrderController::class, 'updatePaymentStatus']);

    // Benutzer
    $router->get('/benutzer', [Controllers\Admin\UserController::class, 'index']);
    $router->get('/benutzer/{id}', [Controllers\Admin\UserController::class, 'show']);
    $router->get('/benutzer/{id}/bearbeiten', [Controllers\Admin\UserController::class, 'edit']);
    $router->post('/benutzer/{id}', [Controllers\Admin\UserController::class, 'update']);
    $router->post('/benutzer/{id}/status', [Controllers\Admin\UserController::class, 'toggleActive']);
    $router->post('/benutzer/{id}/befoerdern', [Controllers\Admin\UserController::class, 'promote']);
    $router->post('/benutzer/{id}/degradieren', [Controllers\Admin\UserController::class, 'demote']);
});

return $router;
