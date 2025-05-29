<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;

class AlmacenController extends BaseController
{
    private AlmacenService $almacenService;
    private PlantaService $plantaService;
    private HospitalService $hospitalService;

    public function __construct()
    {
        $this->almacenService = new AlmacenService();
        $this->plantaService = new PlantaService();
        $this->hospitalService = new HospitalService();
    }

    public function index()
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        // Obtener parámetros de filtro
        $filtroHospital = $_GET['hospital'] ?? null;
        $filtroTipo = $_GET['tipo'] ?? null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtroTipo || $filtroHospital;

        // Obtener información del usuario actual para filtrado
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Obtener almacenes filtrados según permisos y filtros aplicados
        $almacenes = $this->almacenService->getAlmacenesForUser(
            $userId,
            $userRole,
            $filtroHospital,
            $filtroTipo
        );

    }

}