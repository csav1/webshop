<?php

namespace Core;

use Models\User;

/**
 * Auth - Authentifizierungs-Handler
 * 
 * Features:
 * - Login/Logout
 * - Passwort Hashing
 * - Remember Me (optional)
 * - Rollen-Prüfung
 */
class Auth
{
    private static ?array $user = null;

    /**
     * Benutzer einloggen
     */
    public static function login(array $user, bool $remember = false): void
    {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);

        self::$user = $user;

        // Remember Me Cookie setzen (optional)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            // Token in DB speichern und Cookie setzen
            // Implementierung für Remember Me Token
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true);
        }
    }

    /**
     * Benutzer ausloggen
     */
    public static function logout(): void
    {
        // Remember Cookie löschen
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        Session::destroy();
        self::$user = null;
    }

    /**
     * Prüfen ob eingeloggt
     */
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    /**
     * Prüfen ob Gast
     */
    public static function guest(): bool
    {
        return !self::check();
    }

    /**
     * Aktuellen Benutzer abrufen
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        if (self::$user === null) {
            $userId = Session::get('user_id');
            self::$user = User::find($userId);
        }

        return self::$user;
    }

    /**
     * Benutzer-ID abrufen
     */
    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    /**
     * Prüfen ob Admin
     */
    public static function isAdmin(): bool
    {
        return Session::get('user_role') === 'admin';
    }

    /**
     * Prüfen ob bestimmte Rolle
     */
    public static function hasRole(string $role): bool
    {
        return Session::get('user_role') === $role;
    }

    /**
     * Login-Versuch mit Credentials
     */
    public static function attempt(string $email, string $password, bool $remember = false): bool
    {
        $user = User::findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!$user['is_active']) {
            Session::error('Ihr Konto wurde deaktiviert.');
            return false;
        }

        if (!self::verifyPassword($password, $user['password_hash'])) {
            return false;
        }

        self::login($user, $remember);
        return true;
    }

    /**
     * Passwort hashen
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Passwort verifizieren
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Prüfen ob Passwort-Rehash nötig
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Auth-Check mit Redirect
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            Session::flash('intended_url', $_SERVER['REQUEST_URI']);
            Session::error('Bitte melden Sie sich an.');
            redirect('/anmelden');
        }
    }

    /**
     * Admin-Check mit Redirect
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();

        if (!self::isAdmin()) {
            Session::error('Zugriff verweigert.');
            redirect('/');
        }
    }

    /**
     * Gast-Check mit Redirect
     */
    public static function requireGuest(): void
    {
        if (self::check()) {
            redirect('/');
        }
    }
}
