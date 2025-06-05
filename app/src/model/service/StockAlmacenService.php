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

    public function getStockByAlmacenId(int $idAlmacen): array
    {
        return $this->stockRepository->getStockByAlmacenId($idAlmacen);
    }

    public function getStockById(int $idStock): ?StockAlmacen
    {
        return $this->stockRepository->getStockById($idStock);
    }

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


}