<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function hydrateUser(array $data): User
    {
        // 🔸 Création d'un nouvel utilisateur avec les données principales
        $user = new User(
            $data['username'],
            $data['email'],
            $data['password'],
            $data['role'],
            true // ✅ on indique que le mot de passe est déjà hashé
        );

        // 🔸 Ajout des données secondaires (optionnelles)
        $user->setId((int)$data['id']);

        if (!empty($data['birthdate'])) {
            $user->setBirthdate($data['birthdate']);
        }

        if (!empty($data['date_inscription'])) {
            $user->setDateInscription($data['date_inscription']);
        }

        if (!empty($data['avatar'])) {
            $user->setAvatar($data['avatar']);
        }

        if (!empty($data['reset_token'])) {
            $user->setResetToken($data['reset_token']);
        }

        if (!empty($data['reset_token_expires_at'])) {
            $user->setResetTokenExpiresAt($data['reset_token_expires_at']);
        }

        return $user;
    }



    public function findByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function save(User $user): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, role, birthdate, avatar, date_inscription)
            VALUES (:username, :email, :password, :role, :birthdate, :avatar, NOW())
        ");

        return $stmt->execute([
            'username'          => $user->getUsername(),
            'email'             => $user->getEmail(),
            'password'          => $user->getPassword(),
            'role'              => $user->getRole(),
            'birthdate'         => $user->getBirthdate(),
            'avatar'            => $user->getAvatar(),
        ]);
    }

    public function update(User $user): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                username = :username,
                email = :email,
                password = :password,
                birthdate = :birthdate,
                avatar = :avatar,
                role = :role
            WHERE id = :id
        ");

        return $stmt->execute([
            'username'    => $user->getUsername(),
            'email'     => $user->getEmail(),
            'password'  => $user->getPassword(),
            'birthdate' => $user->getBirthdate(),
            'avatar'    => $user->getAvatar(),
            'role'      => $user->getRole(),
            'id'        => $user->getId(),
        ]);
    }

    private function mapToUser(array $data): User
    {
        $user = new User(
            $data['username'],
            $data['email'],
            $data['password'],
            $data['role'],
            (bool) ($data['is_verified'] ?? true),
            (int) $data['id'] ?? null
        );

        $user->setAvatar($data['avatar'] ?? null);
        $user->setBirthdate($data['birthdate'] ?? null);
        $user->setDateInscription($data['date_inscription'] ?? null);

        return $user;
    }


    /*
    // Retourne tous les utilisateurs (triés par nom)
    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY username ASC');
        $users = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = $this->mapToUser($row);
        }

        return $users;
    }
    */

    // Retourne tous les utilisateurs (triés par id)
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    // Retourne le nombre total d’utilisateurs
    public function getTotalUsers(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return (int) $stmt->fetchColumn();
    }

// Retourne un tableau associatif [role => count]
    public function getUsersByRole(): array {
        $stmt = $this->db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $roles = [];
        foreach ($results as $row) {
            $roles[$row['role']] = (int) $row['count'];
        }

        return $roles;
    }

// Retourne les derniers utilisateurs inscrits
    public function getLatestUsers(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY date_inscription DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapToUser($row);
        }

        return $users;
    }

    // Mots de passe oublié
    public function saveResetToken(int $userId, string $token, string $expiresAt): bool
    {
        $sql = "UPDATE users
            SET reset_token = :token,
                reset_token_expires_at = :expiresAt
            WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'token'     => $token,
            'expiresAt' => $expiresAt,
            'id'        => $userId
        ]);
    }

    /*
    // Test ajour bdd
    public function saveResetToken(int $userId, string $token, string $expiresAt): bool
    {
        $sql = "UPDATE users 
            SET reset_token = :token,
                reset_token_expires_at = :expiresAt
            WHERE id = :id";

        // 🔍 Vérifie que les variables ont bien les bonnes valeurs
        var_dump("➡️ Tentative d'enregistrement du token");
        var_dump("User ID: ", $userId);
        var_dump("Token: ", $token);
        var_dump("ExpiresAt: ", $expiresAt);

        $stmt = $this->db->prepare($sql);

        // 🔍 Test de la requête préparée
        if (!$stmt) {
            var_dump("❌ Erreur prepare(): ", $this->db->errorInfo());
            return false;
        }

        $result = $stmt->execute([
            'token'     => $token,
            'expiresAt' => $expiresAt,
            'id'        => $userId
        ]);

        // 🔍 Test du résultat de l'exécution
        if (!$result) {
            var_dump("❌ Erreur execute(): ", $stmt->errorInfo());
        } else {
            var_dump("✅ Token enregistré avec succès");
        }

        return $result;
    }
    */


    public function findByResetToken(string $token): ?User
    {
        $stmt = $this->db->prepare("
        SELECT * FROM users 
        WHERE reset_token = :token AND reset_token_expires_at > NOW()
    ");
        $stmt->execute(['token' => $token]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // 🕓 Vérifie expiration en PHP
            if (!empty($data['reset_token_expires_at']) && $data['reset_token_expires_at'] > date('Y-m-d H:i:s')) {
                return $this->hydrateUser($data);
            }
        }

        return null;
    }


    /*
    // Debug findByResetToken()
    public function findByResetToken(string $token): ?User
    {
        // Debug
        echo "<pre>";
        echo "Recherche du token reçu : $token\n";

        $stmt = $this->db->prepare("
        SELECT * FROM users 
        WHERE reset_token = :token AND reset_token_expires_at > NOW()
    ");
        $stmt->execute(['token' => $token]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        var_dump($data); // 👈 Montre ce qu'on récupère

        if ($data) {
            return $this->hydrateUser($data); // ← on la complète juste en dessous
        }

        echo "❌ Aucun utilisateur trouvé pour ce token.";
        return null;
    }
*/


    public function updatePasswordAndClearToken(User $user): bool
    {
        $query = $this->db->prepare("
        UPDATE users 
        SET password = :password,
            reset_token = NULL,
            reset_token_expires_at = NULL
        WHERE id = :id
    ");

        return $query->execute([
            'password' => $user->getPassword(),
            'id'       => $user->getId()
        ]);
    }


    public function setResetToken(int $userId, string $token, string $expiresAt): void {
        $stmt = $this->db->prepare("
        UPDATE users
        SET reset_token = :token, reset_token_expires_at = :expires
        WHERE id = :id
    ");
        $stmt->execute([
            'token'   => $token,
            'expires' => $expiresAt,
            'id'      => $userId
        ]);
    }
}
