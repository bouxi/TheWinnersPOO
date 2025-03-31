<?php

namespace App\Views;

use App\Core\Twig;

class View {
    public function render(string $template, array $data = []): void {
        $twig = Twig::getInstance();
        echo $twig->render($template, $data);
    }
}
