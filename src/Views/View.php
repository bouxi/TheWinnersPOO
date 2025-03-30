<?php

namespace App\Views;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View {
    private Environment $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);
    }

    public function render(string $template, array $data = []): void {
        echo $this->twig->render($template, $data);
    }
}
