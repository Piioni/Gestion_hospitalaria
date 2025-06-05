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
                SET cantidad = ?
                WHERE id_stock = ?
            ");
            return $stmt->execute([$nuevaCantidad, $idStock]);
        } catch (PDOException $e) {
            error_log("Error al actualizar cantidad de stock: " . $e->getMessage());
            throw $e;
        }
    }

}