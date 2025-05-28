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
     * Obtiene los hospitales asignados a un usuario
     */
    public function getUserHospitals($userId): array
    {
        return $this->userLocationRepository->getUserHospitales($userId);
    }
    
    /**
     * Obtiene las plantas asignadas a un usuario
     */
    public function getUserPlantas($userId): array
    {
        return $this->userLocationRepository->getUserPlantas($userId);
    }
    
    /**
     * Obtiene los botiquines asignados a un usuario
     */
    public function getUserBotiquines($userId): array
    {
        return $this->userLocationRepository->getUserBotiquines($userId);
    }
    
    /**
     * Verifica si el usuario tiene alguna ubicación asignada
     */
    public function hasLocations($userId): bool
    {
        $hospitales = $this->getUserHospitals($userId);
        $plantas = $this->getUserPlantas($userId);
        $botiquines = $this->getUserBotiquines($userId);
        
        return !empty($hospitales) || !empty($plantas) || !empty($botiquines);
    }

    public function addUserLocation($userId, $locationId, $locationType): bool
    {
        return $this->userLocationRepository->addUserLocation($userId, $locationId, $locationType);
    }

}
