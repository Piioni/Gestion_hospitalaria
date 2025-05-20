<?php

namespace model\service;

use model\repository\UserRepository;

class UserService
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function addUser($name, $email, $password, $rol, $hospitalId = null, $plantaId = null, $botiquinId = null): bool
    {
        return $this->userRepository->insertUser($name, $email, $password, $rol, $hospitalId, $plantaId, $botiquinId);
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }
}
