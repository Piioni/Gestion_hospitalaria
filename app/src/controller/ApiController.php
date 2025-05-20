<?php

namespace controller;

use model\service\PlantaService;
use model\service\BotiquinService;
use model\service\RoleService;

class ApiController
{
    private PlantaService $plantaService;
    private BotiquinService $botiquinService;
    private RoleService $roleService;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
        $this->botiquinService = new BotiquinService();
        $this->roleService = new RoleService();
    }

    public function getPlantasByHospital()
    {
        header('Content-Type: application/json');
        
        if (!isset($_GET['hospital_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Hospital ID is required']);
            return;
        }
        
        $hospitalId = $_GET['hospital_id'];
        $plantas = $this->plantaService->getPlantasByHospitalId($hospitalId);
        
        echo json_encode($plantas);
    }

    public function getBotiquinesByPlanta()
    {
        header('Content-Type: application/json');
        
        if (!isset($_GET['planta_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Planta ID is required']);
            return;
        }
        
        $plantaId = $_GET['planta_id'];
        $botiquines = $this->botiquinService->getBotiquinesByPlantaId($plantaId);
        
        echo json_encode($botiquines);
    }
    
    public function getAllRoles() 
    {
        header('Content-Type: application/json');
        $roles = $this->roleService->getAllRoles();
        echo json_encode($roles);
    }
}
