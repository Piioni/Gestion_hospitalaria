<?php

namespace model\repository;

use model\Database;
use model\entity\Producto;
use PDO;
use PDOException;

class ProductoRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createProductoFromData($data): Producto
    {
        return new Producto(
            $data['id_producto'],
            $data['codigo'],
            $data['nombre'],
            $data['descripcion'],
            $data['unidad_medida']
        );
    }

    public function create($codigo, $nombre, $descripcion, $unidad_medida = null): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO producto (codigo, nombre, descripcion, unidad_medida)
                VALUES (?, ?, ?, ?)
                ");
            return $stmt->execute([$codigo, $nombre, $descripcion, $unidad_medida]);
        } catch (PDOException $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida = null): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE productos 
                SET codigo = ?, nombre = ?, descripcion = ?, unidad_medida = ? 
                WHERE id_producto = ?
                ");
            return $stmt->execute([$codigo, $nombre, $descripcion, $unidad_medida, $id_producto]);
        } catch (PDOException $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $query = "SELECT * FROM productos";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map([$this, 'createProductoFromData'], $productos);
        } catch (PDOException $e) {
            error_log("Error al obtener productos filtrados: " . $e->getMessage());
            throw $e;
        }
    }

    public function getById($id_producto): ?Producto
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? $this->createProductoFromData($data) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener producto por ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByCodigo($codigo): ?Producto
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE codigo = ?");
            $stmt->execute([$codigo]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? $this->createProductoFromData($data) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener producto por código: " . $e->getMessage());
            throw $e;
        }
    }
}
