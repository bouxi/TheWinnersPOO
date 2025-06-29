<?php

try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=thewinners", "root", "");
    echo "Connexion réussie à la base de données !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
