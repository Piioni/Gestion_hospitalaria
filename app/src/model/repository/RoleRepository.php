<?php

namespace model\repository;

use model\Database;
use model\entity\Rol;
use PDO;

class RoleRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createRoleFromData($roleData) : Rol
    {
        return new Rol($roleData['id_rol'], $roleData['nombre']);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM roles");
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $roleObjects = [];
        foreach ($roles as $roleData) {
            $roleObjects[] = $this->createRoleFromData($roleData);
        }
        return $roleObjects;
    }

    public function getRoleById($id): ?Rol
    {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id_rol = ?");
        $stmt->execute([$id]);
        $roleData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($roleData) {
            return $this->createRoleFromData($roleData);
        }
        return null;
    }
}
