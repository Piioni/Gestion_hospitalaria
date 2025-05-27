<?php

namespace model\repository;

use model\Database;
use model\entity\Stock;
use PDO;
use PDOException;

class StockRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createStockFromData(array $data): Stock
    {
        return new Stock(
            (int)$data['id_stock'],
            (int)$data['id_producto'],
            $data['tipo_ubicacion'],
            (int)$data['id_ubicacion'],
            (int)$data['cantidad'],
            (int)$data['cantidad_minima'],
            $data['fecha_actualizacion'] ?? date('Y-m-d H:i:s')
        );
    }

    /**
     * Obtener stock por ID de botiquín
     * @param int $idBotiquin
     * @return array
     */
    public function getStocksByBotiquinId(int $idBotiquin): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stocks 
                WHERE tipo_ubicacion = 'BOTIQUIN' AND id_ubicacion = ?
                ORDER BY cantidad < cantidad_minima DESC, id_producto ASC
            ");
            $stmt->execute([$idBotiquin]);
            $stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createStockFromData'], $stockData);
        } catch (PDOException $e) {
            error_log("Error al obtener stock por botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener stock por ID de almacén
     * @param int $idAlmacen
     * @return array
     */
    public function getStockByAlmacenId(int $idAlmacen): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stocks 
                WHERE tipo_ubicacion = 'ALMACEN' AND id_ubicacion = ?
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

    public function getStockById(int $idStock): ?Stock
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stocks 
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
                UPDATE stocks 
                SET cantidad = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id_stock = ?
            ");
            return $stmt->execute([$nuevaCantidad, $idStock]);
        } catch (PDOException $e) {
            error_log("Error al actualizar cantidad de stock: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Añadir producto al stock de un botiquín
     */
    public function addProductToStockBotiquin(int $idBotiquin, int $idProducto, int $cantidad, int $cantidadMinima): bool
    {
        return $this->addProductToStock('BOTIQUIN', $idBotiquin, $idProducto, $cantidad, $cantidadMinima);
    }

    /**
     * Añadir producto al stock de un almacén
     */
    public function addProductToStockAlmacen(int $idAlmacen, int $idProducto, int $cantidad, int $cantidadMinima): bool
    {
        return $this->addProductToStock('ALMACEN', $idAlmacen, $idProducto, $cantidad, $cantidadMinima);
    }

    /**
     * Method interno para añadir producto al stock según tipo de ubicación
     */
    private function addProductToStock(string $tipoUbicacion, int $idUbicacion, int $idProducto, int $cantidad, int $cantidadMinima): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO stocks (id_producto, tipo_ubicacion, id_ubicacion, cantidad, cantidad_minima, fecha_actualizacion) 
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            return $stmt->execute([$idProducto, $tipoUbicacion, $idUbicacion, $cantidad, $cantidadMinima]);
        } catch (PDOException $e) {
            error_log("Error al añadir producto al stock: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener productos con stock bajo para un tipo de ubicación específico
     */
    public function getProductosStockBajo(string $tipoUbicacion = null): array
    {
        try {
            $sql = "
                SELECT s.*, p.nombre as nombre_producto 
                FROM stocks s
                JOIN productos p ON s.id_producto = p.id_producto
                WHERE s.cantidad < s.cantidad_minima
            ";

            $params = [];

            // Si se especifica un tipo de ubicación, filtramos por él
            if ($tipoUbicacion) {
                $sql .= " AND s.tipo_ubicacion = ?";
                $params[] = $tipoUbicacion;
            }

            $sql .= " ORDER BY s.cantidad ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos con stock bajo: " . $e->getMessage());
            throw $e;
        }
    }

}