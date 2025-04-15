<?php

namespace App\Controllers;

use App\Core\Twig;
use App\Core\Controller;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Views\View;

class TipsController extends Controller {
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): void {
        $this->startSession();
        $twig = Twig::getInstance();
        echo $twig->render('tips/index.html.twig');
    }

    public function category(string $category): void {
        $twig = Twig::getInstance();
        echo $twig->render('tips/category.html.twig', ['category' => $category]);
    }

    public function show(string $category, string $id): void {
        /*
        $id = (int) $id;

        if ($id <= 0) {
            echo "ID invalide.";
            return;
        }
        */

        $twig = Twig::getInstance();
        echo $twig->render('tips/show.html.twig', ['category' => $category, 'id' => $id]);
    }

    public function addComment(string $category, int $id): void {

        $this->startSession();
        // Vérifier si l'utilisateur est connecté
        $this->requireAuth();
        $comment = $_POST['comment'] ?? '';
        // Logique pour enregistrer le commentaire
        header("Location: /tips/$category/$id");
        exit();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function addForm(): void {
        // Vérifier si session ou la l'activé si pas.
        $this->startSession();

        // Vérifier si l'utilisateur est connecté
        $this->requireAuth();
        echo "Méthode addForm() appelée"; // Débug
        $twig = Twig::getInstance();
        echo $twig->render('tips/add.html.twig');
    }

    public function add(): void
    {
        $this->startSession();
        // Vérifier si l'utilisateur est connecté
        $this->requireAuth();

        // Récupérer les données du formulaire
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? '';
        $content = $_POST['content'] ?? '';

        // Valider les données
        if (empty($title) || empty($category) || empty($content)) {
            echo "Tous les champs sont obligatoires.";
            return;
        }

        // Préparer l'insertion dans la base de données
        $pdo = \App\Core\Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO tips (title, category, content, id_author) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $category, $content, $_SESSION['user_id']]);

        // Rediriger vers la liste des astuces
        header("Location: /tips");
        exit();
    }
}
