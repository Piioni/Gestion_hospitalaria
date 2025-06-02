<?php

namespace model\service;

use model\entity\User;
use model\repository\UserRepository;
use model\service\UserLocationService;

class UserService
{
    private UserRepository $userRepository;
    private UserLocationService $userLocationService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userLocationService = new UserLocationService();
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
     * Actualiza la contraseña de un usuario
     * 
     * @param int $userId ID del usuario
     * @param string $newPasswordHash Hash de la nueva contraseña
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function updatePassword(int $userId, string $newPasswordHash): bool
    {
        return $this->userRepository->updatePassword($userId, $newPasswordHash);
    }

    /**
     * Añade una ubicación a un usuario
     */
    public function addUserLocation($userId, $locationId, $locationType): bool
    {
        switch ($locationType) {
            case 'hospital':
                return $this->userLocationService->assignHospital($userId, $locationId);
            case 'planta':
                return $this->userLocationService->assignPlanta($userId, $locationId);
            case 'botiquin':
                return $this->userLocationService->assignBotiquin($userId, $locationId);
            default:
                return false;
        }
    }
}
