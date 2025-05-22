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

    public function create($tipo, $id_planta, $id_hospital): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO almacen (tipo, id_planta, id_hospital) 
            VALUES (? , ?, ?)");
        return $stmt->execute([$tipo, $id_planta, $id_hospital]);
    }

    public function update($id_almacen, $tipo, $id_planta, $id_hospital): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE almacen 
            SET tipo = ?, id_planta = ?, id_hospital = ? 
            WHERE id_almacen = ?"
        );
        return $stmt->execute([$tipo, $id_planta, $id_hospital, $id_almacen]);
    }

    public function delete($id_almacen): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM almacen 
            WHERE id_almacen = ?"
        );
        return $stmt->execute([$id_almacen]);
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM almacen");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_almacen)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM almacen WHERE id_almacen = ?");
        $stmt->execute([$id_almacen]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}