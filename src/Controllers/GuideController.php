<?php

namespace App\Controllers;

use App\Core\Security;
use App\Repositories\ClassGuideRepository;
use App\Views\View;

class GuideController
{
    // 📚 Affiche la liste des classes avec leurs spécialisations
    public function index(): void
    {
        Security::requireAuth();

        $user = Security::getCurrentUser();

        $repo = new ClassGuideRepository();
        $guides = $repo->getAllGroupedByClass(); // On récupère les guides regroupés par classe

        $view = new View();
        $view->render('guides/classes/index.html.twig', [
            'user' => $user,
            'guides' => $guides,
        ]);
    }

    // Affiche les détails d'une classe et spécialisation
    public function show(string $class, string $spec): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        $repo = new ClassGuideRepository();
        $guide = $repo->findBySlug($class, $spec);

        if (!$guide) {
            http_response_code(404);

            $view = new View();
            $view->render('errors/404.html.twig', [
                'user' => $user,
                'message' => "Le guide pour la spécialisation \"$spec\" de la classe \"$class\" est introuvable."
            ]);
            return;
        }

        $view = new View();
        $view->render('guides/classes/show.html.twig', [
            'user' => $user,
            'guide' => $guide,
        ]);
    }



}
