<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Views\View;
use App\Repositories\GuildRepository;

class GuildController
{
    public function members(): void
    {
        Security::requireAuth();

        $user = Security::getCurrentUser();

        // 🧭 Récupération des filtres
        $classFilter = $_GET['class'] ?? null;
        $roleFilter = $_GET['role'] ?? null;
        $sort = $_GET['sort'] ?? 'username';
        $order = $_GET['order'] ?? 'asc';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // 📦 Requêtes BDD
        $repo = new GuildRepository();
        $members = $repo->findAllMembers($classFilter, $roleFilter, $sort, $order, $limit, $offset);
        $total = $repo->countAllMembers($classFilter, $roleFilter);
        $totalPages = ceil($total / $limit);

        // 🖥️ Affichage de la vue avec les bons chemins
        $view = new View();
        $view->render('guild/members.html.twig', [ // ✅ vue corrigée ici
            'members' => $members,
            'user' => $user,
            'classes' => $repo->getAvailableClasses(),
            'roles' => $repo->getAvailableRoles(),
            'currentClass' => $classFilter,
            'currentRole' => $roleFilter,
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
}
