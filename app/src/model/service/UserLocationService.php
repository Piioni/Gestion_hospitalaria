<?php

namespace model\service;

use model\repository\UserLocationRepository;

class UserLocationService
{
    private UserLocationRepository $userLocationRepository;
    
    public function __construct()
    {
        $this->userLocationRepository = new UserLocationRepository();
    }
    
    /**
     * Verifica si un usuario tiene acceso a un hospital específico
     * 
     * @param int $userId ID del usuario
     * @param int $hospitalId ID del hospital
     * @return bool True si tiene acceso, false en caso contrario
     */
    public function userHasHospitalAccess(int $userId, int $hospitalId): bool
    {
        $hospitales = $this->userLocationRepository->getUserHospitales($userId);
        
        if (empty($hospitales)) {
            return false;
        }
        
        foreach ($hospitales as $hospital) {
            if ($hospital['id_hospital'] == $hospitalId) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verifica si un usuario tiene acceso a una planta específica
     * 
     * @param int $userId ID del usuario
     * @param int $plantaId ID de la planta
     * @return bool True si tiene acceso, false en caso contrario
     */
    public function userHasPlantaAccess(int $userId, int $plantaId): bool
    {
        $plantas = $this->userLocationRepository->getUserPlantas($userId);
        
        if (empty($plantas)) {
            return false;
        }
        
        foreach ($plantas as $planta) {
            if ($planta['id_planta'] == $plantaId) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verifica si un usuario tiene acceso a un botiquín específico
     * 
     * @param int $userId ID del usuario
     * @param int $botiquinId ID del botiquín
     * @return bool True si tiene acceso, false en caso contrario
     */
    public function userHasBotiquinAccess(int $userId, int $botiquinId): bool
    {
        $botiquines = $this->userLocationRepository->getUserBotiquines($userId);
        
        if (empty($botiquines)) {
            return false;
        }
        
        foreach ($botiquines as $botiquin) {
            if ($botiquin['id_botiquin'] == $botiquinId) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtiene los hospitales asignados a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de hospitales asignados
     */
    public function getUserHospitals(int $userId): array
    {
        return $this->userLocationRepository->getUserHospitales($userId);
    }
    
    /**
     * Obtiene las plantas asignadas a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de plantas asignadas
     */
    public function getUserPlantas(int $userId): array
    {
        return $this->userLocationRepository->getUserPlantas($userId);
    }
    
    /**
     * Obtiene los botiquines asignados a un usuario
     * 
     * @param int $userId ID del usuario
     * @return array Lista de botiquines asignados
     */
    public function getUserBotiquines(int $userId): array
    {
        return $this->userLocationRepository->getUserBotiquines($userId);
    }
    
    /**
     * Asigna un hospital a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $hospitalId ID del hospital
     * @return bool True si la operación fue exitosa
     */
    public function assignHospitalToUser(int $userId, int $hospitalId): bool
    {
        return $this->userLocationRepository->addUserLocation($userId, $hospitalId, 'hospital');
    }
    
    /**
     * Asigna una planta a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $plantaId ID de la planta
     * @return bool True si la operación fue exitosa
     */
    public function assignPlantaToUser(int $userId, int $plantaId): bool
    {
        return $this->userLocationRepository->addUserLocation($userId, $plantaId, 'planta');
    }
    
    /**
     * Asigna un botiquín a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $botiquinId ID del botiquín
     * @return bool True si la operación fue exitosa
     */
    public function assignBotiquinToUser(int $userId, int $botiquinId): bool
    {
        return $this->userLocationRepository->addUserLocation($userId, $botiquinId, 'botiquin');
    }
    
    /**
     * Elimina la asignación de un hospital a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $hospitalId ID del hospital
     * @return bool True si la operación fue exitosa
     */
    public function removeHospitalFromUser(int $userId, int $hospitalId): bool
    {
        return $this->userLocationRepository->removeUserLocation($userId, $hospitalId, 'hospital');
    }
    
    /**
     * Elimina la asignación de una planta a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $plantaId ID de la planta
     * @return bool True si la operación fue exitosa
     */
    public function removePlantaFromUser(int $userId, int $plantaId): bool
    {
        return $this->userLocationRepository->removeUserLocation($userId, $plantaId, 'planta');
    }
    
    /**
     * Elimina la asignación de un botiquín a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $botiquinId ID del botiquín
     * @return bool True si la operación fue exitosa
     */
    public function removeBotiquinFromUser(int $userId, int $botiquinId): bool
    {
        return $this->userLocationRepository->removeUserLocation($userId, $botiquinId, 'botiquin');
    }
    
    /**
     * Elimina todas las ubicaciones asignadas a un usuario
     * 
     * @param int $userId ID del usuario
     * @return void
     */
    public function removeAllLocationsFromUser(int $userId): void
    {
        $this->userLocationRepository->deleteAllUserLocations($userId);
    }
    
    /**
     * Verifica si un usuario tiene asignada alguna ubicación
     * 
     * @param int $userId ID del usuario
     * @return bool True si tiene al menos una ubicación asignada
     */
    public function userHasLocations(int $userId): bool
    {
        $hospitales = $this->getUserHospitals($userId);
        $plantas = $this->getUserPlantas($userId);
        $botiquines = $this->getUserBotiquines($userId);
        
        return !empty($hospitales) || !empty($plantas) || !empty($botiquines);
    }
    
    /**
     * Obtiene los IDs de hospitales a los que un usuario tiene acceso
     * 
     * @param int $userId ID del usuario
     * @return array Lista de IDs de hospitales
     */
    public function getUserHospitalIds(int $userId): array
    {
        $hospitales = $this->userLocationRepository->getUserHospitales($userId);
        return array_column($hospitales, 'id_hospital');
    }
    
    /**
     * Obtiene los IDs de plantas a las que un usuario tiene acceso
     * 
     * @param int $userId ID del usuario
     * @return array Lista de IDs de plantas
     */
    public function getUserPlantaIds(int $userId): array
    {
        $plantas = $this->userLocationRepository->getUserPlantas($userId);
        return array_column($plantas, 'id_planta');
    }
    
    /**
     * Obtiene los IDs de botiquines a los que un usuario tiene acceso
     * 
     * @param int $userId ID del usuario
     * @return array Lista de IDs de botiquines
     */
    public function getUserBotiquinIds(int $userId): array
    {
        $botiquines = $this->userLocationRepository->getUserBotiquines($userId);
        return array_column($botiquines, 'id_botiquin');
    }
}
