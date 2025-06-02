<?php

namespace controllers;

use Exception;
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

        // Obtener parámetros de filtro
        $filtroNombre = $_GET['nombre'] ?? null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtroNombre;

        // Obtener hospitales filtrados por nombre y permisos de usuario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole, $filtroNombre);

        // Verificar si es admin o gestor general para determinar permisos
        $canCreateDelete = in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL']);

        // Datos para la vista
        $data = [
            'hospitals' => $hospitals,
            'filtroNombre' => $filtroNombre,
            'filtrarActivo' => $filtrarActivo,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'plantaService' => $this->plantaService,
            'almacenService' => $this->almacenService,
            'canCreateDelete' => $canCreateDelete,
            'scripts' => 'toasts.js',
            'title' => 'Hospitales',
            'navTitle' => 'Pegasus Medical'
        ];

        // Usando la notación de punto para referenciar la vista
        $this->render('entity.hospitals.dashboard_hospital', $data);
    }

    public function create(): void
    {
        // Solo admins y gestores generales pueden crear hospitales
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            // Usando la notación de punto para referenciar la vista
            $this->render('entity.hospitals.create_hospital');
        }
    }

    public function edit(): void
    {
        $hospitalId = $_GET['id_hospital'] ?? null;

        if (!$hospitalId || !is_numeric($hospitalId)) {
            $this->redirect('/hospitals', ['error' => 'id_invalid']);
        }

        // Verificar acceso específico al hospital
        AuthMiddleware::requireHospitalAccess((int)$hospitalId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit((int)$hospitalId);
        } else {
            $hospital = $this->hospitalService->getHospitalById((int)$hospitalId);
            // Usando la notación de punto para referenciar la vista
            $this->render('entity.hospitals.edit_hospital', ['hospital' => $hospital]);
        }
    }

    public function delete(): void
    {
        //TODO: Implementar el mostrar plantas relacionadas al hospital

        $hospitalId = $_GET['id_hospital'] ?? null;

        if (!$hospitalId || !is_numeric($hospitalId)) {
            $this->redirect('/hospitals', ['error' => 'id_invalid']);
        }

        // Solo admins y gestores generales pueden eliminar
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleDelete((int)$hospitalId);
        } else {
            $hospital = $this->hospitalService->getHospitalById((int)$hospitalId);
            // Usando la notación de punto para referenciar la vista
            $this->render('entity.hospitals.delete_hospital', ['hospital' => $hospital]);
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
