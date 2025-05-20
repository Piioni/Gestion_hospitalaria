<?php

namespace model\service;

use JetBrains\PhpStorm\NoReturn;

class AuthService
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function login($email, $password): bool
    {
        $user = $this->userService->getUserByEmail($email);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;

                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $this->userModel->updatePassword($user['id'], $newHash);
                }

                return true;
            }
        }
        return false;
    }

    public function register($name, $email, $password, $rol, $hospitalId = null, $plantaId = null, $botiquinId = null): bool
    {
        return $this->userService->addUser($name, $email, $password, $rol, $hospitalId, $plantaId, $botiquinId);
    }

    #[NoReturn]
    public function logout(): void
    {
        session_destroy();
        header("Location: /");
        exit();
    }

    public function isAuthenticated() : bool
    {
        return isset($_SESSION['user']);
    }
}
