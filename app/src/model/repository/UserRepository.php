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

    public function getAllBotiquinUsers(): array
    {
        try{
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT id_usuario
                FROM lecturas
                ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $users = [];
            foreach ($result as $userData) {
                $user = $this->getUserById($userData['id_usuario']);
                if ($user) {
                    $users[] = [
                        'id_usuario' => $user->getId(),
                        'nombre' => $user->getNombre()
                    ];
                }
            }
            // Reindexar el array
            return array_values($users);
        } catch (\PDOException $e) {
            error_log("Error al obtener usuarios de botiquín: " . $e->getMessage());
            throw $e;
        }
    }
}
