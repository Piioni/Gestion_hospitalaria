<?php

namespace model\repository;

use model\Database;
use model\entity\Almacen;
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
                INSERT INTO hospitales (nombre, ubicacion, activo) 
                VALUES (?, ?, 1)
                ");
            return $stmt->execute([$nombre, $ubicacion]);
        } catch (PDOException $e) {
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
                WHERE id_hospital = ? AND activo = 1
                ");
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
                UPDATE hospitales
                SET activo = 0
                WHERE id_hospital = ?
                ");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar hospital: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM hospitales 
                WHERE activo = 1
                ");
            $stmt->execute();
            $hospitalesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createHospitalFromData'], $hospitalesData);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los hospitales: " . $e->getMessage());
            throw $e;
        }
    }

    public function getById($id): ?Hospital
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM hospitales 
                WHERE id_hospital = ? AND activo = 1
                ");
            $stmt->execute([$id]);
            $hospitalData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $hospitalData ? $this->createHospitalFromData($hospitalData) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener hospital por ID: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene los hospitales asociados a un usuario específico
     * @param int $userId
     * @return array Lista de objetos Hospital
     */
    public function getHospitalsByUserId(int $userId): array
    {

        $sql = "SELECT h.* FROM hospitales h 
                INNER JOIN user_hospital uh ON h.id_hospital = uh.id_hospital 
                WHERE uh.id_usuario = ? AND h.activo = 1
                ORDER BY h.nombre";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $hospitalsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'createHospitalFromData'], $hospitalsData);
    }

    public function existsByName($nombre): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM hospitales 
                WHERE nombre = ? AND activo = 1
                ");
            $stmt->execute([$nombre]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia por nombre: " . $e->getMessage());
            throw $e;
        }
    }

    public function existsByAddress($ubicacion): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM hospitales 
                WHERE ubicacion = ? AND activo = 1
                ");
            $stmt->execute([$ubicacion]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia por dirección: " . $e->getMessage());
            throw $e;
        }
    }

    public function existsByNameExceptId($nombre, $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM hospitales 
                WHERE activo = 1 AND nombre = ? AND id_hospital != ? 
                ");
            $stmt->execute([$nombre, $id]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia por nombre exceptuando ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function existsByAddressExceptId($ubicacion, $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM hospitales 
                WHERE activo = 1 AND ubicacion = ? AND id_hospital != ? 
                ");
            $stmt->execute([$ubicacion, $id]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar existencia por dirección exceptuando ID: " . $e->getMessage());
            throw $e;
        }
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
                WHERE id_hospital = ? AND activo = 1
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
                WHERE id_hospital = ? AND activo = 1
            ");
            $stmt->execute([$id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar plantas relacionadas: " . $e->getMessage());
            throw $e;
        }
    }

}
