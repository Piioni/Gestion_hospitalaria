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

    public function create($id_planta, $nombre): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO botiquines (id_planta, nombre) 
            VALUES (?, ?)"
        );
        return $stmt->execute([$id_planta, $nombre]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM botiquines");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPlantaId($plantaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM botiquines 
            WHERE id_planta = ?"
        );
        $stmt->execute([$plantaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBotiquinById($id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM botiquines 
            WHERE id_botiquin = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}