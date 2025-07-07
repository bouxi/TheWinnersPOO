<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Utils;
use App\Repositories\ApplicationRepository;
use App\Views\View;

class AdminApplicationController
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $repo = new ApplicationRepository();

        // 🔍 Récupère le statut filtré via l'URL (ex: ?status=accepted)
        $status = $_GET['status'] ?? null;
        $allowedStatuses = ['pending', 'accepted', 'refused'];
        $filterStatus = in_array($status, $allowedStatuses) ? $status : null;

        // 📄 Pagination
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // 📦 Récupération des candidatures filtrées et paginées
        $applications = $repo->findAllFiltered($filterStatus, $perPage, $offset);

        // 📊 Compte total pour la pagination
        $total = $repo->countFiltered($filterStatus);
        $totalPages = (int)ceil($total / $perPage);

        $view = new View();
        $view->render('admin/applications/index.html.twig', [
            'applications' => $applications,
            'page' => $page,
            'totalPages' => $totalPages,
            'filter_status' => $filterStatus
        ]);
    }

    public function updateStatus(): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            $_SESSION['flash_error'] = "Paramètres manquants.";
            Utils::redirect('/admin/applications');
        }

        $repo = new ApplicationRepository();
        try {
            $repo->updateStatus((int)$id, $status);
            $_SESSION['flash_success'] = "Statut mis à jour avec succès.";
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }

        Utils::redirect('/admin/applications');
    }

    public function show(int $id): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $repo = new ApplicationRepository();
        $app = $repo->findById($id);

        if (!$app) {
            $_SESSION['flash_error'] = "Candidature introuvable.";
            Utils::redirect('/admin/applications');
        }

        $view = new View();
        $view->render('admin/applications/show.html.twig', [
            'app' => $app
        ]);
    }

}
