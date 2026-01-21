<?php

namespace Models;

use Core\Database;
use Core\Auth;
use PDO;

/**
 * User Model
 */
class User extends Model
{
    protected static string $table = 'users';

    /**
     * Benutzer nach E-Mail finden
     */
    public static function findByEmail(string $email): ?array
    {
        return self::findBy('email', $email);
    }

    /**
     * Benutzer nach Google ID finden
     */
    public static function findByGoogleId(string $googleId): ?array
    {
        return self::findBy('google_id', $googleId);
    }

    /**
     * Neuen Benutzer registrieren
     */
    public static function register(array $data): int
    {
        $userData = [
            'email' => $data['email'],
            'password_hash' => Auth::hashPassword($data['password']),
            'name' => $data['name'],
            'role' => 'user',
            'is_active' => true
        ];

        return self::create($userData);
    }

    /**
     * Benutzer über Google OAuth erstellen/aktualisieren
     */
    public static function createOrUpdateFromGoogle(array $googleData): array
    {
        // 1. Check by Google ID
        $existing = self::findByGoogleId($googleData['id']);

        if ($existing) {
            // Update existing Google user
            self::update($existing['id'], [
                'avatar' => $googleData['picture'] ?? $existing['avatar'],
                'name' => $googleData['name'] ?? $existing['name']
            ]);
            return self::find($existing['id']);
        }

        // 2. Check by Email (Account Linking)
        $existingByEmail = self::findByEmail($googleData['email']);

        if ($existingByEmail) {
            // Link Google ID to existing user
            self::update($existingByEmail['id'], [
                'google_id' => $googleData['id'],
                'avatar' => $googleData['picture'] ?? $existingByEmail['avatar'] // Only update avatar if not set? Or always? Let's treat valid google avatar as good.
            ]);
            return self::find($existingByEmail['id']);
        }

        // 3. Create new user
        $id = self::create([
            'email' => $googleData['email'],
            'name' => $googleData['name'],
            'google_id' => $googleData['id'],
            'avatar' => $googleData['picture'] ?? null,
            'role' => 'user',
            'is_active' => true
        ]);

        return self::find($id);
    }

    /**
     * Passwort aktualisieren
     */
    public static function updatePassword(int $userId, string $newPassword): bool
    {
        return self::update($userId, [
            'password_hash' => Auth::hashPassword($newPassword)
        ]);
    }

    /**
     * Alle aktiven Benutzer abrufen
     */
    public static function active(): array
    {
        return self::where('is_active', true, 'name', 'ASC');
    }

    /**
     * Alle Admins abrufen
     */
    public static function admins(): array
    {
        return self::where('role', 'admin', 'name', 'ASC');
    }

    /**
     * Benutzer deaktivieren
     */
    public static function deactivate(int $userId): bool
    {
        return self::update($userId, ['is_active' => 0]);
    }

    /**
     * Benutzer aktivieren
     */
    public static function activate(int $userId): bool
    {
        return self::update($userId, ['is_active' => 1]);
    }

    /**
     * Zur Admin-Rolle befördern
     */
    public static function promoteToAdmin(int $userId): bool
    {
        return self::update($userId, ['role' => 'admin']);
    }

    /**
     * Benutzer mit Bestellstatistik
     */
    public static function withOrderStats(int $userId): ?array
    {
        $user = self::find($userId);
        if (!$user)
            return null;

        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as order_count,
                COALESCE(SUM(total), 0) as total_spent
            FROM orders 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return array_merge($user, $stats);
    }
}
