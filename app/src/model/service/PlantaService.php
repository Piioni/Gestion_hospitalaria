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

        // Verificar que los datos no sean los mismos que ya existen
        $existingPlanta = $this->plantaRepository->getById($id);
        if ($existingPlanta->getIdHospital() == $hospitalId &&
            $existingPlanta->getNombre() === $nombre) {
            throw new InvalidArgumentException("Los datos proporcionados son los mismos registrados.");
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

    // metodo para obtener todas las plantas como un array asociativo para darselo a javascript
    public function getAllArray(): array
    {
        return $this->plantaRepository->getAllArray();
    }

    public function getPlantasByHospitalId($hospitalId): array
    {
        if (empty($hospitalId) || !is_numeric($hospitalId)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        return $this->plantaRepository->getByHospitalId($hospitalId);
    }

    public function getPlantasByNombre($nombre): array
    {
        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre de la planta es obligatorio");
        }

        return $this->plantaRepository->getByNombre($nombre);
    }

    public function getPlantasByHospitalAndNombre( $hospitalId, $nombre): array
    {
        if (empty($hospitalId) || !is_numeric($hospitalId)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre de la planta es obligatorio");
        }

        return $this->plantaRepository->getByHospitalAndNombre($hospitalId, $nombre);
    }

    public function getPlantaById($id): ?Planta
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID de la planta es inválido");
        }

        return $this->plantaRepository->getById($id);
    }
}
