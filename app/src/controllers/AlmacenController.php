<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\StockService;
use Exception;
use InvalidArgumentException;

class AlmacenController extends BaseController
{
    private AlmacenService $almacenService;
    private PlantaService $plantaService;
    private HospitalService $hospitalService;
    private StockService $stockService;

    public function __construct()
    {
        $this->almacenService = new AlmacenService();
        $this->plantaService = new PlantaService();
        $this->hospitalService = new HospitalService();
        $this->stockService = new StockService();
    }

    public function index(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        // Obtener parámetros de filtro
        $filtroHospital = $_GET['hospital'] ?? null;
        $filtroTipo = $_GET['tipo'] ?? null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtroTipo || $filtroHospital;
        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

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

        // Obtener hospitales
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        
        $canCreateDelete = in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);

        $data = [
            'almacenes' => $almacenes,
            'hospitals' => $hospitals,
            'filtroHospital' => $filtroHospital,
            'filtroTipo' => $filtroTipo,
            'filtrarActivo' => $filtrarActivo,
            'userRole' => $userRole,
            'userId' => $userId,
            'plantaService' => $this->plantaService,
            'almacenService' => $this->almacenService,
            'hospitalService' => $this->hospitalService,
            'title' => 'Almacenes',
            'scripts' => ['toasts.js', 'almacenes.js',],
            'navTitle' => 'Almacenes',
            'success' => $success,
            'error' => $error,
            'canCreateDelete' => $canCreateDelete,
        ];

        $this->render('entity.almacenes.dashboard_almacen', $data);
    }
    
    public function create(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);
        
        // Inicializar variables
        $almacen = [
            'nombre' => $_GET['nombre'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'id_hospital' => $_GET['id_hospital'] ?? '',
            'id_planta' => $_GET['id_planta'] ?? '',
        ];
        
        $errors = [];
        $success = false;
        
        // Obtener la lista de hospitales y plantas para el formulario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        $plantas = $this->plantaService->getAllPlantas();
        
        // Procesar el formulario si es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($almacen, $errors, $success);
        }
        
        $data = [
            'almacen' => $almacen,
            'errors' => $errors,
            'success' => $success,
            'hospitals' => $hospitals,
            'plantas' => $plantas,
            'title' => 'Crear Almacén',
            'scripts' => ['almacenes.js', 'toasts.js', 'hospital_planta_botiquin.js'],
        ];
        
        $this->render('entity.almacenes.create_almacen', $data);
    }
    
    private function handleCreate(array &$almacen, array &$errors, bool &$success): void
    {
        // Sanitizar datos de entrada
        $almacen['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $almacen['tipo'] = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
        $almacen['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_SPECIAL_CHARS);
        $almacen['id_planta'] = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            // Intentar crear el almacen
            $success = $this->almacenService->createAlmacen(
                $almacen['nombre'],
                $almacen['tipo'],
                $almacen['id_hospital'],
                $almacen['id_planta']
            );

            // Redirigir tras crear con éxito
            if ($success) {
                header("Location: " . url('almacenes', ['success' => 'created']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "Error al crear el almacén: " . $e->getMessage();
        }
    }
    
    public function edit(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);
        
        $id_almacen = $_GET['id_almacen'] ?? null;
        
        if (!$this->validateAlmacenId($id_almacen)) {
            header("Location: " . url('almacenes', ['error' => 'no_id']));
            exit;
        }
        
        $errors = [];
        $success = false;
        $almacen = [];
        
        // Obtener listas para el formulario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        $plantas = $this->plantaService->getAllArray();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar formulario de edición
            $this->handleEdit($almacen, $errors, $success);
        } else {
            // Cargar datos del almacén existente
            $this->loadAlmacenData($id_almacen, $almacen, $errors);
        }
        
        $data = [
            'almacen' => $almacen,
            'errors' => $errors,
            'success' => $success,
            'hospitals' => $hospitals,
            'plantas' => $plantas,
            'title' => 'Editar Almacén',
            'scripts' => ['almacenes.js', 'toasts.js']
        ];
        
        $this->render('entity.almacenes.edit_almacen', $data);
    }
    
    private function validateAlmacenId($id): bool
    {
        return !empty($id) && is_numeric($id);
    }
    
    private function loadAlmacenData($id_almacen, array &$almacen, array &$errors): void
    {
        try {
            $almacenObj = $this->almacenService->getAlmacenById($id_almacen);
            
            if (!$almacenObj) {
                header("Location: " . url('almacenes', ['error' => 'not_found']));
                exit;
            }
            
            $almacen['id'] = $almacenObj->getId();
            $almacen['nombre'] = $almacenObj->getNombre();
            $almacen['tipo'] = $almacenObj->getTipo();
            $almacen['id_hospital'] = $almacenObj->getIdHospital();
            $almacen['id_planta'] = $almacenObj->getIdPlanta();
        } catch (Exception $e) {
            $errors[] = "Error al cargar el almacén: " . $e->getMessage();
        }
    }
    
    private function handleEdit(array &$almacen, array &$errors, bool &$success): void
    {
        // Procesar formulario de edición
        $almacen['id'] = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $almacen['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $almacen['tipo'] = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
        $almacen['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_SPECIAL_CHARS);
        $almacen['id_planta'] = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            $success = $this->almacenService->updateAlmacen(
                $almacen['id'],
                $almacen['nombre'],
                $almacen['tipo'],
                $almacen['id_hospital'],
                $almacen['id_planta']
            );

            if ($success) {
                header("Location: " . url('almacenes', ['success' => 'updated']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "Error al actualizar el almacén: " . $e->getMessage();
        }
    }
    
    public function delete(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);
        
        $id_almacen = $_GET['id_almacen'] ?? null;
        $confirm = isset($_GET['confirm']);
        
        if (!$this->validateAlmacenId($id_almacen)) {
            header("Location: " . url('almacenes', ['error' => 'id_invalid']));
            exit;
        }
        
        try {
            // Obtener datos del almacén
            $data = $this->prepareDeleteData($id_almacen);
            
            // Si se solicita confirmar la eliminación
            if ($confirm) {
                $this->handleDelete($id_almacen, $data);
            }
            
            $this->render('entity.almacenes.delete_almacen', $data);
            
        } catch (Exception $e) {
            header("Location: " . url('almacenes', ['error' => 'unexpected']));
            exit;
        }
    }
    
    private function prepareDeleteData($id_almacen): array
    {
        //TODO: Validar que el almacen no tenga stock asociado
        $almacen = $this->almacenService->getAlmacenById($id_almacen);
        
        if (!$almacen) {
            header("Location: " . url('almacenes', ['error' => 'not_found']));
            exit;
        }
        
        $hospital = $this->hospitalService->getHospitalById($almacen->getIdHospital());
        
        $planta = null;
        if ($almacen->getIdPlanta()) {
            $planta = $this->plantaService->getPlantaById($almacen->getIdPlanta());
        }
        
        // Verificar stock asociado
        $tieneStock = $this->stockService->almacenHasStock($id_almacen);
        
        return [
            'almacen' => $almacen,
            'hospital' => $hospital,
            'planta' => $planta,
            'tieneStock' => $tieneStock,
            'error' => null,
            'title' => 'Confirmar Eliminación de Almacén',
            'scripts' => 'toasts.js'
        ];
    }
    
    private function handleDelete($id_almacen, array &$data): void
    {
        // Verificar si tiene stock antes de eliminar
        if ($data['tieneStock']) {
            $data['error'] = "No se puede eliminar un almacén con productos en stock";
            return;
        }
        
        $result = $this->almacenService->deleteAlmacen($id_almacen);
        if ($result) {
            header("Location: " . url('almacenes', ['success' => 'deleted']));
            exit;
        } else {
            $data['error'] = "No se pudo eliminar el almacén";
        }
    }
}
