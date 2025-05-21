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
                INSERT INTO plantas (id_hospital, nombre) 
                VALUES (?, ?)"
            );
            return $stmt->execute([$id_hospital, $nombre]);
        } catch (PDOException $e) {
            error_log("Error al crear planta: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT * 
                FROM plantas
                ORDER BY nombre"
            );
            
            $plantasObjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $plantasObjects[] = $this->createPlantaFromData($row);
            }
            return $plantasObjects;
        } catch (PDOException $e) {
            error_log("Error al obtener todas las plantas: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllArray(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT * 
                FROM plantas
                ORDER BY nombre"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las plantas: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByHospitalId($hospitalId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM plantas 
                WHERE id_hospital = ?
                ORDER BY nombre"
            );
            $stmt->execute([$hospitalId]);
            
            $plantasObjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $plantasObjects[] = $this->createPlantaFromData($row);
            }
            return $plantasObjects;
        } catch (PDOException $e) {
            error_log("Error al obtener plantas por hospital: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPlantaById($id): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM plantas 
                WHERE id_planta = ?"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener planta por ID: " . $e->getMessage());
            throw $e;
        }
    }
}
