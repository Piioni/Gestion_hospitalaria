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
                INSERT INTO productos (codigo, nombre, descripcion, unidad_medida)
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

    public function getByCodigo(string $codigo): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM productos
                WHERE codigo LIKE CONCAT('%', ?, '%')
                ");
            $stmt->execute([$codigo]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createProductoFromData'], $productos);

        } catch (PDOException $e) {
            error_log("Error al obtener producto por código: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByCodigoAndAlmacen(string $codigo, $almacen) : array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.* 
                FROM productos p
                JOIN stocks s ON p.id_producto = s.id_producto
                WHERE p.codigo LIKE CONCAT('%', ?, '%') AND s.id_ubicacion = ? AND tipo_ubicacion = 'ALMACEN'
            ");
            $stmt->execute([$codigo, $almacen]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createProductoFromData'], $productos);

        } catch (PDOException $e) {
            error_log("Error al obtener productos por código y almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByCodigoAndBotiquin(string $codigo, $botiquin): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.* 
                FROM productos p
                JOIN stocks s ON p.id_producto = s.id_producto
                WHERE p.codigo LIKE CONCAT('%', ?, '%') AND s.id_ubicacion = ? AND tipo_ubicacion = 'BOTIQUIN'
            ");
            $stmt->execute([$codigo, $botiquin]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createProductoFromData'], $productos);

        } catch (PDOException $e) {
            error_log("Error al obtener productos por código y botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByBotiquin(string $botiquin): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.* 
                FROM productos p
                JOIN stocks s ON p.id_producto = s.id_producto
                WHERE s.id_ubicacion = ? AND tipo_ubicacion = 'BOTIQUIN'
            ");
            $stmt->execute([$botiquin]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createProductoFromData'], $productos);

        } catch (PDOException $e) {
            error_log("Error al obtener productos por botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByAlmacen(string $almacen): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.* 
                FROM productos p
                JOIN stocks s ON p.id_producto = s.id_producto
                WHERE s.id_ubicacion = ? AND tipo_ubicacion = 'ALMACEN'
            ");
            $stmt->execute([$almacen]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createProductoFromData'], $productos);

        } catch (PDOException $e) {
            error_log("Error al obtener productos por almacén: " . $e->getMessage());
            throw $e;
        }
    }
}
