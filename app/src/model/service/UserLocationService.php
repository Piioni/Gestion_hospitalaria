<?php

namespace model\service;

use model\repository\UserLocationRepository;
use model\repository\HospitalRepository;
use model\repository\PlantaRepository;

/**
 * Servicio centralizado para gestionar las asignaciones de ubicaciones a usuarios
 * y obtener entidades filtradas por permisos
 */
class UserLocationService
{
    private UserLocationRepository $userLocationRepository;
    private HospitalRepository $hospitalRepository;
    private PlantaRepository $plantaRepository;
    
    public function __construct()
    {
        $this->userLocationRepository = new UserLocationRepository();
        $this->hospitalRepository = new HospitalRepository();
        $this->plantaRepository = new PlantaRepository();
    }

    /**
     * Obtiene hospitales asignados a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de hospitales asignados
     */
    public function getAssignedHospitals(int $userId): array
    {
        return $this->userLocationRepository->getHospitalsByUserId($userId);
    }
    
    /**
     * Obtiene plantas asignadas directamente a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de plantas asignadas
     */
    public function getAssignedPlantas(int $userId): array
    {
        return $this->userLocationRepository->getPlantasByUserId($userId);
    }
    
    /**
     * Obtiene plantas de los hospitales asignados a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de plantas
     */
    public function getPlantsFromAssignedHospitals(int $userId): array
    {
        // Obtener IDs de hospitales asignados
        $hospitales = $this->getAssignedHospitals($userId);
        if (empty($hospitales)) {
            return [];
        }
        
        // Obtener IDs de hospitales
        $hospitalIds = array_map(function($hospital) {
            return $hospital->getId();
        }, $hospitales);
        
        // Obtener todas las plantas
        $plantas = [];
        foreach ($hospitalIds as $hospitalId) {
            $plantasDeHospital = $this->plantaRepository->getByHospitalId($hospitalId);
            $plantas = array_merge($plantas, $plantasDeHospital);
        }
        
        return $plantas;
    }
    
    /**
     * Obtiene botiquines asignados a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de botiquines asignados
     */
    public function getAssignedBotiquines(int $userId): array
    {
        return $this->userLocationRepository->getBotiquinesByUserId($userId);
    }
    
    /**
     * Asigna un hospital a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $hospitalId ID del hospital
     * @return bool Resultado de la operación
     */
    public function assignHospital(int $userId, int $hospitalId): bool
    {
        return $this->userLocationRepository->addUserHospital($userId, $hospitalId);
    }
    
    /**
     * Asigna una planta a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $plantaId ID de la planta
     * @return bool Resultado de la operación
     */
    public function assignPlanta(int $userId, int $plantaId): bool
    {
        return $this->userLocationRepository->addUserPlanta($userId, $plantaId);
    }
    
    /**
     * Asigna un botiquín a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $botiquinId ID del botiquín
     * @return bool Resultado de la operación
     */
    public function assignBotiquin(int $userId, int $botiquinId): bool
    {
        return $this->userLocationRepository->addUserBotiquin($userId, $botiquinId);
    }
    
    /**
     * Verifica si un usuario tiene alguna ubicación asignada
     * 
     * @param int $userId ID del usuario
     * @return bool True si tiene al menos una ubicación
     */
    public function hasAssignedLocations(int $userId): bool
    {
        $hospitales = $this->getAssignedHospitals($userId);
        $plantas = $this->getAssignedPlantas($userId);
        $botiquines = $this->getAssignedBotiquines($userId);
        
        return !empty($hospitales) || !empty($plantas) || !empty($botiquines);
    }
}
