<?php

namespace App\Core;

class Router {
    private array $routes = [];

    // Ajouter une route avec la méthode HTTP
    public function addRoute(string $method, string $path, callable $callback): void {
        $this->routes[] = ['method' => $method, 'path' => $path, 'callback' => $callback];
    }

    // Récupérer toutes les routes (pour le debug)
    public function getRoutes(): array {
        return $this->routes;
    }

    // Gérer la requête et router vers le bon contrôleur
    public function handleRequest(): void {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // ✅ correction ici
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($method === $route['method'] && preg_match($this->convertPathToRegex($route['path']), $path, $matches)) {
                array_shift($matches); // on enlève le chemin complet
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page non trouvée";
    }


    // Convertir le chemin en expression régulière pour prendre en charge les paramètres dynamiques
    private function convertPathToRegex(string $path): string {
        return '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $path) . '$#';
    }
}
