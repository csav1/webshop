<?php

namespace Models;

use Core\Database;
use PDO;

/**
 * Model - Basis-Klasse für alle Models
 * 
 * Enthält gemeinsame CRUD-Operationen
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    /**
     * Alle Datensätze abrufen
     */
    public static function all(array $columns = ['*'], string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $db = Database::getInstance();
        $cols = implode(', ', $columns);
        $stmt = $db->query("SELECT {$cols} FROM " . static::$table . " ORDER BY {$orderBy} {$orderDir}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Einzelnen Datensatz nach ID finden
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Datensatz nach Spalte finden
     */
    public static function findBy(string $column, mixed $value): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE {$column} = ?");
        $stmt->execute([$value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Mehrere Datensätze nach Spalte finden
     */
    public static function where(string $column, mixed $value, string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE {$column} = ? ORDER BY {$orderBy} {$orderDir}");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Neuen Datensatz erstellen
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $db->prepare("INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));

        return (int) $db->lastInsertId();
    }

    /**
     * Datensatz aktualisieren
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';

        $stmt = $db->prepare("UPDATE " . static::$table . " SET {$sets} WHERE " . static::$primaryKey . " = ?");
        $values = array_values($data);
        $values[] = $id;

        return $stmt->execute($values);
    }

    /**
     * Datensatz löschen
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Anzahl der Datensätze
     */
    public static function count(string $where = '1=1', array $params = []): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM " . static::$table . " WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Paginierte Datensätze abrufen
     */
    public static function paginate(int $page = 1, int $perPage = 12, string $where = '1=1', array $params = [], string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE {$where} ORDER BY {$orderBy} {$orderDir} LIMIT {$perPage} OFFSET {$offset}");
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = self::count($where, $params);
        $totalPages = (int) ceil($total / $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ];
    }

    /**
     * Prüfen ob Datensatz existiert
     */
    public static function exists(int $id): bool
    {
        return self::find($id) !== null;
    }
}
