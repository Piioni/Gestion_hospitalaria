<?php

namespace model\repository;

use model\Database;
use model\entity\StockAlmacen;
use PDO;
use PDOException;

class StockAlmacenRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createStockFromData(array $data): StockAlmacen
    {
        return new StockAlmacen(
            (int)$data['id_stock'],
            (int)$data['id_producto'],
            (int)$data['id_almacen'],
            (int)$data['cantidad'],
            (int)$data['cantidad_minima'],
        );
    }

    public function getStockByAlmacenId(int $idAlmacen): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stock_almacenes 
                WHERE id_almacen = ?
                ORDER BY cantidad < cantidad_minima DESC, id_producto ASC
            ");
            $stmt->execute([$idAlmacen]);
            $stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createStockFromData'], $stockData);
        } catch (PDOException $e) {
            error_log("Error al obtener stock por almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function getStockById(int $idStock): ?StockAlmacen
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stock_almacenes 
                WHERE id_stock = ?
            ");
            $stmt->execute([$idStock]);
            $stockData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $stockData ? $this->createStockFromData($stockData) : null;
        } catch (PDOException $e) {
            error_log("Error al obtener stock por ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateStockCantidad(int $idStock, int $nuevaCantidad): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE stock_almacenes 
                SET cantidad = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id_stock = ?
            ");
            return $stmt->execute([$nuevaCantidad, $idStock]);
        } catch (PDOException $e) {
            error_log("Error al actualizar cantidad de stock: " . $e->getMessage());
            throw $e;
        }
    }

    public function addProductToStockAlmacen(int $idAlmacen, int $idProducto, int $cantidad, int $cantidadMinima): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO stock_almacenes (id_producto, id_almacen, cantidad, cantidad_minima, fecha_actualizacion) 
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            return $stmt->execute([$idProducto, $idAlmacen, $cantidad, $cantidadMinima]);
        } catch (PDOException $e) {
            error_log("Error al añadir producto al stock de almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function getProductosStockBajo(): array
    {
        try {
            $sql = "
                SELECT s.*, p.nombre as nombre_producto 
                FROM stock_almacenes s
                JOIN productos p ON s.id_producto = p.id_producto
                WHERE s.cantidad < s.cantidad_minima
                ORDER BY s.cantidad ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos con stock bajo en almacén: " . $e->getMessage());
            throw $e;
        }
    }
}
