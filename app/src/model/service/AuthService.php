<?php

namespace model\service;

use http\Exception\InvalidArgumentException;
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

    public function register($nombre, $email, $password, $rol): bool
    {
        if (empty($nombre) || empty($email) || empty($password)) {
            throw new InvalidArgumentException("Todos los campos son obligatorios."); // All fields are required
        }

        if ($this->userService->getUserByEmail($email)) {
            throw new InvalidArgumentException("Ya existe un usuario con ese email."); // User already exists
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El email no es válido."); // Invalid email
        }

        if (strlen($password) < 6) {
            throw new InvalidArgumentException("La contraseña debe tener al menos 6 caracteres."); // Password must be at least 6 characters
        }

        // Hash the password before saving
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return $this->userService->addUser($nombre, $email, $passwordHash, $rol);
    }

    #[NoReturn]
    public function logout(): void
    {
        session_destroy();
        header("Location: /");
        exit();
    }

    public function getUserIdByEmail($email): ?int
    {
        $user = $this->userService->getUserByEmail($email);
        return $user?->getId();
    }

    public function isAuthenticated() : bool
    {
        return isset($_SESSION['user']);
    }
}
