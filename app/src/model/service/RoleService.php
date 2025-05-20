<?php

namespace model\service;

use model\repository\RoleRepository;

class RoleService
{
    private RoleRepository $roleRepository;
    
    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
    }
    
    public function getAllRoles(): array
    {
        return $this->roleRepository->getAll();
    }
    
    public function getRoleById($id): array
    {
        return $this->roleRepository->getRoleById($id);
    }
}
