<?php

namespace model\repository;

use model\Database;
use model\entity\Planta;
use PDO;
use PDOException;

class PlantaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createPlantaFromData(array $data): Planta
    {
        return new Planta(
            (int)$data['id_planta'],
            (int)$data['id_hospital'],
            $data['nombre'],
        );
    }

    public function create($id_hospital, $nombre): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO plantas (id_hospital, nombre, activo) 
                VALUES (?, ?, 1)
                ");
            return $stmt->execute([$id_hospital, $nombre]);
        } catch (PDOException $e) {
            error_log("Error al crear planta: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id_planta, $id_hospital, $nombre): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE plantas 
                SET id_hospital = ?, nombre = ? 
                WHERE id_planta = ? AND activo = 1
                ");
            return $stmt->execute([$id_hospital, $nombre, $id_planta]);
        } catch (PDOException $e) {
            error_log("Error al actualizar planta: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id_planta): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE plantas 
                SET activo = 0 
                WHERE id_planta = ?
                ");
            return $stmt->execute([$id_planta]);
        } catch (PDOException $e) {
            error_log("Error al eliminar planta: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT * 
                FROM plantas
                WHERE activo = 1
                ORDER BY nombre"
            );
            $plantasData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createPlantaFromData'], $plantasData);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las plantas: " . $e->getMessage());
            throw $e;
        }
    }

    public function getById($id): ?Planta
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM plantas 
                WHERE id_planta = ? AND activo = 1
                ");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->createPlantaFromData($row) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener planta por ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByHospitalId($hospitalId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM plantas 
                WHERE id_hospital = ? AND activo = 1
                ORDER BY nombre
                ");
            $stmt->execute([$hospitalId]);
            $plantasData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createPlantaFromData'], $plantasData);
        } catch (PDOException $e) {
            error_log("Error al obtener plantas por hospital: " . $e->getMessage());
            throw $e;
        }
    }
}
