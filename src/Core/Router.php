<?php

namespace App\Core;

use App\Controllers\HomeController;

class Router {
    public function handleRequest() {
        $path = $_SERVER['REQUEST_URI'];

        switch ($path) {
            case '/':
                $controller = new HomeController();
                $controller->index();
                break;
            default:
                echo "404 - Page non trouvée";
                break;
        }
    }
}
