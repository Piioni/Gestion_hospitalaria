<?php

namespace model\service;

use model\entity\Planta;
use model\repository\PlantaRepository;
use InvalidArgumentException;

class PlantaService
{
    private PlantaRepository $plantaRepository;
    private UserLocationService $userLocationService;

    public function __construct()
    {
        $this->plantaRepository = new PlantaRepository();
        $this->userLocationService = new UserLocationService();
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


    public function getPlantaById($id): ?Planta
    {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("El ID de la planta es inválido");
        }

        return $this->plantaRepository->getById($id);
    }

    /**
     * Obtiene las plantas filtradas por permisos de usuario y criterios de búsqueda
     * 
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario
     * @param $filtroHospital
     * @param string|null $filtroNombre Nombre para filtrar
     * @return array Lista de plantas filtradas
     */
    public function getPlantasForUser(int $userId, string $userRole, $filtroHospital = null, ?string $filtroNombre = null): array
    {
        // Obtener plantas según los permisos del usuario
        $plantas = $this->getPlantsBasedOnUserRole($userId, $userRole);
        
        // Aplicar filtros
        return $this->applyFilters($plantas, $filtroHospital, $filtroNombre);
    }
    
    /**
     * Obtiene las plantas según el rol de usuario
     * 
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario
     * @return array Lista de plantas según permisos
     */
    private function getPlantsBasedOnUserRole(int $userId, string $userRole): array
    {
        return match ($userRole) {
            'ADMINISTRADOR', 'GESTOR_GENERAL' => $this->getAllPlantas(),
            'GESTOR_HOSPITAL' => $this->userLocationService->getPlantsFromAssignedHospitals($userId),
            'GESTOR_PLANTA' => $this->userLocationService->getAssignedPlantas($userId),
            default => [],
        };
    }
    
    /**
     * Aplica filtros a la lista de plantas
     * 
     * @param array $plantas Lista de plantas
     * @param $filtroHospital hospital para filtrar
     * @param string|null $filtroNombre Nombre para filtrar
     * @return array Lista de plantas filtradas
     */
    private function applyFilters(array $plantas, $filtroHospital = null, ?string $filtroNombre = null): array
    {
        if ($filtroHospital) {
            $plantas = array_filter($plantas, function($planta) use ($filtroHospital) {
                return $planta->getIdHospital() == $filtroHospital;
            });
        }
        
        if ($filtroNombre) {
            $plantas = array_filter($plantas, function($planta) use ($filtroNombre) {
                return stripos($planta->getNombre(), $filtroNombre) !== false;
            });
        }
        
        // Reindexar el array para evitar índices faltantes
        return array_values($plantas);
    }

    /**
     * Verifica si un usuario tiene acceso a una planta específica
     * 
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario
     * @param int $plantaId ID de la planta
     * @return bool True si el usuario tiene acceso
     */
    public function userHasAccessToPlanta(int $userId, string $userRole, int $plantaId): bool
    {
        // Administradores y gestores generales tienen acceso a todas las plantas
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return true;
        }
        
        // Obtenemos la planta para verificar el hospital al que pertenece
        $planta = $this->getPlantaById($plantaId);
        if (!$planta) {
            return false;
        }
        
        // Para gestores de hospital, verificamos si tienen asignado el hospital de la planta
        if ($userRole === 'GESTOR_HOSPITAL') {
            return $this->userLocationService->userHasHospitalAccess($userId, $planta->getIdHospital());
        }
        
        // Para gestores de planta, verificamos si tienen asignada específicamente esta planta
        if ($userRole === 'GESTOR_PLANTA') {
            return $this->userLocationService->userHasPlantaAccess($userId, $plantaId);
        }
        
        return false;
    }
}
