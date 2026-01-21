<?php

namespace Controllers;

use Core\View;
use Core\Session;
use Core\Auth;
use Models\Cart;
use Models\Product;

/**
 * CartController - Warenkorb
 */
class CartController
{
    /**
     * Warenkorb anzeigen
     */
    public function index(): void
    {
        // Bestand aktualisieren
        Cart::refreshStock();

        View::render('cart/index', [
            'seo' => [
                'title' => 'Warenkorb',
                'description' => 'Ihr Warenkorb im NBA Shop'
            ],
            'items' => Cart::getItems(),
            'subtotal' => Cart::subtotal(),
            'tax' => Cart::tax(),
            'shipping' => Cart::shipping(),
            'total' => Cart::total(),
            'isEmpty' => Cart::isEmpty()
        ]);
    }

    /**
     * Produkt zum Warenkorb hinzufügen
     */
    public function add(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($productId <= 0 || $quantity <= 0) {
            Session::error('Ungültige Anfrage.');
            $this->redirectBack();
            return;
        }

        $product = Product::find($productId);

        if (!$product || !$product['is_active']) {
            Session::error('Produkt nicht verfügbar.');
            $this->redirectBack();
            return;
        }

        if ($product['stock'] < $quantity) {
            Session::error('Nicht genügend Bestand verfügbar.');
            $this->redirectBack();
            return;
        }

        if (Cart::add($productId, $quantity)) {
            Session::success("{$product['name']} wurde zum Warenkorb hinzugefügt.");
        } else {
            Session::error('Fehler beim Hinzufügen zum Warenkorb.');
        }

        // AJAX-Request?
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cartCount' => Cart::count(),
                'message' => Session::getFlash('success')
            ]);
            exit;
        }

        $this->redirectBack();
    }

    /**
     * Menge aktualisieren
     */
    public function update(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if ($productId <= 0) {
            Session::error('Ungültige Anfrage.');
            redirect('/warenkorb');
            return;
        }

        if ($quantity <= 0) {
            Cart::remove($productId);
            Session::success('Produkt wurde entfernt.');
        } else {
            Cart::update($productId, $quantity);
            Session::success('Warenkorb wurde aktualisiert.');
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cartCount' => Cart::count(),
                'subtotal' => formatPrice(Cart::subtotal()),
                'tax' => formatPrice(Cart::tax()),
                'shipping' => formatPrice(Cart::shipping()),
                'total' => formatPrice(Cart::total())
            ]);
            exit;
        }

        redirect('/warenkorb');
    }

    /**
     * Produkt entfernen
     */
    public function remove(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId > 0) {
            Cart::remove($productId);
            Session::success('Produkt wurde aus dem Warenkorb entfernt.');
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'cartCount' => Cart::count(),
                'isEmpty' => Cart::isEmpty()
            ]);
            exit;
        }

        redirect('/warenkorb');
    }

    /**
     * Warenkorb leeren
     */
    public function clear(): void
    {
        Cart::clear();
        Session::success('Warenkorb wurde geleert.');
        redirect('/warenkorb');
    }

    /**
     * AJAX Request prüfen
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Zum Referrer zurückleiten
     */
    private function redirectBack(): void
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referrer);
    }
}
