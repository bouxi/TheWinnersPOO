<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Utils;
use App\Repositories\ApplicationRepository;
use App\Views\View;

class ApplicationController
{
    // ✅ Données réutilisables pour la vue
    private array $classData = [
        'Guerrier' => ['Armes', 'Fureur', 'Protection'],
        'Paladin' => ['Sacré', 'Protection', 'Vindicte'],
        'Chasseur' => ['Maîtrise des bêtes', 'Précision', 'Survie'],
        'Voleur' => ['Assassinat', 'Combat', 'Finesse'],
        'Prêtre' => ['Discipline', 'Sacré', 'Ombre'],
        'Chevalier de la mort' => ['Sang', 'Givre', 'Impie'],
        'Chaman' => ['Élémentaire', 'Amélioration', 'Restauration'],
        'Mage' => ['Arcanes', 'Feu', 'Givre'],
        'Démoniste' => ['Affliction', 'Démonologie', 'Destruction'],
        'Druide' => ['Équilibre', 'Farouche', 'Restauration', 'Gardien'],
    ];

    // 🧾 Affiche le formulaire de candidature
    public function showForm(): void
    {
        Auth::requireRole(['visitor']); // Seuls les visiteurs peuvent accéder à ce formulaire

        $repo = new ApplicationRepository();

        // 🔒 Si l'utilisateur a déjà rejoint la guilde, on bloque l'accès au formulaire
        if ($repo->hasJoinedGuild($_SESSION['user']['id'])) {
            $_SESSION['flash_error'] = "Vous avez déjà rejoint la guilde.";
            Utils::redirect('/user/profile');
        }

        $view = new View();
        $view->render('guild/apply.html.twig', [
            'classes' => $this->classData
        ]);
    }


    // 📨 Traite l’envoi de la candidature
    public function submit(): void
    {
        Auth::requireRole(['visitor']);

        $class = trim($_POST['class'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $playtime = trim($_POST['playtime'] ?? '');
        $availability = trim($_POST['availability'] ?? '');
        $motivation = trim($_POST['motivation'] ?? '');

        if (empty($class) || empty($specialization) || empty($motivation)) {
            $view = new View();
            $view->render('guild/apply.html.twig', [
                'error' => 'Tous les champs obligatoires doivent être remplis.',
                'classes' => $this->classData,
                'old' => compact('class', 'specialization', 'playtime', 'availability', 'motivation')
            ]);
            return;
        }

        $repo = new ApplicationRepository();
        $repo->create($_SESSION['user']['id'], $class, $specialization, $playtime, $availability, $motivation);

        $_SESSION['flash_success'] = "Votre candidature a bien été envoyée.";
        Utils::redirect('/user/profile');
    }
}
