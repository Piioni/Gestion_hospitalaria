<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\PlantaService;

class PlantaController
{
    private PlantaService $plantaService;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
    }

    public function index(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        $plantas = $this->getPlantasForCurrentUser();

        $this->render('entity/ubicaciones/plantas/dashboard_planta.php', [
            'plantas' => $plantas
        ]);
    }

    public function edit(): void
    {
        $plantaId = $_GET['id_planta'] ?? null;

        if (!$plantaId || !is_numeric($plantaId)) {
            $this->redirect('/plantas', ['error' => 'id_invalid']);
            return;
        }

        // Verificar acceso específico a la planta
        AuthMiddleware::requirePlantaAccess((int)$plantaId);

        // Resto de la lógica...
    }

    private function getPlantasForCurrentUser(): array
    {
        $userRole = $this->getCurrentUserRole();
        $userId = $this->getCurrentUserId();

        switch ($userRole) {
            case 'ADMINISTRADOR':
            case 'GESTOR_GENERAL':
                return $this->plantaService->getAllPlantas();

            case 'GESTOR_HOSPITAL':
                // Solo plantas de hospitales asignados
                return $this->plantaService->getPlantasByUserId($userId);

            case 'GESTOR_PLANTA':
                // Solo plantas asignadas directamente
                return $this->plantaService->getPlantasByUserId($userId);

            default:
                return [];
        }
    }
}