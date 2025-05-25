<?php

namespace model\repository;

use model\Database;
use model\entity\Botiquin;
use PDO;

class BotiquinRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createBotiquinFromData(array $data): Botiquin
    {
        return new Botiquin(
            (int)$data['id_botiquin'],
            (int)$data['id_planta'],
            $data['nombre'],
            (int)$data['capacidad']
        );
    }

    public function create($id_planta, $nombre, $capacidad): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO botiquines (id_planta, nombre, capacidad) 
            VALUES (?, ?, ?)
            ");
        return $stmt->execute([$id_planta, $nombre, $capacidad]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM botiquines");
        $botiquines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $botiquinObjects = [];
        foreach ($botiquines as $botiquinData) {
            $botiquinObjects[] = $this->createBotiquinFromData($botiquinData);
        }
        return $botiquinObjects;
    }

    public function getByPlantaId($id_planta): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM botiquines 
            WHERE id_planta = ?
            ");
        $stmt->execute([$id_planta]);
        $botiquines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $botiquinObjects = [];
        foreach ($botiquines as $botiquinData) {
            $botiquinObjects[] = $this->createBotiquinFromData($botiquinData);
        }
        return $botiquinObjects;
    }

    public function getBotiquinById($id): ?Botiquin
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM botiquines 
            WHERE id_botiquin = ?"
        );
        $stmt->execute([$id]);
        $botiquinData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($botiquinData) {
            return $this->createBotiquinFromData($botiquinData);
        }
        return null;
    }
}