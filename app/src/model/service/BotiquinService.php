<?php

namespace model\service;

use model\entity\Botiquin;
use model\repository\BotiquinRepository;
use model\repository\StockRepository;

class BotiquinService
{
    private BotiquinRepository $botiquinRepository;
    private StockRepository $stockRepository;
    private UserLocationService $userLocationService;

    public function __construct()
    {
        $this->botiquinRepository = new BotiquinRepository();
        $this->stockRepository = new StockRepository();
        $this->userLocationService = new UserLocationService();
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

        // Si hay productos y no se proporcionó un almacén destino, validar
        if ($this->countProductos($id_botiquin) > 0 && empty($idAlmacenDestino)) {
            throw new \InvalidArgumentException("Este botiquín tiene productos. Debe seleccionar un almacén destino.");
        }

        // Si hay un almacén destino, transferir los productos
        if ($idAlmacenDestino) {
            // Implementar lógica de transferencia
            $this->transferirProductosAAlmacen($id_botiquin, $idAlmacenDestino);
        }

        return $this->botiquinRepository->delete($id_botiquin);
    }

    private function transferirProductosAAlmacen($idBotiquin, $idAlmacen): bool
    {
        // Implementar la lógica de transferencia de productos
        // Esta es una función simplificada, se debe implementar según la lógica de negocio
        return $this->stockRepository->transferBotiquinStockToAlmacen($idBotiquin, $idAlmacen);
    }

    public function getAllBotiquines(): array
    {
        return $this->botiquinRepository->getAll();
    }

    public function getAllArray(): array
    {
        return $this->botiquinRepository->getAllArray();
    }

    public function getBotiquinesByPlantaId($plantaId): array
    {
        return $this->botiquinRepository->getByPlantaId($plantaId);
    }

    public function getBotiquinById($id_botiquin): ?Botiquin
    {
        return $this->botiquinRepository->getBotiquinById($id_botiquin);
    }

    public function getStockByBotiquinId($id_botiquin): int
    {
        return $this->countProductos($id_botiquin);
    }

    public function countProductos($id_botiquin): int
    {
        return $this->botiquinRepository->countProductos($id_botiquin);
    }

    public function getBotiquinesForUser(int $userId, string $userRole): array
    {
        return match ($userRole) {
            'ADMINISTRADOR', 'GESTOR_GENERAL' => $this->getAllBotiquines(),
            'GESTOR_HOSPITAL' => $this->userLocationService->getAssignedBotiquinesFromHospitals($userId),
            'GESTOR_PLANTA' => $this->userLocationService->getAssignedBotiquinesFromPlantas($userId),
            default => [],
        };
    }
}
