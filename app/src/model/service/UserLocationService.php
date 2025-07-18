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

    public function getAssignedAlmacenesFromHospitals(int $userId): array
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

        // Obtener almacenes de los hospitales
        $almacenes = [];
        foreach ($hospitalIds as $hospitalId) {
            $almacenesDeHospital = $this->userLocationRepository->getAlmacenesByHospitalId($hospitalId);
            $almacenes = array_merge($almacenes, $almacenesDeHospital);
        }

        return $almacenes;
    }

    public function getAssignedAlmacenesFromPlantas(int $userId): array
    {
        // Obtener plantas asignadas al usuario
        $plantas = $this->getAssignedPlantas($userId);
        if (empty($plantas)) {
            return [];
        }

        // Obtener IDs de plantas
        $plantaIds = array_map(function($planta) {
            return $planta->getId();
        }, $plantas);

        // Obtener almacenes de las plantas
        $almacenes = [];
        foreach ($plantaIds as $plantaId) {
            $almacenesDePlanta = $this->userLocationRepository->getAlmacenesByPlantaId($plantaId);
            $almacenes = array_merge($almacenes, $almacenesDePlanta);
        }

        return $almacenes;
    }

    public function getAssignedBotiquinesFromHospitals(int $userId): array
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

        // Obtener botiquines de los hospitales
        $botiquines = [];
        foreach ($hospitalIds as $hospitalId) {
            $botiquinesDeHospital = $this->userLocationRepository->getBotiquinesByHospitalId($hospitalId);
            $botiquines = array_merge($botiquines, $botiquinesDeHospital);
        }

        return $botiquines;
    }

    public function getAssignedBotiquinesFromPlantas(int $userId): array
    {
        // Obtener plantas asignadas al usuario
        $plantas = $this->getAssignedPlantas($userId);
        if (empty($plantas)) {
            return [];
        }

        // Obtener IDs de plantas
        $plantaIds = array_map(function($planta) {
            return $planta->getId();
        }, $plantas);

        // Obtener botiquines de las plantas
        $botiquines = [];
        foreach ($plantaIds as $plantaId) {
            $botiquinesDePlanta = $this->userLocationRepository->getBotiquinesByPlantaId($plantaId);
            $botiquines = array_merge($botiquines, $botiquinesDePlanta);
        }

        return $botiquines;
    }

    public function assignHospital(int $userId, int $hospitalId): bool
    {
        return $this->userLocationRepository->addUserHospital($userId, $hospitalId);
    }

    public function assignPlanta(int $userId, int $plantaId): bool
    {
        return $this->userLocationRepository->addUserPlanta($userId, $plantaId);
    }

    public function assignBotiquin(int $userId, int $botiquinId): bool
    {
        return $this->userLocationRepository->addUserBotiquin($userId, $botiquinId);
    }

    public function hasAssignedLocations(int $userId): bool
    {
        $hospitales = $this->getAssignedHospitals($userId);
        $plantas = $this->getAssignedPlantas($userId);
        $botiquines = $this->getAssignedBotiquines($userId);
        
        return !empty($hospitales) || !empty($plantas) || !empty($botiquines);
    }

    public function userHasHospitalAccess(int $userId, int $hospitalId): bool
    {
        $hospitales = $this->getAssignedHospitals($userId);
        foreach ($hospitales as $hospital) {
            if ($hospital->getId() === $hospitalId) {
                return true;
            }
        }
        return false;
    }

    public function userHasPlantaAccess(int $userId, int $plantaId): bool
    {
        $plantas = $this->getAssignedPlantas($userId);
        foreach ($plantas as $planta) {
            if ($planta->getId() === $plantaId) {
                return true;
            }
        }
        return false;
    }

    public function userHasBotiquinAccess(int $userId, int $botiquinId): bool
    {
        $botiquines = $this->getAssignedBotiquines($userId);
        foreach ($botiquines as $botiquin) {
            if ($botiquin->getId() === $botiquinId) {
                return true;
            }
        }
        return false;
    }
}
