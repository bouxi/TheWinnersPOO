<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Repositories\ClassGuideRepository;
use App\Views\View;

class AdminGuideController
{
    public function index(): void
    {
        $repo = new ClassGuideRepository();
        $guides = $repo->getAll();

        $view = new View();
        $view->render('admin/guides/index.html.twig', [
            'user' => Security::getCurrentUser(),
            'guides' => $guides,
        ]);
    }

    public function create(): void
    {
        $view = new View();
        $view->render('admin/guides/create.html.twig', [
            'user' => Security::getCurrentUser()
        ]);
    }

    public function store(): void
    {
        $class = trim($_POST['class'] ?? '');
        $spec = trim($_POST['spec'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($class) || empty($spec) || empty($content)) {
            $_SESSION['flash_error'] = "Tous les champs sont obligatoires.";
            Utils::redirect('/admin/guides/create');
        }

        $repo = new ClassGuideRepository();
        $repo->create($class, $spec, $content);

        $_SESSION['flash_success'] = "Guide ajouté avec succès.";
        Utils::redirect('/admin/guides');
    }

    public function edit(int $id): void
    {
        $repo = new ClassGuideRepository();
        $guide = $repo->findById($id);

        $view = new View();
        $view->render('admin/guides/edit.html.twig', [
            'user' => Security::getCurrentUser(),
            'guide' => $guide
        ]);
    }

    public function update(int $id): void
    {
        $class = trim($_POST['class'] ?? '');
        $spec = trim($_POST['spec'] ?? '');
        $content = trim($_POST['content'] ?? '');

        $repo = new ClassGuideRepository();
        $repo->update($id, $class, $spec, $content);

        $_SESSION['flash_success'] = "Guide mis à jour.";
        Utils::redirect('/admin/guides/edit/' . $id);
    }
}
