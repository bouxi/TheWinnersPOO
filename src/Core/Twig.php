<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Twig {
    private static ?Environment $instance = null;

    public static function getInstance(): Environment {
        if (self::$instance === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../../templates');
            $twig = new Environment($loader);

            // Ajouter la fonction 'asset'
            $twig->addFunction(new TwigFunction('asset', function ($asset) {
                return sprintf('/assets/%s', ltrim($asset, '/'));
            }));

            self::$instance = $twig;
        }

        return self::$instance;
    }
}
