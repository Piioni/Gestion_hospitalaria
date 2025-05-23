<?php

namespace model\repository;

use model\Database;
use model\entity\Almacen;
use PDO;

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
        $stmt = $this->pdo->prepare("
            INSERT INTO almacenes (nombre, tipo, id_hospital, id_planta) 
            VALUES (? , ?, ?, ?)");
        return $stmt->execute([$nombre, $tipo, $id_hospital, $id_planta]);
    }

    public function update($id_almacen, $tipo, $id_hospital, $id_planta): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE almacenes 
            SET tipo = ?,  id_hospital = ? , id_planta = ?
            WHERE id_almacen = ?"
        );
        return $stmt->execute([$tipo, $id_hospital, $id_planta, $id_almacen ]);
    }

    public function delete($id_almacen): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM almacenes 
            WHERE id_almacen = ?"
        );
        return $stmt->execute([$id_almacen]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM almacenes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_almacen)
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM almacenes
            WHERE id_almacen = ?"
        );
        $stmt->execute([$id_almacen]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByPlantaId($id_planta) : ?Almacen
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM almacenes
            WHERE id_planta = ?"
        );
        $stmt->execute([$id_planta]);
        $almacenData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($almacenData)) {
            return null;
        }
        return $this->createAlmacenFromData($almacenData[0]);
    }

    public function getByHospitalId($id_hospital) : ?Almacen
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM almacenes
            WHERE id_hospital = ?"
        );
        $stmt->execute([$id_hospital]);
        $almacenData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($almacenData)) {
            return null;
        }
        return $this->createAlmacenFromData($almacenData[0]);
    }


}