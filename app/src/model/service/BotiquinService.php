<?php

namespace model\service;

use model\entity\Botiquin;
use model\repository\BotiquinRepository;
use model\service\StockService;

class BotiquinService
{
    private BotiquinRepository $botiquinRepository;
    private StockService $stockService;

    public function __construct()
    {
        $this->botiquinRepository = new BotiquinRepository();
        $this->stockService = new StockService();
    }

    public function createBotiquin($id_planta, $nombre, $capacidad): bool
    {
        if (empty($id_planta) || empty($nombre)) {
            throw new \InvalidArgumentException("La planta y el nombre son obligatorios.");
        }
        if (!is_numeric($capacidad) || $capacidad <= 0 || $capacidad > 250) {
            throw new \InvalidArgumentException("La capacidad debe ser un número entre 1 y 250.");
        }

        return $this->botiquinRepository->create($id_planta, $nombre, $capacidad);
    }

    public function updateBotiquin($id_botiquin, $id_planta, $nombre, $capacidad): bool
    {
        if (empty($id_planta) || empty($nombre)) {
            throw new \InvalidArgumentException("La planta y el nombre son obligatorios.");
        }
        if (!is_numeric($capacidad) || $capacidad <= 0 || $capacidad > 250) {
            throw new \InvalidArgumentException("La capacidad debe ser un número entre 1 y 250.");
        }
        // Verificar que los datos no sea los mismos que ya existen
        $existingBotiquin = $this->botiquinRepository->getBotiquinById($id_botiquin);
        if ($existingBotiquin->getIdPlanta() == $id_planta &&
            $existingBotiquin->getNombre() === $nombre &&
            $existingBotiquin->getCapacidad() == $capacidad) {
            throw new \InvalidArgumentException("Los datos proporcionados son iguales a los registrados.");
        }

        return $this->botiquinRepository->update($id_botiquin, $id_planta, $nombre, $capacidad);
    }

    public function deleteBotiquin($id_botiquin, $idAlmacenDestino = null): bool
    {
        if (empty($id_botiquin) || !is_numeric($id_botiquin)) {
            throw new \InvalidArgumentException("El ID del botiquín es inválido.");
        }
        
        // Verificar si el botiquín tiene productos
        $hasProducts = $this->stockService->botiquinHasProducts($id_botiquin);
        
        // Si tiene productos y no se especificó un almacén destino, lanzar excepción
        if ($hasProducts && $idAlmacenDestino === null) {
            throw new \InvalidArgumentException("El botiquín tiene productos asociados. Debe seleccionar un almacén destino para transferirlos.");
        }
        
        // Si hay productos y se especificó un almacén, transferir los productos
        if ($hasProducts && $idAlmacenDestino !== null) {
            $this->stockService->transferirProductosBotiquinToAlmacen($id_botiquin, $idAlmacenDestino);
        }

        return $this->botiquinRepository->delete($id_botiquin);
    }

    public function getAllBotiquines(): array
    {
        return $this->botiquinRepository->getAll();
    }

    public function getBotiquinesByPlantaId($plantaId): array
    {
        return $this->botiquinRepository->getByPlantaId($plantaId);
    }

    public function getBotiquinById($id_botiquin): Botiquin
    {
        return $this->botiquinRepository->getBotiquinById($id_botiquin);
    }

    public function getBotiquinProducts($id_botiquin): int
    {
        return $this->botiquinRepository->countProductos($id_botiquin);
    }
}
