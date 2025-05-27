<?php

namespace model\repository;

use model\Database;
use model\entity\Almacen;
use PDO;
use PDOException;

class AlmacenRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createAlmacenFromData(array $data): Almacen
    {
        return new Almacen(
            $data['id_almacen'],
            $data['nombre'],
            $data['tipo'],
            $data['id_hospital'],
            $data['id_planta']
        );
    }

    public function create($nombre, $tipo, $id_hospital, $id_planta = null): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO almacenes (nombre, tipo, id_hospital, id_planta) 
                VALUES (? , ?, ?, ?)");
            return $stmt->execute([$nombre, $tipo, $id_hospital, $id_planta]);
        } catch (PDOException $e) {
            error_log("Error al crear almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id_almacen, $nombre, $tipo, $id_hospital, $id_planta): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE almacenes 
                SET nombre = ?, tipo = ?,  id_hospital = ? , id_planta = ?
                WHERE id_almacen = ?"
            );
            return $stmt->execute([$nombre, $tipo, $id_hospital, $id_planta, $id_almacen]);
        } catch (PDOException $e) {
            error_log("Error al actualizar almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id_almacen): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM almacenes 
                WHERE id_almacen = ?"
            );
            return $stmt->execute([$id_almacen]);
        } catch (PDOException $e) {
            error_log("Error al eliminar almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM almacenes");
            $stmt->execute();
            $almacenesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createAlmacenFromData'], $almacenesData);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los almacenes: " . $e->getMessage());
            throw $e;
        }
    }

    public function getById($id_almacen) : ?Almacen
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM almacenes
                WHERE id_almacen = ?"
            );
            $stmt->execute([$id_almacen]);
            $almacenData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $almacenData ? $this->createAlmacenFromData($almacenData) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener almacén por ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByPlantaId($id_planta) : ?Almacen
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM almacenes
                WHERE id_planta = ?"
            );
            $stmt->execute([$id_planta]);
            $almacenData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $almacenData ? $this->createAlmacenFromData($almacenData) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener almacén por ID de planta: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByHospitalId($id_hospital) : ?Almacen
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM almacenes
                WHERE id_hospital = ? AND tipo = 'GENERAL'"
            );
            $stmt->execute([$id_hospital]);
            $almacenData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $almacenData ? $this->createAlmacenFromData($almacenData) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener almacén por ID de hospital: " . $e->getMessage());
            throw $e;
        }
    }
}
