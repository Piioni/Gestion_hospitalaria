<?php

namespace model\service;

use model\repository\PlantaRepository;
use InvalidArgumentException;

class PlantaService
{
    private PlantaRepository $plantaRepository;

    public function __construct()
    {
        $this->plantaRepository = new PlantaRepository();
    }

    public function getAllArray(): array
    {
        return $this->plantaRepository->getAllArray();
    }

    public function getAllPlantas(): array
    {
        return $this->plantaRepository->getAll();
    }
    
    public function getPlantasByHospitalId($hospitalId): array
    {
        if (empty($hospitalId) || !is_numeric($hospitalId)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }
        
        return $this->plantaRepository->getByHospitalId($hospitalId);
    }
    
    public function getPlantaById($id): array
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID de la planta es inválido");
        }
        
        $planta = $this->plantaRepository->getPlantaById($id);
        
        if (empty($planta)) {
            throw new InvalidArgumentException("La planta no existe");
        }
        
        return $planta;
    }
    
    public function createPlanta($hospitalId, $nombre, $especialidad = null): bool
    {
        if (empty($hospitalId) || !is_numeric($hospitalId)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }
        
        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre de la planta es obligatorio");
        }
        
        return $this->plantaRepository->create($hospitalId, $nombre, $especialidad);
    }
}
