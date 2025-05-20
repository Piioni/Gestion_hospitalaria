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

    public function createHospital($name, $address, $phone, $email): bool
    {
        return $this->hospitalRepository->create($name, $address, $phone, $email);
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
