<?php

namespace Models;

use Core\Database;
use Core\Auth;
use PDO;

/**
 * Review Model
 */
class Review extends Model
{
    protected static string $table = 'reviews';

    /**
     * Bewertungen für ein Produkt
     */
    public static function findByProduct(int $productId, int $page = 1, int $perPage = 10, string $sortBy = 'helpful'): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        // Determine ORDER BY clause based on sortBy
        $orderBy = match($sortBy) {
            'newest' => 'r.created_at DESC',
            'oldest' => 'r.created_at ASC',
            'highest' => 'r.rating DESC, r.created_at DESC',
            'lowest' => 'r.rating ASC, r.created_at DESC',
            default => 'r.helpful_count DESC, r.created_at DESC' // 'helpful'
        };

        $stmt = $db->prepare("
            SELECT r.*, u.name as user_name, u.avatar as user_avatar
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ? AND r.is_approved = 1
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$productId, $perPage, $offset]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = self::countForProduct($productId);

        return [
            'items' => $items,
            'total' => $total,
            'totalPages' => (int) ceil($total / $perPage),
            'currentPage' => $page
        ];
    }

    /**
     * Durchschnittsbewertung für Produkt
     */
    public static function averageRating(int $productId): float
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT AVG(rating) FROM reviews WHERE product_id = ? AND is_approved = 1");
        $stmt->execute([$productId]);
        return round((float) $stmt->fetchColumn(), 1);
    }

    /**
     * Anzahl Bewertungen für Produkt
     */
    public static function countForProduct(int $productId): int
    {
        return self::count('product_id = ? AND is_approved = 1', [$productId]);
    }

    /**
     * Rating-Verteilung für Produkt
     */
    public static function ratingDistribution(int $productId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT rating, COUNT(*) as count
            FROM reviews
            WHERE product_id = ? AND is_approved = 1
            GROUP BY rating
            ORDER BY rating DESC
        ");
        $stmt->execute([$productId]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Vollständige Verteilung mit 0 für fehlende Ratings
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $results[$i] ?? 0;
        }
        return $distribution;
    }

    /**
     * Bewertung erstellen
     */
    public static function createReview(int $productId, int $userId, array $data): int
    {
        // Prüfen ob Benutzer das Produkt gekauft hat
        $verifiedPurchase = OrderItem::hasUserPurchased($userId, $productId);

        return self::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'verified_purchase' => $verifiedPurchase,
            'is_approved' => true // Auto-Approve, optional Moderation
        ]);
    }

    /**
     * Prüfen ob Benutzer bereits bewertet hat
     */
    public static function hasUserReviewed(int $userId, int $productId): bool
    {
        return self::count('user_id = ? AND product_id = ?', [$userId, $productId]) > 0;
    }

    /**
     * Als hilfreich markieren
     */
    public static function markHelpful(int $reviewId, int $userId): bool
    {
        $db = Database::getInstance();

        // Prüfen ob bereits markiert
        $stmt = $db->prepare("SELECT id FROM review_helpful WHERE review_id = ? AND user_id = ?");
        $stmt->execute([$reviewId, $userId]);
        if ($stmt->fetch()) {
            return false; // Bereits markiert
        }

        // Markierung hinzufügen
        $stmt = $db->prepare("INSERT INTO review_helpful (review_id, user_id) VALUES (?, ?)");
        $stmt->execute([$reviewId, $userId]);

        // Counter erhöhen
        $stmt = $db->prepare("UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = ?");
        return $stmt->execute([$reviewId]);
    }

    /**
     * Prüfen ob Benutzer als hilfreich markiert hat
     */
    public static function hasMarkedHelpful(int $reviewId, int $userId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM review_helpful WHERE review_id = ? AND user_id = ?");
        $stmt->execute([$reviewId, $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Neueste Bewertungen
     */
    public static function latest(int $limit = 5): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, u.name as user_name, p.name as product_name, p.slug as product_slug
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            JOIN products p ON r.product_id = p.id
            WHERE r.is_approved = 1
            ORDER BY r.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
