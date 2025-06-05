<?php

namespace model\repository;

use model\Database;
use model\entity\StockBotiquin;
use PDO;
use PDOException;

class StockBotiquinRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createStockFromData(array $data): StockBotiquin
    {
        return new StockBotiquin(
            (int)$data['id_stock'],
            (int)$data['id_producto'],
            (int)$data['id_botiquin'],
            (int)$data['cantidad'],
            (int)$data['cantidad_minima'],
        );
    }
    
    public function getStocksByBotiquinId(int $idBotiquin): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stock_botiquines 
                WHERE id_botiquin = ?
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

    public function getStockById(int $idStock): ?StockBotiquin
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM stock_botiquines 
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
                UPDATE stock_botiquines 
                SET cantidad = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id_stock = ?
            ");
            return $stmt->execute([$nuevaCantidad, $idStock]);
        } catch (PDOException $e) {
            error_log("Error al actualizar cantidad de stock: " . $e->getMessage());
            throw $e;
        }
    }

    public function addProductToStockBotiquin(int $idBotiquin, int $idProducto, int $cantidad, int $cantidadMinima): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO stock_botiquines (id_producto, id_botiquin, cantidad, cantidad_minima, fecha_actualizacion) 
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            return $stmt->execute([$idProducto, $idBotiquin, $cantidad, $cantidadMinima]);
        } catch (PDOException $e) {
            error_log("Error al añadir producto al stock de botiquín: " . $e->getMessage());
            throw $e;
        }
    }

    public function getProductosStockBajo(): array
    {
        try {
            $sql = "
                SELECT s.*, p.nombre as nombre_producto 
                FROM stock_botiquines s
                JOIN productos p ON s.id_producto = p.id_producto
                WHERE s.cantidad < s.cantidad_minima
                ORDER BY s.cantidad ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos con stock bajo en botiquín: " . $e->getMessage());
            throw $e;
        }
    }
}
