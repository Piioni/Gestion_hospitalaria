<?php

namespace model\service;

use model\entity\Botiquin;
use model\repository\BotiquinRepository;
use model\service\StockBotiquinService;
use model\service\StockAlmacenService;
use model\entity\StockBotiquin;
use InvalidArgumentException;
use Exception;

class BotiquinService
{
    private BotiquinRepository $botiquinRepository;
    private UserLocationService $userLocationService;
    private StockBotiquinService $stockBotiquinService;
    private StockAlmacenService $stockAlmacenService;

    public function __construct()
    {
        $this->botiquinRepository = new BotiquinRepository();
        $this->userLocationService = new UserLocationService();
        $this->stockBotiquinService = new StockBotiquinService();
        $this->stockAlmacenService = new StockAlmacenService();
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
            throw new InvalidArgumentException("El ID del botiquín es inválido.");
        }

        // Verificar si el botiquín tiene productos
        $stockBotiquin = $this->stockBotiquinService->getStockByBotiquinId($id_botiquin);
        $hasProducts = !empty($stockBotiquin);

        // Si hay productos y no se proporcionó un almacén destino, validar
        if ($hasProducts && empty($idAlmacenDestino)) {
            throw new InvalidArgumentException("Este botiquín tiene productos. Debe seleccionar un almacén destino.");
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

    public function getBotiquinById($id_botiquin): ?Botiquin
    {
        return $this->botiquinRepository->getBotiquinById($id_botiquin);
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

    public function filterBotiquines($filtro_plantas, $id_botiquin, $filtroNombre): array
    {
        // Filtrar los botiquines por planta o por ID específico
        if ($id_botiquin) {
            // Si se proporciona un ID específico de botiquín
            $botiquin = $this->getBotiquinById($id_botiquin);
            return $botiquin ? [$botiquin] : [];
        } elseif ($filtro_plantas && $filtroNombre) {
            // Filtrar por planta y nombre
            return array_filter($this->getBotiquinesByPlantaId($filtro_plantas), function ($b) use ($filtroNombre) {
                return stripos($b->getNombre(), $filtroNombre) !== false;
            });
        } elseif ($filtro_plantas) {
            // Filtrar por planta
            return $this->getBotiquinesByPlantaId($filtro_plantas);
        } elseif ($filtroNombre) {
            // Filtrar por nombre
            return array_filter($this->getAllBotiquines(), function ($b) use ($filtroNombre) {
                return stripos($b->getNombre(), $filtroNombre) !== false;
            });
        } else {
            // Sin filtros, mostrar todos
            return $this->getAllBotiquines();
        }
    }
}