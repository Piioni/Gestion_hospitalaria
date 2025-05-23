<?php

namespace model\service;

use model\entity\User;
use model\repository\UserRepository;
use model\repository\UserLocationRepository;

class UserService
{
    private UserRepository $userRepository;
    private UserLocationRepository $userLocationRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userLocationRepository = new UserLocationRepository();
    }

    public function addUser($nombre, $email, $password, $rol, $id_hospital = null, $id_planta = null, $id_botiquin = null): bool
    {
        return $this->userRepository->insertUser($nombre, $email, $password, $rol, $id_hospital, $id_planta, $id_botiquin);
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAllUsers();
    }

    public function getUserById($id): ?User
    {
        return $this->userRepository->getUserById($id);
    }

    public function getUserByEmail($email): ?User
    {
        return $this->userRepository->getUserByEmail($email);
    }
    
    /**
     * Guarda las ubicaciones asignadas a un usuario
     * Implementa la regla de que solo se puede asignar un tipo de ubicación por usuario
     */
    public function saveUserLocations($userId, $hospitales, $plantas, $botiquines): bool
    {
        // Verificar que solo hay un tipo de ubicación asignado
        $hasHospitales = !empty($hospitales);
        $hasPlantas = !empty($plantas);
        $hasBotiquines = !empty($botiquines);
        
        $selectedTypes = ($hasHospitales ? 1 : 0) + ($hasPlantas ? 1 : 0) + ($hasBotiquines ? 1 : 0);
        
        if ($selectedTypes > 1) {
            throw new \Exception("Solo puede asignar un tipo de ubicación: hospitales, plantas o botiquines.");
        }
        
        // Eliminar todas las ubicaciones actuales
        $this->userLocationRepository->deleteAllUserLocations($userId);
        
        // Asignar nuevas ubicaciones
        if ($hasHospitales) {
            foreach ($hospitales as $hospitalId) {
                $this->userLocationRepository->addUserHospital($userId, $hospitalId);
            }
        }
        
        if ($hasPlantas) {
            foreach ($plantas as $plantaId) {
                $this->userLocationRepository->addUserPlanta($userId, $plantaId);
            }
        }
        
        if ($hasBotiquines) {
            foreach ($botiquines as $botiquinId) {
                $this->userLocationRepository->addUserBotiquin($userId, $botiquinId);
            }
        }
        
        return true;
    }
}
