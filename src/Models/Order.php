<?php

namespace Models;

use Core\Database;
use PDO;

/**
 * Order Model
 */
class Order extends Model
{
    protected static string $table = 'orders';

    /**
     * Bestellung nach Bestellnummer finden
     */
    public static function findByNumber(string $orderNumber): ?array
    {
        $order = self::findBy('order_number', $orderNumber);
        if ($order) {
            $order['items'] = OrderItem::findByOrder($order['id']);
        }
        return $order;
    }

    /**
     * Bestellungen eines Benutzers
     */
    public static function findByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        return self::paginate($page, $perPage, 'user_id = ?', [$userId], 'created_at', 'DESC');
    }

    /**
     * Eindeutige Bestellnummer generieren
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $db = Database::getInstance();

        // Höchste Nummer für dieses Jahr finden
        $stmt = $db->prepare("SELECT order_number FROM orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute(["ORD-{$year}-%"]);
        $last = $stmt->fetchColumn();

        if ($last) {
            $lastNum = (int) substr($last, -4);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return sprintf("ORD-%s-%04d", $year, $newNum);
    }

    /**
     * Neue Bestellung erstellen
     */
    public static function createOrder(int $userId, array $shippingData, string $paymentMethod, array $cartItems): ?array
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Gesamtsummen berechnen (Preise sind Bruttopreise - MwSt. bereits enthalten)
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            // MwSt. ist bereits im Preis enthalten, hier nur zur Anzeige berechnen
            $tax = round($subtotal - ($subtotal / 1.19), 2); // Enthaltene 19% MwSt
            $shippingCost = $subtotal >= 50 ? 0 : 4.99; // Kostenloser Versand ab 50€
            $total = $subtotal + $shippingCost; // Keine Extra-Steuer

            // Bestellung erstellen
            $orderId = self::create([
                'user_id' => $userId,
                'order_number' => self::generateOrderNumber(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'shipping_name' => $shippingData['name'],
                'shipping_street' => $shippingData['street'],
                'shipping_city' => $shippingData['city'],
                'shipping_zip' => $shippingData['zip'],
                'shipping_country' => $shippingData['country'] ?? 'Deutschland',
                'shipping_phone' => $shippingData['phone'] ?? null,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending'
            ]);

            // Bestellpositionen hinzufügen
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'] ?? null,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);

                // Bestand reduzieren
                Product::decreaseStock($item['product_id'], $item['quantity']);
            }

            $db->commit();

            return self::find($orderId);

        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Bestellstatus aktualisieren
     */
    public static function updateStatus(int $orderId, string $status): bool
    {
        return self::update($orderId, ['status' => $status]);
    }

    /**
     * Zahlungsstatus aktualisieren
     */
    public static function updatePaymentStatus(int $orderId, string $status): bool
    {
        return self::update($orderId, ['payment_status' => $status]);
    }

    /**
     * Bestellung stornieren
     */
    public static function cancel(int $orderId): bool
    {
        $order = self::find($orderId);
        if (!$order || $order['status'] === 'shipped' || $order['status'] === 'delivered') {
            return false;
        }

        // Bestand wiederherstellen
        $items = OrderItem::findByOrder($orderId);
        foreach ($items as $item) {
            Product::increaseStock($item['product_id'], $item['quantity']);
        }

        return self::update($orderId, [
            'status' => 'cancelled',
            'payment_status' => 'refunded'
        ]);
    }

    /**
     * Bestellungen nach Status
     */
    public static function byStatus(string $status): array
    {
        return self::where('status', $status, 'created_at', 'DESC');
    }

    /**
     * Umsatz-Statistiken
     */
    public static function getStats(): array
    {
        $db = Database::getInstance();

        // Gesamtumsatz
        $stmt = $db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'");
        $totalRevenue = (float) $stmt->fetchColumn();

        // Bestellungen heute
        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
        $ordersToday = (int) $stmt->fetchColumn();

        // Bestellungen diese Woche
        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE())");
        $ordersThisWeek = (int) $stmt->fetchColumn();

        // Offene Bestellungen
        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $pendingOrders = (int) $stmt->fetchColumn();

        return [
            'total_revenue' => $totalRevenue,
            'orders_today' => $ordersToday,
            'orders_this_week' => $ordersThisWeek,
            'pending_orders' => $pendingOrders,
            'total_orders' => self::count()
        ];
    }
}
