<?php

namespace model\service;

use model\entity\User;
use model\repository\UserRepository;

class UserService
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function addUser($nombre, $email, $password, $rol, $id_hospital = null, $id_planta = null, $id_botiquin = null): bool
    {
        return $this->userRepository->insertUser($nombre, $email, $password, $rol, $id_hospital, $id_planta, $id_botiquin);
    }

    public function getUserByEmail($email): ?User
    {
        return $this->userRepository->getUserByEmail($email);
    }
}
