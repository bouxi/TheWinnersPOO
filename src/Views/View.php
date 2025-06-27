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
        $twig = App::getTwig(); // On récupère Twig injecté via App::setTwig()
        echo $twig->render($template, $data);
    }
}
