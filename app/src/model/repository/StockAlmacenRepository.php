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
        );
    }

    public function getStockByAlmacenId(int $idAlmacen): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stock_almacenes 
                WHERE id_almacen = ?
                ORDER BY cantidad DESC, id_producto ASC
            ");
            $stmt->execute([$idAlmacen]);
            $stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createStockFromData'], $stockData);
        } catch (PDOException $e) {
            error_log("Error al obtener stock por almacén: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCantidadTotalEnAlmacen(int $idAlmacen): int
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT SUM(cantidad) as total 
                FROM stock_almacenes 
                WHERE id_almacen = ?
            ");
            $stmt->execute([$idAlmacen]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Error al contar productos en almacén: " . $e->getMessage());
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
            // Si la cantidad es 0 o negativa, eliminar el registro
            if ($nuevaCantidad <= 0) {
                return $this->deleteStock($idStock);
            }
            
            // Actualizar la cantidad
            $stmt = $this->pdo->prepare("
                UPDATE stock_almacenes 
                SET cantidad = ?
                WHERE id_stock = ?
            ");
            return $stmt->execute([$nuevaCantidad, $idStock]);
        } catch (PDOException $e) {
            error_log("Error al actualizar cantidad de stock: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function deleteStock(int $idStock): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM stock_almacenes 
                WHERE id_stock = ?
            ");
            return $stmt->execute([$idStock]);
        } catch (PDOException $e) {
            error_log("Error al eliminar stock: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verifica si hay suficiente stock de un producto en un almacén
     * @param int $idAlmacen ID del almacén
     * @param int $idProducto ID del producto
     * @param int $cantidad Cantidad requerida
     * @return bool True si hay suficiente stock, false en caso contrario
     */
    public function verificarStockSuficiente(int $idAlmacen, int $idProducto, int $cantidad): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT cantidad 
                FROM stock_almacenes 
                WHERE id_almacen = ? AND id_producto = ?
            ");
            $stmt->execute([$idAlmacen, $idProducto]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result && $result['cantidad'] >= $cantidad;
        } catch (PDOException $e) {
            error_log("Error al verificar stock suficiente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reduce la cantidad de un producto en un almacén
     * @param int $idAlmacen ID del almacén
     * @param int $idProducto ID del producto
     * @param int $cantidad Cantidad a reducir
     * @return bool True si la operación fue exitosa
     */
    public function reducirStock(int $idAlmacen, int $idProducto, int $cantidad): bool
    {
        try {
            // Primero obtenemos la cantidad actual
            $stmt = $this->pdo->prepare("
                SELECT id_stock, cantidad 
                FROM stock_almacenes 
                WHERE id_almacen = ? AND id_producto = ?
            ");
            $stmt->execute([$idAlmacen, $idProducto]);
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stock) {
                return false; // No existe el stock
            }
            
            $nuevaCantidad = $stock['cantidad'] - $cantidad;
            return $this->updateStockCantidad($stock['id_stock'], $nuevaCantidad);
        } catch (PDOException $e) {
            error_log("Error al reducir stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Incrementa o crea stock de un producto en un almacén
     * @param int $idAlmacen ID del almacén
     * @param int $idProducto ID del producto
     * @param int $cantidad Cantidad a incrementar
     * @return bool True si la operación fue exitosa
     */
    public function incrementarStock(int $idAlmacen, int $idProducto, int $cantidad): bool
    {
        try {
            // Verificar si ya existe stock para este producto en el almacén
            $stmt = $this->pdo->prepare("
                SELECT id_stock, cantidad 
                FROM stock_almacenes 
                WHERE id_almacen = ? AND id_producto = ?
            ");
            $stmt->execute([$idAlmacen, $idProducto]);
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stock) {
                // Actualizar cantidad existente
                $nuevaCantidad = $stock['cantidad'] + $cantidad;
                return $this->updateStockCantidad($stock['id_stock'], $nuevaCantidad);
            } else {
                // Crear nuevo registro de stock
                $stmtInsert = $this->pdo->prepare("
                    INSERT INTO stock_almacenes (id_producto, id_almacen, cantidad)
                    VALUES (?, ?, ?)
                ");
                return $stmtInsert->execute([$idProducto, $idAlmacen, $cantidad]);
            }
        } catch (PDOException $e) {
            error_log("Error al incrementar stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si un almacén tiene algún producto en stock
     * @param int $idAlmacen ID del almacén
     * @return bool True si tiene al menos un producto en stock
     */
    public function hasStock(int $idAlmacen): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM stock_almacenes 
                WHERE id_almacen = ?
            ");
            $stmt->execute([$idAlmacen]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result && $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar si tiene stock: " . $e->getMessage());
            return false;
        }
    }
}