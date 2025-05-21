<?php

namespace model\service;

use model\repository\HospitalRepository;

class HospitalService
{
    private HospitalRepository $hospitalRepository;

    public function __construct()
    {
        $this->hospitalRepository = new HospitalRepository();
    }

    public function createHospital($name, $address): bool
    {
        return $this->hospitalRepository->create($name, $address);
    }
    
    public function getAllHospitals(): array
    {
        return $this->hospitalRepository->getAll();
    }
    
    public function getHospitalById($id): array
    {
        return $this->hospitalRepository->getHospitalById($id);
    }
}
