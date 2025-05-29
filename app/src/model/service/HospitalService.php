<?php

namespace model\service;

use model\entity\Almacen;
use model\entity\Hospital;
use model\repository\HospitalRepository;
use InvalidArgumentException;

class HospitalService
{
    private HospitalRepository $hospitalRepository;

    public function __construct()
    {
        $this->hospitalRepository = new HospitalRepository();
    }

    public function createHospital($nombre, $ubicacion): bool
    {
        // Validación de reglas de negocio
        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre del hospital es obligatorio");
        }

        if (empty($ubicacion)) {
            throw new InvalidArgumentException("La dirección del hospital es obligatoria");
        }

        // Validación de duplicados
        if ($this->hospitalRepository->existsByName($nombre)) {
            throw new InvalidArgumentException("Ya existe un hospital con el nombre '$nombre'");
        }

        if ($this->hospitalRepository->existsByAddress($ubicacion)) {
            throw new InvalidArgumentException("Ya existe un hospital con la dirección '$ubicacion'");
        }

        return $this->hospitalRepository->create($nombre, $ubicacion);
    }

    public function deleteHospital($id_hospital, $force = false): bool
    {
        // Validación del ID
        if (empty($id_hospital) || !is_numeric($id_hospital)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->getById($id_hospital);
        if (empty($hospital)) {
            throw new InvalidArgumentException("El hospital no existe");
        }

        // Verificar si hay plantas asociadas
        $plantCount = $this->hospitalRepository->countRelatedPlants($id_hospital);

        // Si hay plantas asociadas y no se fuerza la eliminación, no permitir
        if ($plantCount > 0 && !$force) {
            throw new InvalidArgumentException("No se puede eliminar el hospital porque tiene $plantCount planta(s) asociada(s)");
        }

        // Eliminar el hospital
        $result = $this->hospitalRepository->delete($id_hospital);
        if (!$result) {
            throw new InvalidArgumentException("No se pudo eliminar el hospital");
        }
        return true;
    }

    public function updateHospital($id_hospital, $nombre, $ubicacion): bool
    {
        // Validación de reglas de negocio
        if (empty($id_hospital) || !is_numeric($id_hospital)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre del hospital es obligatorio");
        }

        if (empty($ubicacion)) {
            throw new InvalidArgumentException("La dirección del hospital es obligatoria");
        }

        // Validación de duplicados (excepto el mismo hospital)
        if ($this->hospitalRepository->existsByNameExceptId($nombre, $id_hospital)) {
            throw new InvalidArgumentException("Ya existe otro hospital con el nombre '$nombre'");
        }

        if ($this->hospitalRepository->existsByAddressExceptId($ubicacion, $id_hospital)) {
            throw new InvalidArgumentException("Ya existe otro hospital con la dirección '$ubicacion'");
        }

        // Verificar que los datos no sean los mismos que ya existen
        $existingHospital = $this->hospitalRepository->getById($id_hospital);
        if ($existingHospital->getNombre() === $nombre &&
            $existingHospital->getUbicacion() === $ubicacion) {
            throw new InvalidArgumentException("Los datos proporcionados son los mismos registrados en el sistema.");
        }

        return $this->hospitalRepository->update($id_hospital, $nombre, $ubicacion);
    }

    public function getAllHospitals(): array
    {
        return $this->hospitalRepository->getAll();
    }

    public function getHospitalById($id_hospital): ?Hospital
    {
        return $this->hospitalRepository->getById($id_hospital);
    }

    /**
     * Obtiene los hospitales filtrados por nombre y permisos de usuario
     *
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario
     * @param string|null $filtroNombre Filtro opcional por nombre
     * @return array Lista de hospitales filtrados
     */
    public function getHospitalsForUser(int $userId, string $userRole, ?string $filtroNombre = null): array
    {
        // Obtener hospitales según el rol del usuario
        $hospitals = [];

        switch ($userRole) {
            case 'ADMINISTRADOR':
            case 'GESTOR_GENERAL':
                $hospitals = $this->getAllHospitals();
                break;
            case 'GESTOR_HOSPITAL':
                $hospitals = $this->hospitalRepository->getHospitalsByUserId($userId);
                break;
            default:
                return [];
        }

        // Aplicar filtro de nombre si existe
        if ($filtroNombre) {
            $hospitals = array_filter($hospitals, function ($hospital) use ($filtroNombre) {
                return stripos($hospital->getNombre(), $filtroNombre) !== false;
            });
        }

        return $hospitals;
    }

    /**
     * Verifica si un hospital tiene plantas asociadas
     * @param int $id_hospital ID del hospital
     * @return array Información sobre las plantas asociadas
     */
    public function checkHospitalRelations($id_hospital): array
    {
        // Validación del ID
        if (empty($id_hospital) || !is_numeric($id_hospital)) {
            throw new InvalidArgumentException("El ID del hospital es inválido");
        }

        // Verificar que el hospital existe
        $hospital = $this->hospitalRepository->getById($id_hospital);
        if (empty($hospital)) {
            throw new InvalidArgumentException("El hospital no existe");
        }

        // Obtener plantas relacionadas
        $relatedPlants = $this->hospitalRepository->getRelatedPlants($id_hospital);

        return [
            'hospital' => $hospital,
            'relatedPlants' => $relatedPlants,
            'canDelete' => empty($relatedPlants)
        ];
    }

}
