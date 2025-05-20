<?php

namespace model\service;

use model\repository\PlantaRepository;

class PlantaService
{
    private PlantaRepository $plantaRepository;

    public function __construct()
    {
        $this->plantaRepository = new PlantaRepository();
    }

    public function getAllPlantas(): array
    {
        return $this->plantaRepository->getAll();
    }
    
    public function getPlantasByHospitalId($hospitalId): array
    {
        return $this->plantaRepository->getByHospitalId($hospitalId);
    }
    
    public function getPlantaById($id): array
    {
        return $this->plantaRepository->getPlantaById($id);
    }
}
