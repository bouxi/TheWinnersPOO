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

        // Injection automatique de l'utilisateur connecté + messages flash
        $data['app']['user'] = $_SESSION['user'] ?? null;
        $data['flash_success'] = $_SESSION['flash_success'] ?? null;
        $data['flash_error'] = $_SESSION['flash_error'] ?? null;

        // Supprime les messages flash après affichage (optionnel mais recommandé)
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        echo $twig->render($template, $data);
    }

}
