<?php

namespace controllers;

use Exception;
use InvalidArgumentException;
use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\StockService;

class PlantaController extends BaseController
{
    private PlantaService $plantaService;
    private StockService $stockService;
    private HospitalService $hospitalService;
    private AlmacenService $almacenService;
    private BotiquinService $botiquinService;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
        $this->stockService = new StockService();
        $this->hospitalService = new HospitalService();
        $this->almacenService = new AlmacenService();
        $this->botiquinService = new BotiquinService();
    }

    public function index(): void
    {
        // Verificar permisos de acceso
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);
        
        // Obtener parámetros de filtro
        $filtroHospital = $_GET['hospital'] ?? null;
        $filtroNombre = $_GET['nombre'] ?? null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtroNombre || $filtroHospital;
        
        // Obtener información del usuario actual para filtrado
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        
        // Obtener plantas filtradas según permisos y filtros aplicados
        $plantas = $this->plantaService->getPlantasForUser(
            $userId, 
            $userRole, 
            $filtroHospital, 
            $filtroNombre
        );
        
        // Obtener hospitales para el selector de filtro (solo los que el usuario puede ver)
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        
        // Verificar si es admin o gestor general para determinar permisos
        $canCreateDelete = in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL']);
        
        // Datos para la vista
        $data = [
            'plantas' => $plantas,
            'hospitals' => $hospitals,
            'filtroHospital' => $filtroHospital,
            'filtroNombre' => $filtroNombre,
            'filtrarActivo' => $filtrarActivo,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'hospitalService' => $this->hospitalService,
            'plantaService' => $this->plantaService,
            'botiquinService' => $this->botiquinService,
            'almacenService' => $this->almacenService,
            'stockService' => $this->stockService,
            'canCreateDelete' => $canCreateDelete,
            'scripts' => 'toasts.js',
            'title' => 'Sistema de Gestión Hospitalaria',
            'navTitle' => 'Gestión de Plantas'
        ];
        
        $this->render('entity.plantas.dashboard_planta', $data);
    }
    
    public function create(): void
    {
        // Solo admins y gestores generales pueden crear plantas
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);
        
        // Obtener información del usuario para mostrar solo hospitales permitidos
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        
        $planta = [
            'id_hospital' => $_GET['id_hospital'] ?? '',
            'nombre' => '',
        ];
        $errors = [];
        
        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar datos de entrada
            $planta['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_NUMBER_INT);
            $planta['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
            
            try {
                // Intentar crear la planta
                $this->plantaService->createPlanta($planta['id_hospital'], $planta['nombre']);
                
                // Redirigir con mensaje de éxito
                $this->redirect('plantas', ['success' => 'created']);
                
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            } catch (Exception $e) {
                $errors[] = "Error al crear la planta: " . $e->getMessage();
            }
        } elseif (isset($_GET['id_hospital'])) {
            // Si se pasa un ID de hospital, verificar acceso
            $hospital_id = filter_input(INPUT_GET, 'id_hospital', FILTER_SANITIZE_NUMBER_INT);
            if ($hospital_id) {
                try {
                    if (!$this->isAdminOrGestor($userRole)) {
                        AuthMiddleware::requireHospitalAccess((int)$hospital_id);
                    }
                    
                    $hospital = $this->hospitalService->getHospitalById($hospital_id);
                    if ($hospital) {
                        $planta['id_hospital'] = $hospital->getId();
                    }
                } catch (Exception $e) {
                    $this->redirect('plantas', ['error' => 'hospital_no_encontrado']);
                }
            }
        }
        
        $data = [
            'planta' => $planta,
            'errors' => $errors,
            'hospitals' => $hospitals,
            'title' => 'Crear Planta',
            'scripts' => 'toasts.js'
        ];
        
        $this->render('entity.plantas.create_planta', $data);
    }
    
    public function edit(): void
    {
        $plantaId = $_GET['id_planta'] ?? null;

        if (!$plantaId || !is_numeric($plantaId)) {
            $this->redirect('plantas', ['error' => 'id_invalid']);
        }

        // Verificar acceso a la planta
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        if (!$this->plantaService->userHasAccessToPlanta($userId, $userRole, (int)$plantaId)) {
            http_response_code(403);
            header('Location: /403');
            exit;
        }
        
        // Obtener datos de la planta
        $plantaObj = $this->plantaService->getPlantaById((int)$plantaId);
        if (!$plantaObj) {
            $this->redirect('plantas', ['error' => 'id_not_found']);
        }
        
        // Obtener hospitales permitidos según el rol
        $hospitals = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        
        // Preparar datos para la vista
        $planta = [
            'id' => $plantaObj->getId(),
            'id_hospital' => $plantaObj->getIdHospital(),
            'nombre' => $plantaObj->getNombre(),
        ];
        $errors = [];
        $canCreateDelete = in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar datos de entrada
            $planta['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_NUMBER_INT);
            $planta['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
            
            try {
                // Verificar acceso al nuevo hospital seleccionado si es distinto
                if ($planta['id_hospital'] != $plantaObj->getIdHospital() && !$this->isAdminOrGestor($userRole)) {
                    AuthMiddleware::requireHospitalAccess((int)$planta['id_hospital']);
                }
                
                // Actualizar la planta
                $success = $this->plantaService->updatePlanta($planta['id'], $planta['id_hospital'], $planta['nombre']);
                
                if ($success) {
                    $this->redirect('plantas', ['success' => 'updated']);
                }
                
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            } catch (Exception $e) {
                $errors[] = "Error al actualizar la planta: " . $e->getMessage();
            }
        }
        
        $data = [
            'planta' => $planta,
            'hospitals' => $hospitals,
            'errors' => $errors,
            'canCreateDelete' => $canCreateDelete,
            'scripts' => 'toasts.js',
            'title' => 'Editar Planta'
        ];
        
        $this->render('entity.plantas.edit_planta', $data);
    }
    
    public function delete(): void
    {
        $plantaId = $_GET['id_planta'] ?? null;

        if (!$plantaId || !is_numeric($plantaId)) {
            $this->redirect('plantas', ['error' => 'id_invalid']);
        }

        // Solo admin y gestores generales pueden eliminar
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);
        
        try {
            // Obtener datos de la planta
            $planta = $this->plantaService->getPlantaById((int)$plantaId);
            if (!$planta) {
                $this->redirect('plantas', ['error' => 'id_not_found']);
            }
            
            // Obtener hospital relacionado
            $hospital = $this->hospitalService->getHospitalById($planta->getIdHospital());
            
            // Verificar dependencias
            $almacen = $this->almacenService->getAlmacenByPlantaId((int)$plantaId);
            $botiquines = $this->botiquinService->getBotiquinesByPlantaId((int)$plantaId);
            $hasAlmacen = $almacen !== null;
            $error = null;
            
            // Si se confirma la eliminación
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
                if ($hasAlmacen) {
                    throw new InvalidArgumentException("No se puede eliminar la planta porque tiene un almacén asociado.");
                }

                if (!empty($botiquines)) {
                    throw new InvalidArgumentException("No se puede eliminar la planta porque tiene botiquines asociados.");
                }
                
                $success = $this->plantaService->deletePlanta((int)$plantaId);
                
                if ($success) {
                    $this->redirect('plantas', ['success' => 'deleted']);
                } else {
                    throw new Exception("No se pudo eliminar la planta.");
                }
            }
            
            $data = [
                'planta' => $planta,
                'hospital' => $hospital,
                'almacen' => $almacen,
                'hasAlmacen' => $hasAlmacen,
                'botiquines' => $botiquines,
                'error' => $error,
                'scripts' => 'toasts.js',
                'title' => 'Confirmar Eliminación'
            ];
            
            $this->render('entity.plantas.delete_planta', $data);
            
        } catch (InvalidArgumentException $e) {
            $this->redirect('plantas', ['error' => urlencode($e->getMessage())]);
        } catch (Exception $e) {
            $this->redirect('plantas', ['error' => 'unexpected']);
        }
    }

    private function isAdminOrGestor(string $userRole): bool
    {
        return in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL']);
    }
}
