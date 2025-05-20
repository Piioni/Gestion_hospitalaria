<?php

namespace model\repository;

use model\Database;
use PDO;

class BotiquinRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM botiquines");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPlantaId($plantaId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM botiquines WHERE planta_id = ?");
        $stmt->execute([$plantaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBotiquinById($id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM botiquines WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}