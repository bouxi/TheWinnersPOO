<?php

namespace App\Views;

use App\Core\App;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class View {
    /**
     * Rend un template Twig avec les données fournies
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(string $template, array $data = []): void {
        $twig = App::getTwig();

        // Injection automatique de l'utilisateur connecté dans toutes les vues Twig
        $data['app']['user'] = $_SESSION['user'] ?? null;

        echo $twig->render($template, $data);
    }
}
