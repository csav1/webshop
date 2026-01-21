<?php

namespace Models;

use Core\Database;
use Core\Session;
use Core\Auth;
use PDO;

/**
 * Cart Model - Session-basierter Warenkorb
 * 
 * Für eingeloggte Benutzer wird der Warenkorb auch in der DB gespeichert
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    /**
     * Warenkorb abrufen
     */
    public static function get(): array
    {
        Session::start();
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Produkt zum Warenkorb hinzufügen
     */
    public static function add(int $productId, int $quantity = 1): bool
    {
        $product = Product::find($productId);

        if (!$product || !$product['is_active']) {
            return false;
        }

        $cart = self::get();

        if (isset($cart[$productId])) {
            // Menge erhöhen
            $newQuantity = $cart[$productId]['quantity'] + $quantity;

            // Bestandsprüfung
            if ($newQuantity > $product['stock']) {
                $newQuantity = $product['stock'];
            }

            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            // Neues Produkt hinzufügen
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'slug' => $product['slug'],
                'price' => Product::getCurrentPrice($product),
                'original_price' => $product['price'],
                'image' => $product['image'],
                'quantity' => min($quantity, $product['stock']),
                'stock' => $product['stock']
            ];
        }

        Session::set(self::SESSION_KEY, $cart);

        // Für eingeloggte Benutzer auch in DB speichern
        if (Auth::check()) {
            self::syncToDatabase(Auth::id(), $cart);
        }

        return true;
    }

    /**
     * Menge aktualisieren
     */
    public static function update(int $productId, int $quantity): bool
    {
        $cart = self::get();

        if (!isset($cart[$productId])) {
            return false;
        }

        if ($quantity <= 0) {
            return self::remove($productId);
        }

        // Bestandsprüfung
        $product = Product::find($productId);
        if ($quantity > $product['stock']) {
            $quantity = $product['stock'];
        }

        $cart[$productId]['quantity'] = $quantity;
        Session::set(self::SESSION_KEY, $cart);

        if (Auth::check()) {
            self::syncToDatabase(Auth::id(), $cart);
        }

        return true;
    }

    /**
     * Produkt entfernen
     */
    public static function remove(int $productId): bool
    {
        $cart = self::get();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::set(self::SESSION_KEY, $cart);

            if (Auth::check()) {
                self::removeFromDatabase(Auth::id(), $productId);
            }

            return true;
        }

        return false;
    }

    /**
     * Warenkorb leeren
     */
    public static function clear(): void
    {
        Session::set(self::SESSION_KEY, []);

        if (Auth::check()) {
            self::clearDatabase(Auth::id());
        }
    }

    /**
     * Anzahl Artikel im Warenkorb
     */
    public static function count(): int
    {
        $cart = self::get();
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Anzahl verschiedener Produkte
     */
    public static function itemCount(): int
    {
        return count(self::get());
    }

    /**
     * Zwischensumme berechnen
     */
    public static function subtotal(): float
    {
        $cart = self::get();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return round($total, 2);
    }

    /**
     * MwSt berechnen
     */
    public static function tax(): float
    {
        return round(self::subtotal() * 0.19, 2);
    }

    /**
     * Versandkosten berechnen
     */
    public static function shipping(): float
    {
        // Kostenloser Versand ab 50€
        return self::subtotal() >= 50 ? 0 : 4.99;
    }

    /**
     * Gesamtsumme berechnen (Preise sind Bruttopreise - MwSt bereits enthalten)
     */
    public static function total(): float
    {
        return self::subtotal() + self::shipping();
    }

    /**
     * Prüfen ob Warenkorb leer
     */
    public static function isEmpty(): bool
    {
        return empty(self::get());
    }

    /**
     * Warenkorb als Array für Checkout
     */
    public static function getItems(): array
    {
        return array_values(self::get());
    }

    /**
     * Bestand für alle Warenkorb-Artikel aktualisieren
     */
    public static function refreshStock(): void
    {
        $cart = self::get();
        $updated = false;

        foreach ($cart as $productId => &$item) {
            $product = Product::find($productId);

            if (!$product || !$product['is_active']) {
                unset($cart[$productId]);
                $updated = true;
                continue;
            }

            $item['stock'] = $product['stock'];
            $item['price'] = Product::getCurrentPrice($product);

            if ($item['quantity'] > $product['stock']) {
                $item['quantity'] = $product['stock'];
                $updated = true;
            }
        }

        if ($updated) {
            Session::set(self::SESSION_KEY, $cart);
        }
    }

    /**
     * Warenkorb aus DB laden (nach Login)
     */
    public static function loadFromDatabase(int $userId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT ci.*, p.name, p.slug, p.price, p.sale_price, p.image, p.stock, p.is_active
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sessionCart = self::get();

        foreach ($items as $item) {
            if (!$item['is_active'])
                continue;

            $productId = $item['product_id'];
            $price = Product::getCurrentPrice($item);

            if (isset($sessionCart[$productId])) {
                // Session-Menge + DB-Menge, max. Bestand
                $sessionCart[$productId]['quantity'] = min(
                    $sessionCart[$productId]['quantity'] + $item['quantity'],
                    $item['stock']
                );
            } else {
                $sessionCart[$productId] = [
                    'product_id' => $productId,
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'price' => $price,
                    'original_price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => min($item['quantity'], $item['stock']),
                    'stock' => $item['stock']
                ];
            }
        }

        Session::set(self::SESSION_KEY, $sessionCart);
    }

    /**
     * Warenkorb in DB speichern
     */
    private static function syncToDatabase(int $userId, array $cart): void
    {
        $db = Database::getInstance();

        // Alte Items löschen
        $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Neue Items einfügen
        $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($cart as $item) {
            $stmt->execute([$userId, $item['product_id'], $item['quantity']]);
        }
    }

    /**
     * Item aus DB entfernen
     */
    private static function removeFromDatabase(int $userId, int $productId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
    }

    /**
     * DB-Warenkorb leeren
     */
    private static function clearDatabase(int $userId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
}
