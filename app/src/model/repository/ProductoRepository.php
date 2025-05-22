<?php

namespace model\repository;

use model\Database;
use PDO;

class ProductoRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function create($codigo, $nombre, $descripcion, $unidad_medida = null ) :bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO producto (codigo, nombre, descripcion, unidad_medida)
            VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$codigo, $nombre, $descripcion, $unidad_medida]);
    }

    public function update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida = null) :bool {
        $stmt = $this->pdo->prepare("
            UPDATE producto 
            SET codigo = ?, nombre = ?, descripcion = ?, unidad_medida = ? 
            WHERE id_producto = ?"
        );
        return $stmt->execute([$codigo, $nombre, $descripcion, $unidad_medida, $id_producto]);
    }

    public function delete($id_producto) :bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM producto 
            WHERE id_producto = ?"
        );
        return $stmt->execute([$id_producto]);
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM producto");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_producto) {
        $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCodigo($codigo) {
        $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE codigo = ?");
        $stmt->execute([$codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




}