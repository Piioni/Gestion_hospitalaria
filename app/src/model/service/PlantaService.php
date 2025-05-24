<?php

namespace model\service;

use model\entity\Planta;
use model\repository\PlantaRepository;
use InvalidArgumentException;

class PlantaService
{
    private PlantaRepository $plantaRepository;

    public function __construct()
    {
        $this->plantaRepository = new PlantaRepository();
    }

    public function createPlanta($id_hospital, $nombre): bool
    {
        if (empty($id_hospital) || !is_numeric($id_hospital)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre de la planta es obligatorio");
        }

        return $this->plantaRepository->create($id_hospital, $nombre);
    }

    public function updatePlanta($id, $hospitalId, $nombre): bool
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID de la planta es inválido");
        }

        if (empty($hospitalId) || !is_numeric($hospitalId)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre de la planta es obligatorio");
        }

        return $this->plantaRepository->update($id, $hospitalId, $nombre);
    }

    public function deletePlanta($id): bool
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID de la planta es inválido");
        }

        return $this->plantaRepository->delete($id);
    }

    public function getAllPlantas(): array
    {
        return $this->plantaRepository->getAll();
    }

    public function getAllArray(): array
    {
        return $this->plantaRepository->getAllArray();
    }

    public function getPlantasByHospitalId($hospitalId): array
    {
        if (empty($hospitalId) || !is_numeric($hospitalId)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        return $this->plantaRepository->getPlantasByHospitalId($hospitalId);
    }

    public function getPlantaById($id): Planta
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID de la planta es inválido");
        }

        return $this->plantaRepository->getPlantaById($id);
    }
}
