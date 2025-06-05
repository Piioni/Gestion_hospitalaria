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

        // Si hay un almacén destino, transferir los productos
        if ($hasProducts && $idAlmacenDestino) {
            $this->transferirProductosAAlmacen($id_botiquin, $idAlmacenDestino);
        }

        return $this->botiquinRepository->delete($id_botiquin);
    }

    /**
     * Transfiere los productos de un botiquín a un almacén
     */
    private function transferirProductosAAlmacen($id_botiquin, $id_almacen): bool
    {
        try {
            // 1. Obtener todos los productos del botiquín
            $stockItems = $this->stockBotiquinService->getStockByBotiquinId($id_botiquin);

            if (empty($stockItems)) {
                return true; // No hay productos para transferir
            }

            // 2. Por cada producto, verificar si existe en el almacén y agregarlo/actualizarlo
            foreach ($stockItems as $stockItem) {
                $id_producto = $stockItem->getIdProducto();
                $cantidad = $stockItem->getCantidad();
                $cantidad_minima = $stockItem->getStockMinimo();

                // Buscar si el producto ya existe en el almacén destino
                $stocksAlmacen = $this->stockAlmacenService->getStockByAlmacenId($id_almacen);
                $existeEnAlmacen = false;

                foreach ($stocksAlmacen as $stockAlmacen) {
                    if ($stockAlmacen->getIdProducto() == $id_producto) {
                        // Actualizar el stock existente
                        $nuevaCantidad = $stockAlmacen->getCantidad() + $cantidad;
                        $this->stockAlmacenService->reponerStock($stockAlmacen->getId(), $cantidad);
                        $existeEnAlmacen = true;
                        break;
                    }
                }

                // Si no existe en el almacén, crear un nuevo registro
                if (!$existeEnAlmacen) {
                    $this->stockAlmacenService->addProductToStockAlmacen(
                        $id_almacen,
                        $id_producto,
                        $cantidad,
                        $cantidad_minima
                    );
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al transferir productos: " . $e->getMessage());
            throw new Exception("No se pudieron transferir los productos al almacén: " . $e->getMessage());
        }
    }

    public function getStockByBotiquinId(int $id_botiquin): array
    {
        return $this->stockBotiquinService->getStockByBotiquinId($id_botiquin);
    }

    public function countProductos(int $id_botiquin): int
    {
        return count($this->stockBotiquinService->getStockByBotiquinId($id_botiquin));
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
}