<?php

namespace Core;

/**
 * View - Template Rendering Engine
 * 
 * Features:
 * - Layout-System
 * - Partials
 * - Automatisches Escaping
 * - SEO Meta-Tags
 */
class View
{
    private static string $viewsPath = '';
    private static array $shared = [];
    private static ?string $layout = 'layouts/main';

    /**
     * Views-Pfad setzen
     */
    public static function setViewsPath(string $path): void
    {
        self::$viewsPath = rtrim($path, '/');
    }

    /**
     * Standard-Layout setzen
     */
    public static function setLayout(?string $layout): void
    {
        self::$layout = $layout;
    }

    /**
     * Globale View-Daten setzen
     */
    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    /**
     * View rendern
     */
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        $layout = $layout ?? self::$layout;

        // Globale Daten mergen
        $data = array_merge(self::$shared, $data);

        // Standard SEO-Daten
        $data['seo'] = array_merge([
            'title' => 'NBA Webshop',
            'description' => 'Ihr Online-Shop für offizielle NBA Merchandise',
            'canonical' => url(request_uri()),
            'image' => url('/assets/images/og-default.jpg'),
            'type' => 'website'
        ], $data['seo'] ?? []);

        // Auth-Daten für Views verfügbar machen
        $data['auth'] = [
            'check' => Auth::check(),
            'user' => Auth::user(),
            'isAdmin' => Auth::isAdmin()
        ];

        // Flash Messages
        $data['flash'] = Session::getAllFlash();

        // CSRF Token
        $data['csrf_token'] = Session::generateCsrfToken();

        // View-Content rendern
        $content = self::renderView($view, $data);

        if ($layout) {
            // In Layout einbetten
            $data['content'] = $content;
            echo self::renderView($layout, $data);
        } else {
            echo $content;
        }
    }

    /**
     * View-Datei rendern
     */
    private static function renderView(string $view, array $data): string
    {
        $viewFile = self::$viewsPath . '/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View '{$view}' nicht gefunden: {$viewFile}");
        }

        extract($data);

        ob_start();
        include $viewFile;
        return ob_get_clean();
    }

    /**
     * Partial rendern
     */
    public static function partial(string $partial, array $data = []): void
    {
        $data = array_merge(self::$shared, $data);
        echo self::renderView('partials/' . $partial, $data);
    }

    /**
     * HTML escapen
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * JSON für JavaScript ausgeben
     */
    public static function json(mixed $data): string
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * CSRF Hidden Field
     */
    public static function csrf(): string
    {
        $token = Session::generateCsrfToken();
        return '<input type="hidden" name="_csrf_token" value="' . self::e($token) . '">';
    }

    /**
     * Method Spoofing Field
     */
    public static function method(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }

    /**
     * Asset URL generieren
     */
    public static function asset(string $path): string
    {
        return url('/assets/' . ltrim($path, '/'));
    }

    /**
     * Pagination rendern
     */
    public static function pagination(int $currentPage, int $totalPages, string $baseUrl): string
    {
        if ($totalPages <= 1) {
            return '';
        }

        $html = '<nav class="pagination" aria-label="Seitennavigation"><ul class="pagination-list">';

        // Zurück-Button
        if ($currentPage > 1) {
            $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="pagination-prev">&laquo; Zurück</a></li>';
        }

        // Seitenzahlen
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i === $currentPage) {
                $html .= '<li><span class="pagination-current">' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
            }
        }

        // Weiter-Button
        if ($currentPage < $totalPages) {
            $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="pagination-next">Weiter &raquo;</a></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }
}
