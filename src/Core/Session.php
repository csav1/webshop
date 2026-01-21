<?php

namespace Core;

/**
 * Session - Sichere Session-Verwaltung
 * 
 * Features:
 * - Flash Messages
 * - CSRF Token
 * - Sichere Session-Konfiguration
 */
class Session
{
    private static bool $started = false;

    /**
     * Session starten
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../../config/app.php';
            
            // Sichere Session-Einstellungen
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', $config['session']['same_site'] ?? 'Lax');
            ini_set('session.use_strict_mode', '1');
            
            // Cookie Pfad explizit setzen
            $path = $config['session']['path'] ?? '/';
            session_set_cookie_params([
                'lifetime' => ($config['session']['lifetime'] ?? 120) * 60,
                'path' => $path,
                'domain' => '', // Current domain
                'secure' => $config['session']['secure'] ?? false,
                'httponly' => true,
                'samesite' => $config['session']['same_site'] ?? 'Lax'
            ]);

            session_start();
        }

        self::$started = true;

        // Flash Messages nach dem Lesen löschen
        self::processFlash();
    }

    /**
     * Session-Wert setzen
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Session-Wert abrufen
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Session-Wert löschen
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Prüfen ob Key existiert
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Flash Message setzen (nur für nächsten Request)
     */
    public static function flash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash_new'][$key] = $value;
    }

    /**
     * Flash Messages verarbeiten
     */
    private static function processFlash(): void
    {
        // Alte Flash Messages löschen
        if (isset($_SESSION['_flash'])) {
            unset($_SESSION['_flash']);
        }

        // Neue Flash Messages für diesen Request verfügbar machen
        if (isset($_SESSION['_flash_new'])) {
            $_SESSION['_flash'] = $_SESSION['_flash_new'];
            unset($_SESSION['_flash_new']);
        }
    }

    /**
     * Flash Message abrufen
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION['_flash'][$key] ?? $default;
    }

    /**
     * Alle Flash Messages abrufen
     */
    public static function getAllFlash(): array
    {
        self::start();
        return $_SESSION['_flash'] ?? [];
    }

    /**
     * CSRF Token generieren
     */
    public static function generateCsrfToken(): string
    {
        self::start();

        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * CSRF Token validieren
     */
    public static function validateCsrfToken(?string $token): bool
    {
        self::start();

        if (!$token || !isset($_SESSION['_csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * CSRF Token regenerieren
     */
    public static function regenerateCsrfToken(): string
    {
        self::start();
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf_token'];
    }

    /**
     * Session ID regenerieren (nach Login)
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Session komplett zerstören
     */
    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Erfolgs-Flash setzen
     */
    public static function success(string $message): void
    {
        self::flash('success', $message);
    }

    /**
     * Error-Flash setzen
     */
    public static function error(string $message): void
    {
        self::flash('error', $message);
    }

    /**
     * Warning-Flash setzen
     */
    public static function warning(string $message): void
    {
        self::flash('warning', $message);
    }

    /**
     * Info-Flash setzen
     */
    public static function info(string $message): void
    {
        self::flash('info', $message);
    }
}
