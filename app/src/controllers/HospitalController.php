<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;

class HospitalController extends BaseController
{
    private HospitalService $hospitalService;
    private PlantaService $plantaService;
    private AlmacenService $almacenService;

    public function __construct()
    {
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
        $this->almacenService = new AlmacenService();
    }

    public function index(): void
    {
        // Verificar permisos - solo admins, gestores generales y gestores de hospital
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);

        // Obtener hospitales según el rol del usuario
        $hospitals = $this->getHospitalsForCurrentUser();

        // Obtener parámetros de filtro
        $filtroNombre = $_GET['nombre'] ?? null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtroNombre;

        // Aplicar filtros si existen
        if ($filtroNombre) {
            $hospitals = array_filter($hospitals, function ($hospital) use ($filtroNombre) {
                return stripos($hospital->getNombre(), $filtroNombre) !== false;
            });
        }

        // Datos para la vista
        $data = [
            'hospitals' => $hospitals,
            'filtroNombre' => $filtroNombre,
            'filtrarActivo' => $filtrarActivo,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'plantaService' => $this->plantaService,
            'almacenService' => $this->almacenService,
            'scripts' => 'toasts.js',
            'title' => 'Sistema de Gestión Hospitalaria',
            'navTitle' => 'Gestión de Hospitales'
        ];

        $this->render('entity/ubicaciones/hospitals/dashboard_hospital.php', $data);
    }

    public function create(): void
    {
        // Solo admins y gestores generales pueden crear hospitales
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            $this->render('entity/ubicaciones/hospitals/create_hospital.php');
        }
    }

    public function edit(): void
    {
        $hospitalId = $_GET['id_hospital'] ?? null;

        if (!$hospitalId || !is_numeric($hospitalId)) {
            $this->redirect('/hospitals', ['error' => 'id_invalid']);
            return;
        }

        // Verificar acceso específico al hospital
        AuthMiddleware::requireHospitalAccess((int)$hospitalId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit((int)$hospitalId);
        } else {
            $hospital = $this->hospitalService->getHospitalById((int)$hospitalId);
            $this->render('entity/ubicaciones/hospitals/edit_hospital.php', ['hospital' => $hospital]);
        }
    }

    public function delete(): void
    {
        $hospitalId = $_GET['id_hospital'] ?? null;

        if (!$hospitalId || !is_numeric($hospitalId)) {
            $this->redirect('/hospitals', ['error' => 'id_invalid']);
            return;
        }

        // Solo admins y gestores generales pueden eliminar
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleDelete((int)$hospitalId);
        } else {
            $hospital = $this->hospitalService->getHospitalById((int)$hospitalId);
            $this->render('entity/ubicaciones/hospitals/delete_hospital.php', ['hospital' => $hospital]);
        }
    }

    private function getHospitalsForCurrentUser(): array
    {
        $userRole = $this->getCurrentUserRole();
        $userId = $this->getCurrentUserId();

        switch ($userRole) {
            case 'ADMINISTRADOR':
            case 'GESTOR_GENERAL':
                return $this->hospitalService->getAllHospitals();

            case 'GESTOR_HOSPITAL':
                // Solo hospitales asignados al usuario
                return $this->hospitalService->getHospitalsByUserId($userId);

            default:
                return [];
        }
    }

    private function handleCreate(): void
    {
        try {
            $nombre = $_POST['nombre'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';

            if (empty($nombre) || empty($ubicacion)) {
                throw new Exception('Todos los campos son obligatorios');
            }

            $this->hospitalService->createHospital($nombre, $ubicacion);
            $this->redirect('/hospitals', ['success' => 'created']);

        } catch (Exception $e) {
            $this->redirect('/hospitals/create', ['error' => urlencode($e->getMessage())]);
        }
    }

    private function handleEdit(int $hospitalId): void
    {
        try {
            $nombre = $_POST['nombre'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';

            if (empty($nombre) || empty($ubicacion)) {
                throw new Exception('Todos los campos son obligatorios');
            }

            $this->hospitalService->updateHospital($hospitalId, $nombre, $ubicacion);
            $this->redirect('/hospitals', ['success' => 'updated']);

        } catch (Exception $e) {
            $this->redirect('/hospitals/edit', [
                'id_hospital' => $hospitalId,
                'error' => urlencode($e->getMessage())
            ]);
        }
    }

    private function handleDelete(int $hospitalId): void
    {
        try {
            $this->hospitalService->deleteHospital($hospitalId);
            $this->redirect('/hospitals', ['success' => 'deleted']);

        } catch (Exception $e) {
            $this->redirect('/hospitals', ['error' => urlencode($e->getMessage())]);
        }
    }
}