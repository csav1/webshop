<?php

namespace Models;

use Core\Database;
use PDO;

/**
 * Category Model
 */
class Category extends Model
{
    protected static string $table = 'categories';

    /**
     * Kategorie nach Slug finden
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    /**
     * Alle aktiven Kategorien
     */
    public static function active(): array
    {
        return self::where('is_active', true, 'name', 'ASC');
    }

    /**
     * Kategorie mit Produktanzahl
     */
    public static function withProductCount(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        // Einzigartigkeit prüfen
        $originalSlug = $slug;
        $counter = 1;
        while (self::findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Neue Kategorie erstellen
     */
    public static function createCategory(array $data): int
    {
        $data['slug'] = self::generateSlug($data['name']);
        return self::create($data);
    }

    /**
     * Kategorie aktualisieren
     */
    public static function updateCategory(int $id, array $data): bool
    {
        if (isset($data['name'])) {
            $existing = self::find($id);
            if ($existing && $existing['name'] !== $data['name']) {
                $data['slug'] = self::generateSlug($data['name']);
            }
        }
        return self::update($id, $data);
    }
}
