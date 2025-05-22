<?php

namespace model\repository;

use model\Database;
use model\entity\Hospital;
use PDO;
use PDOException;

class HospitalRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Crea un objeto Hospital a partir de los datos obtenidos de la base de datos
     * @param array $hospitalData Datos del hospital
     * @return Hospital Objeto Hospital
     */
    public function createHospitalFromData(array $hospitalData): Hospital
    {
        return new Hospital($hospitalData['id_hospital'], $hospitalData['nombre'], $hospitalData['ubicacion']);
    }

    public function create($nombre, $ubicacion): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO hospitales (nombre, ubicacion) 
                VALUES (?, ?)"
            );
            return $stmt->execute([$nombre, $ubicacion]);
        } catch (PDOException $e) {
            // Registrar el error y relanzarlo
            error_log("Error al crear hospital: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $nombre, $ubicacion): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE hospitales 
                SET nombre = ?, ubicacion = ? 
                WHERE id_hospital = ?"
            );
            return $stmt->execute([$nombre, $ubicacion, $id]);
        } catch (PDOException $e) {
            error_log("Error al actualizar hospital: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM hospitales 
                WHERE id_hospital = ?"
            );
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar hospital: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM hospitales");
        $hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hospitalObjects = [];
        foreach ($hospitals as $hospitalData) {
            $hospitalObjects[] = $this->createHospitalFromData($hospitalData);
        }
        return $hospitalObjects;
    }

    public function getById($id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM hospitales 
            WHERE id_hospital = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function existsByName($nombre): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM hospitales 
            WHERE nombre = ?"
        );
        $stmt->execute([$nombre]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function existsByAddress($ubicacion): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM hospitales 
            WHERE ubicacion = ?"
        );
        $stmt->execute([$ubicacion]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function existsByNameExceptId($nombre, $id): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM hospitales 
            WHERE nombre = ? AND id_hospital != ?");
        $stmt->execute([$nombre, $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function existsByAddressExceptId($ubicacion, $id): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM hospitales 
            WHERE ubicacion = ? AND id_hospital != ?"
        );
        $stmt->execute([$ubicacion, $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Verifica si un hospital tiene plantas asociadas
     * @param int $id ID del hospital
     * @return array Datos de plantas asociadas
     */
    public function getRelatedPlants($id): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id_planta, nombre 
                FROM plantas 
                WHERE id_hospital = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al verificar plantas relacionadas: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Cuenta el número de plantas asociadas a un hospital
     * @param int $id ID del hospital
     * @return int Cantidad de plantas asociadas
     */
    public function countRelatedPlants($id): int
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM plantas 
                WHERE id_hospital = ?
            ");
            $stmt->execute([$id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar plantas relacionadas: " . $e->getMessage());
            throw $e;
        }
    }
}
