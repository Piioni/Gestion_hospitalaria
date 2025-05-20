<?php

namespace model\repository;
use model\Database;
use PDO;
class PlantaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM plantas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByHospitalId($hospitalId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM plantas WHERE hospital_id = ?");
        $stmt->execute([$hospitalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlantaById($id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM plantas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}