<?php

namespace model\repository;

use model\Database;
use model\entity\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createUserFromData($userData): User
    {
        $user = new User();
        $user->setId($userData['id_usuario']);
        $user->setNombre($userData['nombre']);
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password']);
        $user->setRol($userData['id_rol']);

        // Configurar campos opcionales si existen
        if (isset($userData['id_hospital'])) {
            $user->setHospitalId($userData['id_hospital']);
        }
        if (isset($userData['id_planta'])) {
            $user->setPlantaId($userData['id_planta']);
        }
        if (isset($userData['id_botiquin'])) {
            $user->setBotiquinId($userData['id_botiquin']);
        }

        return $user;
    }

    public function insertUser($nombre, $email, $password, $id_rol): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (nombre, email, password, id_rol, activo) 
            VALUES (?, ?, ?, ?, 1)");


        return $stmt->execute([$nombre, $email, $password, $id_rol]);
    }

    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($usersData as $userData) {
            $users[] = $this->createUserFromData($userData);
        }
        return $users;
    }

    public function getUserById($id): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM users 
            WHERE id_usuario = ?"
        );
        $stmt->execute([$id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? $this->createUserFromData($userData) : null;
    }

    public function getUserByEmail($email): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM users 
            WHERE email = ?"
        );
        $stmt->execute([$email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? $this->createUserFromData($userData) : null;
    }
}
