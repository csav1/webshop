<?php

namespace Models;

use Core\Database;
use PDO;

/**
 * Product Model
 */
class Product extends Model
{
    protected static string $table = 'products';

    /**
     * Produkt nach Slug finden
     */
    public static function findBySlug(string $slug): ?array
    {
        $product = self::findBy('slug', $slug);

        if ($product) {
            // Kategorie hinzufügen
            $product['category'] = Category::find($product['category_id']);
            // Durchschnittsbewertung hinzufügen
            $product['rating'] = Review::averageRating($product['id']);
            $product['review_count'] = Review::countForProduct($product['id']);
        }

        return $product;
    }

    /**
     * Alle aktiven Produkte
     */
    public static function active(): array
    {
        return self::where('is_active', true, 'created_at', 'DESC');
    }

    /**
     * Featured Produkte
     */
    public static function featured(int $limit = 8): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Produkte nach Kategorie
     */
    public static function byCategory(int $categoryId, int $page = 1, int $perPage = 12): array
    {
        return self::paginate($page, $perPage, 'category_id = ? AND is_active = 1', [$categoryId], 'created_at', 'DESC');
    }

    /**
     * Produkte nach Kategorie-Slug
     */
    public static function byCategorySlug(string $slug, int $page = 1, int $perPage = 12): array
    {
        $category = Category::findBySlug($slug);
        if (!$category)
            return ['items' => [], 'total' => 0, 'totalPages' => 0, 'currentPage' => $page];

        return self::byCategory($category['id'], $page, $perPage);
    }

    /**
     * Produkte suchen
     */
    public static function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $db = Database::getInstance();
        $searchTerm = "%{$query}%";
        $offset = ($page - 1) * $perPage;

        // Produkte finden
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 
            AND (p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)
            ORDER BY p.name ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $perPage, $offset]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total zählen
        $countStmt = $db->prepare("
            SELECT COUNT(*) FROM products 
            WHERE is_active = 1 
            AND (name LIKE ? OR description LIKE ? OR short_description LIKE ?)
        ");
        $countStmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $total = (int) $countStmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total,
            'totalPages' => (int) ceil($total / $perPage),
            'currentPage' => $page,
            'query' => $query
        ];
    }

    /**
     * Slug generieren
     */
    public static function generateSlug(string $name): string
    {
        $slug = mb_strtolower($name);
        $slug = preg_replace('/[äÄ]/', 'ae', $slug);
        $slug = preg_replace('/[öÖ]/', 'oe', $slug);
        $slug = preg_replace('/[üÜ]/', 'ue', $slug);
        $slug = preg_replace('/ß/', 'ss', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $originalSlug = $slug;
        $counter = 1;
        while (self::findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Neues Produkt erstellen
     */
    public static function createProduct(array $data): int
    {
        $data['slug'] = self::generateSlug($data['name']);
        return self::create($data);
    }

    /**
     * Bestand reduzieren
     */
    public static function decreaseStock(int $productId, int $quantity): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        return $stmt->execute([$quantity, $productId, $quantity]);
    }

    /**
     * Bestand erhöhen
     */
    public static function increaseStock(int $productId, int $quantity): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$quantity, $productId]);
    }

    /**
     * Produkte mit niedrigem Bestand
     */
    public static function lowStock(int $threshold = 10): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE stock <= ? AND is_active = 1 ORDER BY stock ASC");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ähnliche Produkte
     */
    public static function similar(int $productId, int $limit = 4): array
    {
        $product = self::find($productId);
        if (!$product)
            return [];

        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
            ORDER BY RAND()
            LIMIT ?
        ");
        $stmt->execute([$product['category_id'], $productId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aktuellen Preis abrufen (Sale oder Normal)
     */
    public static function getCurrentPrice(array $product): float
    {
        if (!empty($product['sale_price']) && $product['sale_price'] > 0) {
            return (float) $product['sale_price'];
        }
        return (float) $product['price'];
    }

    /**
     * Prüfen ob Produkt im Sale
     */
    public static function isOnSale(array $product): bool
    {
        return !empty($product['sale_price']) && $product['sale_price'] > 0;
    }

    /**
     * Paginierte Produkte für Admin mit Kategorie-Namen
     */
    public static function adminPaginate(int $page = 1, int $perPage = 20): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = self::count();
        $totalPages = (int) ceil($total / $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
    }
}
