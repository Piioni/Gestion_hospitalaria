<?php

namespace model\repository;

use model\Database;
use PDO;

class AlmacenRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function create($tipo, $id_hospital, $id_planta = null): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO almacenes (tipo, id_hospital, id_planta) 
            VALUES (? , ?, ?)");
        return $stmt->execute([$tipo, $id_hospital, $id_planta]);
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

    public function getByPlantaId($id_planta): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM almacenes
            WHERE id_planta = ?"
        );
        $stmt->execute([$id_planta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}