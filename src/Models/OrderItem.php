<?php

namespace Models;

use Core\Database;
use PDO;

/**
 * OrderItem Model
 */
class OrderItem extends Model
{
    protected static string $table = 'order_items';

    /**
     * Positionen einer Bestellung abrufen
     */
    public static function findByOrder(int $orderId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT oi.*, p.slug as product_slug
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Prüfen ob Benutzer ein Produkt gekauft hat
     */
    public static function hasUserPurchased(int $userId, int $productId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = ? AND oi.product_id = ? AND o.status IN ('processing', 'shipped', 'delivered')
        ");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Meistverkaufte Produkte
     */
    public static function bestSellers(int $limit = 10): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                p.*,
                SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status NOT IN ('cancelled') AND p.is_active = 1
            GROUP BY p.id
            ORDER BY total_sold DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
