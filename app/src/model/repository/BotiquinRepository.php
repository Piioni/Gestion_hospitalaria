<?php

namespace model\repository;

use model\Database;
use model\entity\Botiquin;
use PDO;
use PDOException;

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
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO botiquines (id_planta, nombre, capacidad) 
                VALUES (?, ?, ?)
                ");
            return $stmt->execute([$id_planta, $nombre, $capacidad]);
        } catch (PDOException $e) {
            error_log("Error al crear botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id_botiquin, $id_planta, $nombre, $capacidad): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE botiquines 
                SET id_planta = ?, nombre = ?, capacidad = ? 
                WHERE id_botiquin = ?
                ");
            return $stmt->execute([$id_planta, $nombre, $capacidad, $id_botiquin]);
        } catch (PDOException $e) {
            error_log("Error al actualizar botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id_botiquin): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM botiquines 
                WHERE id_botiquin = ?
                ");
            return $stmt->execute([$id_botiquin]);
        } catch (PDOException $e) {
            error_log("Error al eliminar botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM botiquines");
            $botiquinesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createBotiquinFromData'], $botiquinesData);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los botiquines: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByPlantaId($id_planta): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM botiquines 
                WHERE id_planta = ?
                ");
            $stmt->execute([$id_planta]);
            $botiquinesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createBotiquinFromData'], $botiquinesData);
        } catch (PDOException $e) {
            error_log("Error al obtener botiquines por ID de planta: " . $e->getMessage());
            throw $e;
        }
    }

    public function getBotiquinById($id): ?Botiquin
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM botiquines 
                WHERE id_botiquin = ?"
            );
            $stmt->execute([$id]);
            $botiquinData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $botiquinData ? $this->createBotiquinFromData($botiquinData) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener botiquín por ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function countProductos($id_botiquin): int
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(cantidad) 
                FROM stocks 
                WHERE id_ubicacion = ? AND tipo_ubicacion = 'BOTIQUIN'
                ");
            $stmt->execute([$id_botiquin]);
            $count = $stmt->fetchColumn();
            return $count ? (int)$count : 0;
        } catch (PDOException $e) {
            error_log("Error al contar productos en botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllArray(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT id_botiquin, id_planta, nombre FROM botiquines");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los botiquines como array: " . $e->getMessage());
            throw $e;
        }
    }
}
