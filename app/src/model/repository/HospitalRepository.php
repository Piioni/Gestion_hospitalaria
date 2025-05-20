<?php

namespace model\repository;

use model\Database;
use PDO;

class HospitalRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function create($name, $address, $phone, $email): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO hospitals (name, address, phone, email) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $address, $phone, $email]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM hospitals");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getHospitalById($id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM hospitals WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}
