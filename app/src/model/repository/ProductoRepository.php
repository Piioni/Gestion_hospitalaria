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
    
    public function delete($id_producto): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
            return $stmt->execute([$id_producto]);
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
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

    /**
     * Méto do optimizado para filtrar productos con múltiples criterios
     * 
     * @param array $filtros Criterios de filtrado (codigo, almacen, botiquin)
     * @return array Lista de productos filtrados
     */
    public function filtrarProductos(array $filtros = []): array
    {
        try {
            $whereConditions = [];
            $params = [];

            // Código del producto
            if (!empty($filtros['codigo'])) {
                $whereConditions[] = "codigo LIKE CONCAT('%', ?, '%')";
                $params[] = $filtros['codigo'];
            }
            // Nombre del producto
            if (!empty($filtros['nombre'])) {
                $whereConditions[] = "nombre LIKE CONCAT('%', ?, '%')";
                $params[] = $filtros['nombre'];
            }

            // Construir consulta SQL
            $sql = "SELECT DISTINCT * FROM productos p";
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }

            $sql .= " ORDER BY nombre ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map([$this, 'createProductoFromData'], $productos);
        } catch (PDOException $e) {
            error_log("Error al filtrar productos: " . $e->getMessage());
            throw $e;
        }
    }
}
