<?php
namespace App\Controllers;

use App\Models\User;

class UserController {
    public function login(string $username, string $password): bool {
        $user = new User($username, $password, 'member');
        return $user->verifyPassword($password);
    }
}
