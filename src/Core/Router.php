<?php

namespace Core;

/**
 * Router - URL-Routing für SEO-freundliche URLs
 * 
 * Unterstützt:
 * - GET, POST, PUT, DELETE Methoden
 * - URL-Parameter (z.B. /produkt/{slug})
 * - Route-Gruppen (z.B. /admin/...)
 */
class Router
{
    private array $routes = [];
    private array $params = [];
    private string $currentPrefix = '';
    private array $currentMiddleware = [];

    /**
     * GET Route registrieren
     */
    public function get(string $path, array|callable $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * POST Route registrieren
     */
    public function post(string $path, array|callable $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * PUT Route registrieren
     */
    public function put(string $path, array|callable $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * DELETE Route registrieren
     */
    public function delete(string $path, array|callable $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Route-Gruppe mit Prefix und/oder Middleware
     */
    public function group(array $options, callable $callback): void
    {
        $previousPrefix = $this->currentPrefix;
        $previousMiddleware = $this->currentMiddleware;

        if (isset($options['prefix'])) {
            $this->currentPrefix .= $options['prefix'];
        }

        if (isset($options['middleware'])) {
            $this->currentMiddleware = array_merge(
                $this->currentMiddleware,
                (array) $options['middleware']
            );
        }

        $callback($this);

        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
    }

    /**
     * Route hinzufügen
     */
    private function addRoute(string $method, string $path, array|callable $handler): self
    {
        $fullPath = $this->currentPrefix . $path;

        $this->routes[$method][$fullPath] = [
            'handler' => $handler,
            'middleware' => $this->currentMiddleware
        ];

        return $this;
    }

    /**
     * URL-Pfad normalisieren
     */
    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        return $path === '' ? '/' : '/' . $path;
    }

    /**
     * Route-Pattern in Regex umwandeln
     */
    private function pathToRegex(string $path): string
    {
        // {param} -> (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Request dispatchen
     */
    public function dispatch(string $uri, string $method): mixed
    {
        $uri = $this->normalizePath(parse_url($uri, PHP_URL_PATH));
        $method = strtoupper($method);

        // PUT/DELETE über POST mit _method Parameter
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        if (!isset($this->routes[$method])) {
            return $this->handleNotFound();
        }

        foreach ($this->routes[$method] as $routePath => $routeConfig) {
            $pattern = $this->pathToRegex($this->normalizePath($routePath));

            if (preg_match($pattern, $uri, $matches)) {
                // Parameter extrahieren
                $this->params = array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);

                // Middleware ausführen
                foreach ($routeConfig['middleware'] as $middleware) {
                    $result = $this->runMiddleware($middleware);
                    if ($result !== true) {
                        return $result;
                    }
                }

                // Handler ausführen
                return $this->runHandler($routeConfig['handler']);
            }
        }

        return $this->handleNotFound();
    }

    /**
     * Middleware ausführen
     */
    private function runMiddleware(string $middleware): mixed
    {
        return match ($middleware) {
            'auth' => Auth::check() ? true : $this->redirect('/anmelden'),
            'admin' => Auth::isAdmin() ? true : $this->redirect('/'),
            'guest' => !Auth::check() ? true : $this->redirect('/'),
            default => true
        };
    }

    /**
     * Handler ausführen
     */
    private function runHandler(array|callable $handler): mixed
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $this->params);
        }

        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} nicht gefunden");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Methode {$method} in {$controllerClass} nicht gefunden");
        }

        return call_user_func_array([$controller, $method], $this->params);
    }

    /**
     * 404 Error behandeln
     */
    private function handleNotFound(): never
    {
        http_response_code(404);
        View::render('errors/404', ['title' => 'Seite nicht gefunden']);
        exit;
    }

    /**
     * Redirect Helper
     */
    public function redirect(string $path): never
    {
        if (!str_starts_with($path, 'http')) {
            $path = url($path);
        }
        header("Location: {$path}");
        exit;
    }

    /**
     * URL-Parameter abrufen
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Einzelnen Parameter abrufen
     */
    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }
}
