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

    public function insertUser($name, $email, $password, $rol, $hospitalId = null, $plantaId = null, $botiquinId = null): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, rol, id_hospital, id_planta, id_botiquin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $passwordHash, $rol, $hospitalId, $plantaId, $botiquinId]);
    }

    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_usuario = ?");
        $stmt->execute([$id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? $this->createUserFromData($userData) : null;
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? $this->createUserFromData($userData) : null;
    }

    public function createUserFromData($userData): User
    {
        $user = new User();
        $user->setId($userData['id']);
        $user->setNombre($userData['name']);
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password']);
        $user->setRol($userData['rol']);

        // Configurar campos opcionales si existen
        if (isset($userData['hospital_id'])) {
            $user->setHospitalId($userData['hospital_id']);
        }
        if (isset($userData['planta_id'])) {
            $user->setPlantaId($userData['planta_id']);
        }
        if (isset($userData['botiquin_id'])) {
            $user->setBotiquinId($userData['botiquin_id']);
        }

        return $user;
    }
}
