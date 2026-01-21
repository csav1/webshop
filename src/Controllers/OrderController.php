<?php

namespace Controllers;

use Core\View;
use Core\Session;
use Core\Auth;
use Core\Validator;
use Models\Cart;
use Models\Order;

/**
 * OrderController - Bestellungen und Checkout
 */
class OrderController
{
    public function __construct()
    {
        // Require Auth via Middleware in routes.php
    }

    /**
     * Checkout-Seite anzeigen
     */
    public function checkout(): void
    {
        if (Cart::isEmpty()) {
            Session::error('Ihr Warenkorb ist leer.');
            redirect('/warenkorb');
            return;
        }

        // Bestand prüfen
        Cart::refreshStock();

        View::render('orders/checkout', [
            'seo' => [
                'title' => 'Kasse',
                'description' => 'Schließen Sie Ihre Bestellung ab'
            ],
            'cartItems' => Cart::getItems(),
            'subtotal' => Cart::subtotal(),
            'tax' => Cart::tax(),
            'shipping' => Cart::shipping(),
            'total' => Cart::total(),
            'user' => Auth::user()
        ]);
    }

    /**
     * Bestellung abschließen
     */
    public function store(): void
    {
        if (Cart::isEmpty()) {
            Session::error('Ihr Warenkorb ist leer.');
            redirect('/warenkorb');
            return;
        }

        // CSRF prüfen
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage. Bitte versuchen Sie es erneut.');
            redirect('/kasse');
            return;
        }

        // Validierung
        $validator = Validator::make($_POST, [
            'shipping_name' => 'required|min:2|max:100',
            'shipping_street' => 'required|min:5|max:255',
            'shipping_city' => 'required|min:2|max:100',
            'shipping_zip' => 'required|min:4|max:20',
            'payment_method' => 'required|in:paypal,kreditkarte'
        ], [
            'shipping_name.required' => 'Bitte geben Sie Ihren Namen ein.',
            'shipping_street.required' => 'Bitte geben Sie Ihre Straße ein.',
            'shipping_city.required' => 'Bitte geben Sie Ihre Stadt ein.',
            'shipping_zip.required' => 'Bitte geben Sie Ihre PLZ ein.',
            'payment_method.required' => 'Bitte wählen Sie eine Zahlungsmethode.'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            Session::flash('old', $_POST);
            redirect('/kasse');
            return;
        }

        try {
            $order = Order::createOrder(
                Auth::id(), // Kann null sein
                [
                    'name' => $_POST['shipping_name'],
                    'street' => $_POST['shipping_street'],
                    'city' => $_POST['shipping_city'],
                    'zip' => $_POST['shipping_zip'],
                    'country' => $_POST['shipping_country'] ?? 'Deutschland',
                    'phone' => $_POST['shipping_phone'] ?? null
                ],
                $_POST['payment_method'],
                Cart::getItems()
            );

            // Warenkorb leeren
            Cart::clear();

            // Redirect to payment page based on method
            $paymentMethod = $_POST['payment_method'];
            if ($paymentMethod === 'kreditkarte') {
                redirect('/zahlung/kreditkarte/' . $order['order_number']);
            } else {
                redirect('/zahlung/paypal/' . $order['order_number']);
            }

        } catch (\Exception $e) {
            Session::error('Fehler bei der Bestellung: ' . $e->getMessage());
            redirect('/kasse');
        }
    }

    /**
     * Bestellhistorie anzeigen
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $orders = Order::findByUser(Auth::id(), $page);

        View::render('orders/index', [
            'seo' => [
                'title' => 'Meine Bestellungen',
                'description' => 'Übersicht Ihrer Bestellungen'
            ],
            'orders' => $orders['items'],
            'pagination' => $orders
        ]);
    }

    /**
     * Einzelne Bestellung anzeigen
     */
    public function show(string $orderNumber): void
    {
        $order = Order::findByNumber($orderNumber);

        if (!$order || $order['user_id'] !== Auth::id()) {
            http_response_code(404);
            View::render('errors/404', [
                'seo' => ['title' => 'Bestellung nicht gefunden'],
                'message' => 'Diese Bestellung wurde nicht gefunden.'
            ]);
            return;
        }

        View::render('orders/show', [
            'seo' => [
                'title' => "Bestellung {$orderNumber}",
                'description' => 'Details zu Ihrer Bestellung'
            ],
            'order' => $order
        ]);
    }

    /**
     * Bestellung stornieren
     */
    public function cancel(string $orderNumber): void
    {
        $order = Order::findByNumber($orderNumber);

        if (!$order || $order['user_id'] !== Auth::id()) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/bestellungen');
            return;
        }

        if (in_array($order['status'], ['shipped', 'delivered', 'cancelled'])) {
            Session::error('Diese Bestellung kann nicht mehr storniert werden.');
            redirect('/bestellungen/' . $orderNumber);
            return;
        }

        if (Order::cancel($order['id'])) {
            Session::success('Bestellung wurde erfolgreich storniert.');
        } else {
            Session::error('Fehler beim Stornieren der Bestellung.');
        }

        redirect('/bestellungen/' . $orderNumber);
    }
}
