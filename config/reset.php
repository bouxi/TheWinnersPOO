<?php
Database::reset(); // Réinitialise l'ancienne connexion
$pdo = Database::getConnection(); // Etablie une nouvelle
// php cli.php db:reset
/*
php cli.php test:env
# → App: TheWinners
# → Env : dev

*/
// crée un utilisateur
/*
php cli.php user:create

Nom d'utilisateur : Bouxi
Adresse e-mail : bouxi@example.com
Mot de passe : monSuperMotDePasse
Rôle (ex: admin) [admin par défaut] :
Date de naissance (YYYY-MM-DD) : 1986-12-24

✅ Utilisateur 'Bouxi' créé avec succès.

*/

// tester la connexion a la base de données

/*
php cli.php db:test

✅ Connexion à la base de données : OK


❌ Erreur DB : SQLSTATE[HY000] [1045] Access denied for user 'root'...

*/

// Réinitialiser la connexion PDO

/*
php cli.php db:reset
 */

// Voir les routes connues

/*
 *  php cli.php route:list

 */