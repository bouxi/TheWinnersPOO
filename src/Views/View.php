<?php

namespace App\Views;

use App\Core\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class View {
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(string $template, array $data = []): void {
        $twig = Twig::getInstance();
        echo $twig->render($template, $data);
    }
}
