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
            'scripts' => ['toasts.js', 'almacenes.js'],
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
        $plantas = $this->plantaService->getAllArray();
        
        // Procesar el formulario si es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    header("Location: " . url('almacenes.dashboard', ['success' => 'created']));
                    exit;
                }
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            } catch (Exception $e) {
                $errors[] = "Error al crear el almacén: " . $e->getMessage();
            }
        }
        
        $data = [
            'almacen' => $almacen,
            'errors' => $errors,
            'success' => $success,
            'hospitals' => $hospitals,
            'plantas' => $plantas,
            'title' => 'Crear Almacén',
            'scripts' => ['almacenes.js', 'toasts.js']
        ];
        
        $this->render('entity.almacenes.create_almacen', $data);
    }
    
    public function edit(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);
        
        $id_almacen = $_GET['id_almacen'] ?? null;
        
        if (!$id_almacen || !is_numeric($id_almacen)) {
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
        } else {
            // Cargar datos del almacén existente
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
    
    public function delete(): void
    {
        // TODO: Implementar la verificación de stock asociado antes de eliminar un almacén
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);
        
        $id_almacen = $_GET['id_almacen'] ?? null;
        $confirm = isset($_GET['confirm']);
        
        if (!$id_almacen || !is_numeric($id_almacen)) {
            header("Location: " . url('almacenes', ['error' => 'id_invalid']));
            exit;
        }
        
        // Inicializar variables
        $error = null;
        
        try {
            // Obtener datos del almacén
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
            $tieneStock = false; // Implementar después la verificación real
            
            // Si se solicita confirmar la eliminación
            if ($confirm) {
                $result = $this->almacenService->deleteAlmacen($id_almacen);
                if ($result) {
                    header("Location: " . url('almacenes', ['success' => 'deleted']));
                    exit;
                } else {
                    $error = "No se pudo eliminar el almacén";
                }
            }
            
            $data = [
                'almacen' => $almacen,
                'hospital' => $hospital,
                'planta' => $planta,
                'tieneStock' => $tieneStock,
                'error' => $error,
                'title' => 'Confirmar Eliminación de Almacén',
                'scripts' => 'toasts.js'
            ];
            
            $this->render('entity.almacenes.delete_almacen', $data);
            
        } catch (Exception $e) {
            header("Location: " . url('almacenes', ['error' => 'unexpected']));
            exit;
        }
    }
}
