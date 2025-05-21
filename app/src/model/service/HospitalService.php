<?php

namespace model\service;

use model\repository\HospitalRepository;
use InvalidArgumentException;

class HospitalService
{
    private HospitalRepository $hospitalRepository;

    public function __construct()
    {
        $this->hospitalRepository = new HospitalRepository();
    }

    public function createHospital($name, $address): bool
    {
        // Validación de reglas de negocio
        if (empty($name)) {
            throw new InvalidArgumentException("El nombre del hospital es obligatorio");
        }

        if (empty($address)) {
            throw new InvalidArgumentException("La dirección del hospital es obligatoria");
        }

        // Validación de duplicados
        if ($this->hospitalRepository->existsByName($name)) {
            throw new InvalidArgumentException("Ya existe un hospital con el nombre '$name'");
        }

        if ($this->hospitalRepository->existsByAddress($address)) {
            throw new InvalidArgumentException("Ya existe un hospital con la dirección '$address'");
        }

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

    public function updateHospital($id, $name, $address): bool
    {
        // Validación de reglas de negocio
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        if (empty($name)) {
            throw new InvalidArgumentException("El nombre del hospital es obligatorio");
        }

        if (empty($address)) {
            throw new InvalidArgumentException("La dirección del hospital es obligatoria");
        }

        // Validación de duplicados (excepto el mismo hospital)
        if ($this->hospitalRepository->existsByNameExceptId($name, $id)) {
            throw new InvalidArgumentException("Ya existe otro hospital con el nombre '$name'");
        }

        if ($this->hospitalRepository->existsByAddressExceptId($address, $id)) {
            throw new InvalidArgumentException("Ya existe otro hospital con la dirección '$address'");
        }

        return $this->hospitalRepository->update($id, $name, $address);
    }

    /**
     * Verifica si un hospital tiene plantas asociadas
     * @param int $id ID del hospital
     * @return array Información sobre las plantas asociadas
     */
    public function checkHospitalRelations($id): array
    {
        // Validación del ID
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }
        
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->getHospitalById($id);
        if (empty($hospital)) {
            throw new InvalidArgumentException("El hospital no existe");
        }
        
        // Obtener plantas relacionadas
        $relatedPlants = $this->hospitalRepository->getRelatedPlants($id);
        
        return [
            'hospital' => $hospital,
            'relatedPlants' => $relatedPlants,
            'canDelete' => empty($relatedPlants)
        ];
    }

    /**
     * Elimina un hospital si no tiene dependencias o si se fuerza la eliminación
     * @param int $id ID del hospital
     * @param bool $force Si es true, fuerza la eliminación incluso con dependencias
     * @return bool Resultado de la operación
     */
    public function deleteHospital($id, $force = false): bool
    {
        // Validación del ID
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }
        
        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->getHospitalById($id);
        if (empty($hospital)) {
            throw new InvalidArgumentException("El hospital no existe");
        }
        
        // Verificar si hay plantas asociadas
        $plantCount = $this->hospitalRepository->countRelatedPlants($id);
        
        // Si hay plantas asociadas y no se fuerza la eliminación, no permitir
        if ($plantCount > 0 && !$force) {
            throw new InvalidArgumentException("No se puede eliminar el hospital porque tiene $plantCount planta(s) asociada(s)");
        }
        
        // Eliminar el hospital
        $result = $this->hospitalRepository->delete($id);
        if (!$result) {
            throw new InvalidArgumentException("No se pudo eliminar el hospital");
        }
        return true;
    }
}
