<?php

namespace Controllers;

use Core\View;
use Core\Session;
use Core\Auth;
use Models\Order;

/**
 * PaymentController - Simulated Payment Processing
 */
class PaymentController
{
    public function __construct()
    {
        Auth::requireAuth();
    }

    /**
     * Show credit card payment form
     */
    public function creditCard(string $orderNumber): void
    {
        $order = Order::findByNumber($orderNumber);
        
        if (!$order || $order['user_id'] !== Auth::id()) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/bestellungen');
            return;
        }

        if ($order['payment_status'] === 'paid') {
            Session::info('Diese Bestellung wurde bereits bezahlt.');
            redirect('/bestellungen/' . $orderNumber);
            return;
        }

        View::render('payment/creditcard', [
            'seo' => ['title' => 'Kreditkartenzahlung'],
            'order' => $order
        ]);
    }

    /**
     * Process credit card payment
     */
    public function processCreditCard(string $orderNumber): void
    {
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/zahlung/kreditkarte/' . $orderNumber);
            return;
        }

        $order = Order::findByNumber($orderNumber);
        
        if (!$order || $order['user_id'] !== Auth::id()) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/bestellungen');
            return;
        }

        // Simulate payment processing
        Order::updatePaymentStatus($order['id'], 'paid');
        
        Session::success('Zahlung erfolgreich! Vielen Dank für Ihre Bestellung.');
        redirect('/bestellungen/' . $orderNumber);
    }

    /**
     * Show PayPal payment form
     */
    public function paypal(string $orderNumber): void
    {
        $order = Order::findByNumber($orderNumber);
        
        if (!$order || $order['user_id'] !== Auth::id()) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/bestellungen');
            return;
        }

        if ($order['payment_status'] === 'paid') {
            Session::info('Diese Bestellung wurde bereits bezahlt.');
            redirect('/bestellungen/' . $orderNumber);
            return;
        }

        View::render('payment/paypal', [
            'seo' => ['title' => 'PayPal Zahlung'],
            'order' => $order
        ]);
    }

    /**
     * Process PayPal payment
     */
    public function processPaypal(string $orderNumber): void
    {
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/zahlung/paypal/' . $orderNumber);
            return;
        }

        $order = Order::findByNumber($orderNumber);
        
        if (!$order || $order['user_id'] !== Auth::id()) {
            Session::error('Bestellung nicht gefunden.');
            redirect('/bestellungen');
            return;
        }

        // Simulate payment processing
        Order::updatePaymentStatus($order['id'], 'paid');
        
        Session::success('PayPal Zahlung erfolgreich! Vielen Dank für Ihre Bestellung.');
        redirect('/bestellungen/' . $orderNumber);
    }
}
