<?php

namespace model\service;

use model\entity\StockBotiquin;
use model\repository\StockBotiquinRepository;

class StockBotiquinService
{
    private StockBotiquinRepository $stockRepository;
    
    public function __construct()
    {
        $this->stockRepository = new StockBotiquinRepository();
    }

    public function getStockByBotiquinId(int $idBotiquin): array
    {
        return $this->stockRepository->getStocksByBotiquinId($idBotiquin);
    }

    public function getTotalStockByBotiquinId(int $idBotiquin): int
    {
        return $this->stockRepository->getTotalStockByBotiquinId($idBotiquin);
    }

    public function getStockById(int $idStock): ?StockBotiquin
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


    public function devolver(){

    }


    public function botiquinHasProducts($id_botiquin): array
    {
        if (empty($id_botiquin) || !is_numeric($id_botiquin)) {
            throw new \InvalidArgumentException("El ID del botiquín es inválido.");
        }

        return $this->stockRepository->getStocksByBotiquinId($id_botiquin);
    }


}
