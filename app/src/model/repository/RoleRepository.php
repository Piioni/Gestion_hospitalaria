<?php

namespace model\repository;

use model\Database;
use PDO;

class RoleRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM roles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRoleById($id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id_rol = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
