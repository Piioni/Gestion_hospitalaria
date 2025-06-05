<?php

namespace model\service;

use model\entity\StockAlmacen;
use model\repository\StockAlmacenRepository;

class StockAlmacenService
{
    private StockAlmacenRepository $stockRepository;
    
    public function __construct()
    {
        $this->stockRepository = new StockAlmacenRepository();
    }
    
    /**
     * Obtiene el stock de un almacén específico
     */
    public function getStockByAlmacenId(int $idAlmacen): array
    {
        return $this->stockRepository->getStockByAlmacenId($idAlmacen);
    }

    /**
     * Obtiene un stock específico por su ID
     */
    public function getStockById(int $idStock): ?StockAlmacen
    {
        return $this->stockRepository->getStockById($idStock);
    }

    /**
     * Consume una cantidad específica de un producto del stock
     */
    public function consumirStock(int $idStock, int $cantidad): bool
    {
        // Validar que la cantidad sea positiva
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException("La cantidad a consumir debe ser mayor que cero.");
        }

        // Obtener el stock actual
        $stock = $this->stockRepository->getStockById($idStock);
        if (!$stock) {
            throw new \InvalidArgumentException("No se encontró el stock con el ID especificado.");
        }

        // Validar que hay suficiente stock
        if ($stock->getCantidad() < $cantidad) {
            throw new \InvalidArgumentException("No hay suficiente stock para consumir la cantidad especificada.");
        }

        // Actualizar la cantidad
        $nuevaCantidad = $stock->getCantidad() - $cantidad;
        return $this->stockRepository->updateStockCantidad($idStock, $nuevaCantidad);
    }

    /**
     * Repone una cantidad específica de un producto al stock
     */
    public function reponerStock(int $idStock, int $cantidad): bool
    {
        // Validar que la cantidad sea positiva
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException("La cantidad a reponer debe ser mayor que cero.");
        }

        // Obtener el stock actual
        $stock = $this->stockRepository->getStockById($idStock);
        if (!$stock) {
            throw new \InvalidArgumentException("No se encontró el stock con el ID especificado.");
        }

        // Actualizar la cantidad
        $nuevaCantidad = $stock->getCantidad() + $cantidad;
        return $this->stockRepository->updateStockCantidad($idStock, $nuevaCantidad);
    }

    /**
     * Añade un producto al stock de un almacén
     */
    public function addProductToStockAlmacen(int $idAlmacen, int $idProducto, int $cantidad, int $stockMinimo): bool
    {
        // Validaciones básicas
        if ($cantidad < 0) {
            throw new \InvalidArgumentException("La cantidad no puede ser negativa.");
        }
        if ($stockMinimo < 0) {
            throw new \InvalidArgumentException("El stock mínimo no puede ser negativo.");
        }

        return $this->stockRepository->addProductToStockAlmacen($idAlmacen, $idProducto, $cantidad, $stockMinimo);
    }

    /**
     * Obtiene productos con stock bajo en almacenes
     */
    public function getProductosStockBajo(): array
    {
        return $this->stockRepository->getProductosStockBajo();
    }
}
