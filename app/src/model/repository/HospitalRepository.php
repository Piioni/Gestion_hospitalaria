<?php

namespace model\repository;

use model\Database;
use model\entity\Hospital;
use PDO;

class HospitalRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function create($name, $address, $phone, $email): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO hospitales (name, address, phone, email) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $address, $phone, $email]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM hospitales");
        $hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hospitalObjects = [];
        foreach ($hospitals as $hospitalData) {
            $hospitalObjects[] = $this->createHospitalFromData($hospitalData);
        }
        return $hospitalObjects;
    }
    
    public function getHospitalById($id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM hospitales WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function createHospitalFromData($hospitalData): Hospital
    {
        return new Hospital($hospitalData['id_hospital'], $hospitalData['nombre'], $hospitalData['ubicacion']);
    }
}
