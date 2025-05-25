<?php

namespace model\service;

use model\entity\Botiquin;
use model\repository\BotiquinRepository;

class BotiquinService
{
    private BotiquinRepository $botiquinRepository;

    public function __construct()
    {
        $this->botiquinRepository = new BotiquinRepository();
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

    public function updateBotiquin( $botiquinId, $id_planta, $nombre, $capacidad): bool
    {
        if (empty($id_planta) || empty($nombre)) {
            throw new \InvalidArgumentException("La planta y el nombre son obligatorios.");
        }
        if (!is_numeric($capacidad) || $capacidad <= 0 || $capacidad > 250) {
            throw new \InvalidArgumentException("La capacidad debe ser un número entre 1 y 250.");
        }
        // Verificar que los datos no sea los mismos que ya existen
        $existingBotiquin = $this->botiquinRepository->getBotiquinById($botiquinId);
        if ($existingBotiquin->getIdPlanta() == $id_planta &&
            $existingBotiquin->getNombre() === $nombre &&
            $existingBotiquin->getCapacidad() == $capacidad) {
            throw new \InvalidArgumentException("Los datos proporcionados son iguales a los registrados.");
        }

        return $this->botiquinRepository->update($botiquinId, $id_planta, $nombre, $capacidad);
    }

    public function getAllBotiquines(): array
    {
        return $this->botiquinRepository->getAll();
    }

    public function getBotiquinesByPlantaId($plantaId): array
    {
        return $this->botiquinRepository->getByPlantaId($plantaId);
    }

    public function getBotiquinById($id): Botiquin
    {
        return $this->botiquinRepository->getBotiquinById($id);
    }
}
