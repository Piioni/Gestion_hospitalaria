<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\ProductoService;
use model\service\StockService;
use model\service\UserLocationService;

class StockController extends BaseController
{
    private StockService $stockService;
    private HospitalService $hospitalService;
    private PlantaService $plantaService;
    private AlmacenService $almacenService;
    private BotiquinService $botiquinService;
    private ProductoService $productoService;
    private UserLocationService $userLocationService;

    public function __construct()
    {
        $this->stockService = new StockService();
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
        $this->almacenService = new AlmacenService();
        $this->botiquinService = new BotiquinService();
        $this->productoService = new ProductoService();
        $this->userLocationService = new UserLocationService();
    }

    public function index(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'GESTOR_BOTIQUIN']);
        
        $data = [
            'title' => "Dashboard de Stock",
            'navTitle' => "Gestión de Stock",
        ];
        
        $this->render('entity.stocks.dashboard_stock', $data);
    }

    public function indexBotiquin(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'GESTOR_BOTIQUIN']);
        
        $data = $this->prepareBotiquinStockData();
        
        $this->render('entity.stocks.stock_botiquin', $data);
    }
    
    private function prepareBotiquinStockData(): array
    {
        // Obtener información del usuario actual
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        
        // Obtener filtros desde la URL
        $filtro_plantas = isset($_GET['planta']) ? (int)$_GET['planta'] : null;
        $filtro_botiquin = isset($_GET['id_botiquin']) ? (int)$_GET['id_botiquin'] : null;
        $filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : null;
        
        // Determinar qué plantas puede ver el usuario según su rol
        $plantas = $this->getPlantasForUserRole($userId, $userRole);
        
        // Filtrar los botiquines según los parámetros y permisos del usuario
        $botiquines = $this->filterBotiquinesForUser($userId, $userRole, $filtro_plantas, $filtro_botiquin, $filtro_nombre);
        
        return [
            'botiquines' => $botiquines,
            'plantas' => $plantas,
            'filtro_plantas' => $filtro_plantas,
            'filtro_botiquin' => $filtro_botiquin,
            'filtro_nombre' => $filtro_nombre,
            'userRole' => $userRole,
            'plantaService' => $this->plantaService,
            'productoService' => $this->productoService,
            'stockService' => $this->stockService,
            'title' => "Stock de Botiquines",
            'navTitle' => "Gestión de Stock en Botiquines",
            'scripts' => "toasts.js"
        ];
    }
    
    private function getPlantasForUserRole(int $userId, string $userRole): array
    {
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Administradores y gestores generales ven todas las plantas
            return $this->plantaService->getAllPlantas();
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Gestores de hospital ven plantas de sus hospitales asignados
            $hospitales = $this->userLocationService->getAssignedHospitals($userId);
            if (empty($hospitales)) {
                return [];
            }
            
            $plantas = [];
            foreach ($hospitales as $hospital) {
                $plantasDeHospital = $this->plantaService->getByHospitalId($hospital->getId());
                $plantas = array_merge($plantas, $plantasDeHospital);
            }
            return $plantas;
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Gestores de planta ven sus plantas asignadas
            return $this->userLocationService->getAssignedPlantas($userId);
        } elseif ($userRole === 'GESTOR_BOTIQUIN') {
            // Gestores de botiquín ven plantas donde tienen botiquines asignados
            $botiquines = $this->userLocationService->getAssignedBotiquines($userId);
            if (empty($botiquines)) {
                return [];
            }
            
            $plantaIds = [];
            foreach ($botiquines as $botiquin) {
                $botiquinObj = $this->botiquinService->getBotiquinById($botiquin['id_botiquin']);
                if ($botiquinObj) {
                    $plantaIds[] = $botiquinObj->getIdPlanta();
                }
            }
            
            if (empty($plantaIds)) {
                return [];
            }
            
            $plantas = [];
            foreach ($plantaIds as $plantaId) {
                $planta = $this->plantaService->getPlantaById($plantaId);
                if ($planta) {
                    $plantas[] = $planta;
                }
            }
            return $plantas;
        }
        
        return [];
    }
    
    private function filterBotiquinesForUser(int $userId, string $userRole, ?int $filtro_plantas, ?int $filtro_botiquin, ?string $filtro_nombre): array
    {
        // Si se especificó un botiquín específico
        if ($filtro_botiquin) {
            $botiquin = $this->botiquinService->getBotiquinById($filtro_botiquin);
            
            // Verificar si el usuario tiene acceso a este botiquín
            if ($botiquin && $this->userHasBotiquinAccess($userId, $userRole, $botiquin)) {
                return [$botiquin];
            }
            return [];
        }
        
        // Si se filtró por planta
        if ($filtro_plantas) {
            // Verificar si el usuario tiene acceso a esta planta
            if (!$this->userHasPlantaAccess($userId, $userRole, $filtro_plantas)) {
                return [];
            }
            
            $botiquines = $this->botiquinService->getBotiquinesByPlantaId($filtro_plantas);
        } else {
            // Sin filtro de planta, mostrar botiquines según permisos
            $botiquines = $this->getBotiquinesForUserRole($userId, $userRole);
        }
        
        // Filtrar por nombre si se especificó
        if ($filtro_nombre && !empty($botiquines)) {
            $botiquines = array_filter($botiquines, function($botiquin) use ($filtro_nombre) {
                return stripos($botiquin->getNombre(), $filtro_nombre) !== false;
            });
        }
        
        return $botiquines;
    }
    
    private function getBotiquinesForUserRole(int $userId, string $userRole): array
    {
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Administradores y gestores generales ven todos los botiquines
            return $this->botiquinService->getAllBotiquines();
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Gestores de hospital ven botiquines de sus hospitales
            $hospitales = $this->userLocationService->getAssignedHospitals($userId);
            if (empty($hospitales)) {
                return [];
            }
            
            $botiquines = [];
            foreach ($hospitales as $hospital) {
                $plantas = $this->plantaService->getByHospitalId($hospital->getId());
                foreach ($plantas as $planta) {
                    $botiquinesPlanta = $this->botiquinService->getBotiquinesByPlantaId($planta->getId());
                    $botiquines = array_merge($botiquines, $botiquinesPlanta);
                }
            }
            return $botiquines;
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Gestores de planta ven botiquines de sus plantas asignadas
            $plantas = $this->userLocationService->getAssignedPlantas($userId);
            if (empty($plantas)) {
                return [];
            }
            
            $botiquines = [];
            foreach ($plantas as $planta) {
                $botiquinesPlanta = $this->botiquinService->getBotiquinesByPlantaId($planta->getId());
                $botiquines = array_merge($botiquines, $botiquinesPlanta);
            }
            return $botiquines;
        } elseif ($userRole === 'GESTOR_BOTIQUIN') {
            // Gestores de botiquín solo ven sus botiquines asignados
            $botiquinesAsignados = $this->userLocationService->getAssignedBotiquines($userId);
            if (empty($botiquinesAsignados)) {
                return [];
            }
            
            $botiquines = [];
            foreach ($botiquinesAsignados as $botiquin) {
                $botiquinObj = $this->botiquinService->getBotiquinById($botiquin['id_botiquin']);
                if ($botiquinObj) {
                    $botiquines[] = $botiquinObj;
                }
            }
            return $botiquines;
        }
        
        return [];
    }
    
    private function userHasPlantaAccess(int $userId, string $userRole, int $plantaId): bool
    {
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return true; // Acceso total
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Verificar si la planta pertenece a alguno de sus hospitales
            $planta = $this->plantaService->getPlantaById($plantaId);
            if (!$planta) return false;
            
            $hospitalesAsignados = $this->userLocationService->getAssignedHospitals($userId);
            foreach ($hospitalesAsignados as $hospital) {
                if ($hospital->getId() === $planta->getIdHospital()) {
                    return true;
                }
            }
            return false;
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Verificar si la planta está directamente asignada
            $plantasAsignadas = $this->userLocationService->getAssignedPlantas($userId);
            foreach ($plantasAsignadas as $planta) {
                if ($planta->getId() === $plantaId) {
                    return true;
                }
            }
            return false;
        } elseif ($userRole === 'GESTOR_BOTIQUIN') {
            // Verificar si tiene algún botiquín en esta planta
            $botiquinesAsignados = $this->userLocationService->getAssignedBotiquines($userId);
            foreach ($botiquinesAsignados as $botiquin) {
                $botiquinObj = $this->botiquinService->getBotiquinById($botiquin['id_botiquin']);
                if ($botiquinObj && $botiquinObj->getIdPlanta() === $plantaId) {
                    return true;
                }
            }
            return false;
        }
        
        return false;
    }
    
    private function userHasBotiquinAccess(int $userId, string $userRole, $botiquin): bool
    {
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return true; // Acceso total
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Verificar si el botiquín está en una planta de sus hospitales
            $planta = $this->plantaService->getPlantaById($botiquin->getIdPlanta());
            if (!$planta) return false;
            
            return $this->userHasPlantaAccess($userId, $userRole, $planta->getId());
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Verificar si el botiquín está en una de sus plantas asignadas
            return $this->userHasPlantaAccess($userId, $userRole, $botiquin->getIdPlanta());
        } elseif ($userRole === 'GESTOR_BOTIQUIN') {
            // Verificar si el botiquín está directamente asignado
            $botiquinesAsignados = $this->userLocationService->getAssignedBotiquines($userId);
            foreach ($botiquinesAsignados as $botiquinAsignado) {
                if ($botiquinAsignado['id_botiquin'] == $botiquin->getId()) {
                    return true;
                }
            }
            return false;
        }
        
        return false;
    }

    public function indexAlmacen(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);
        
        $data = $this->prepareAlmacenStockData();
        
        $this->render('entity.stocks.stock_almacen', $data);
    }
    
    private function prepareAlmacenStockData(): array
    {
        // Obtener información del usuario actual
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        
        // Obtener filtros desde la URL
        $filtro_hospital = isset($_GET['hospital']) ? (int)$_GET['hospital'] : null;
        $filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
        $filtro_almacen = isset($_GET['id_almacen']) ? (int)$_GET['id_almacen'] : null;
        $filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : null;
        
        // Obtener la lista de hospitales para el filtro según permisos
        $hospitals = $this->getHospitalsForUserRole($userId, $userRole);
        
        // Filtrar los almacenes según los parámetros y permisos del usuario
        $almacenes = $this->filterAlmacenesForUser($userId, $userRole, $filtro_hospital, $filtro_tipo, $filtro_almacen, $filtro_nombre);
        
        return [
            'almacenes' => $almacenes,
            'hospitals' => $hospitals,
            'filtro_hospital' => $filtro_hospital,
            'filtro_tipo' => $filtro_tipo,
            'filtro_almacen' => $filtro_almacen,
            'filtro_nombre' => $filtro_nombre,
            'userRole' => $userRole,
            'hospitalService' => $this->hospitalService,
            'plantaService' => $this->plantaService,
            'productoService' => $this->productoService,
            'stockService' => $this->stockService,
            'title' => "Stock de Almacenes",
            'navTitle' => "Gestión de Stock en Almacenes",
            'scripts' => "toasts.js"
        ];
    }
    
    private function getHospitalsForUserRole(int $userId, string $userRole): array
    {
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Administradores y gestores generales ven todos los hospitales
            return $this->hospitalService->getAllHospitals();
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Gestores de hospital ven sus hospitales asignados
            return $this->userLocationService->getAssignedHospitals($userId);
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Gestores de planta ven hospitales donde tienen plantas asignadas
            $plantas = $this->userLocationService->getAssignedPlantas($userId);
            if (empty($plantas)) {
                return [];
            }
            
            $hospitalIds = [];
            foreach ($plantas as $planta) {
                $hospitalIds[$planta->getIdHospital()] = true;
            }
            
            $hospitales = [];
            foreach (array_keys($hospitalIds) as $hospitalId) {
                $hospital = $this->hospitalService->getHospitalById($hospitalId);
                if ($hospital) {
                    $hospitales[] = $hospital;
                }
            }
            return $hospitales;
        }
        
        return [];
    }
    
    private function filterAlmacenesForUser(int $userId, string $userRole, ?int $filtro_hospital, ?string $filtro_tipo, ?int $filtro_almacen, ?string $filtro_nombre): array
    {
        // Si se especificó un almacén específico
        if ($filtro_almacen) {
            $almacen = $this->almacenService->getAlmacenById($filtro_almacen);
            
            // Verificar si el usuario tiene acceso a este almacén
            if ($almacen && $this->userHasAlmacenAccess($userId, $userRole, $almacen)) {
                return [$almacen];
            }
            return [];
        }
        
        // Obtener almacenes según rol y filtros
        $almacenes = $this->getAlmacenesForUserRole($userId, $userRole, $filtro_hospital, $filtro_tipo);
        
        // Filtrar por nombre si se especificó
        if ($filtro_nombre && !empty($almacenes)) {
            $almacenes = array_filter($almacenes, function($almacen) use ($filtro_nombre) {
                return stripos($almacen->getNombre(), $filtro_nombre) !== false;
            });
        }
        
        return $almacenes;
    }
    
    private function getAlmacenesForUserRole(int $userId, string $userRole, ?int $filtro_hospital, ?string $filtro_tipo): array
    {
        $almacenes = [];
        
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Administradores y gestores generales pueden ver todos los almacenes
            $almacenes = $this->almacenService->getAllAlmacenes();
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Gestores de hospital ven almacenes de sus hospitales asignados
            if ($filtro_hospital) {
                // Verificar que tenga acceso a este hospital
                $hospitalesAsignados = $this->userLocationService->getAssignedHospitals($userId);
                $tieneAcceso = false;
                foreach ($hospitalesAsignados as $hospital) {
                    if ($hospital->getId() == $filtro_hospital) {
                        $tieneAcceso = true;
                        break;
                    }
                }
                
                if ($tieneAcceso) {
                    $almacenes = $this->almacenService->getAlmacenesByHospitalId($filtro_hospital);
                }
            } else {
                // Sin filtro, obtener todos los almacenes de sus hospitales
                $hospitalesAsignados = $this->userLocationService->getAssignedHospitals($userId);
                foreach ($hospitalesAsignados as $hospital) {
                    $almacenesHospital = $this->almacenService->getAlmacenesByHospitalId($hospital->getId());
                    $almacenes = array_merge($almacenes, $almacenesHospital);
                }
            }
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Gestores de planta ven almacenes de sus plantas asignadas y hospitales asociados
            if ($filtro_hospital) {
                // Verificar que tenga acceso a este hospital
                $plantas = $this->userLocationService->getAssignedPlantas($userId);
                $tieneAcceso = false;
                foreach ($plantas as $planta) {
                    if ($planta->getIdHospital() == $filtro_hospital) {
                        $tieneAcceso = true;
                        break;
                    }
                }
                
                if ($tieneAcceso) {
                    // Filtrar por tipo si es necesario
                    if ($filtro_tipo === 'PLANTA') {
                        // Solo almacenes de plantas asignadas en este hospital
                        $plantas = $this->plantaService->getByHospitalId($filtro_hospital);
                        foreach ($plantas as $planta) {
                            if ($this->userHasPlantaAccess($userId, $userRole, $planta->getId())) {
                                $almacenPlanta = $this->almacenService->getAlmacenByPlantaId($planta->getId());
                                if ($almacenPlanta) {
                                    $almacenes[] = $almacenPlanta;
                                }
                            }
                        }
                    } else {
                        // Almacenes generales del hospital (todos los que no son de planta)
                        $todosAlmacenes = $this->almacenService->getAlmacenesByHospitalId($filtro_hospital);
                        foreach ($todosAlmacenes as $almacen) {
                            if ($filtro_tipo === null || $almacen->getTipo() === $filtro_tipo) {
                                $almacenes[] = $almacen;
                            }
                        }
                    }
                }
            } else {
                // Sin filtro de hospital, obtener almacenes de plantas asignadas
                $plantas = $this->userLocationService->getAssignedPlantas($userId);
                foreach ($plantas as $planta) {
                    $almacenPlanta = $this->almacenService->getAlmacenByPlantaId($planta->getId());
                    if ($almacenPlanta && ($filtro_tipo === null || $almacenPlanta->getTipo() === $filtro_tipo)) {
                        $almacenes[] = $almacenPlanta;
                    }
                    
                    // También incluir almacenes generales del hospital si no hay filtro o es GENERAL
                    if ($filtro_tipo === null || $filtro_tipo === 'GENERAL') {
                        $almacenesGenerales = $this->almacenService->getAlmacenesGeneralesByHospitalId($planta->getIdHospital());
                        $almacenes = array_merge($almacenes, $almacenesGenerales);
                    }
                }
                
                // Eliminar duplicados
                $almacenes = array_unique($almacenes, SORT_REGULAR);
            }
        }
        
        // Aplicar filtro de tipo si existe
        if ($filtro_tipo && !empty($almacenes)) {
            $almacenes = array_filter($almacenes, function($almacen) use ($filtro_tipo) {
                return $almacen->getTipo() === $filtro_tipo;
            });
        }
        
        return $almacenes;
    }
    
    private function userHasAlmacenAccess(int $userId, string $userRole, $almacen): bool
    {
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return true; // Acceso total
        } elseif ($userRole === 'GESTOR_HOSPITAL') {
            // Verificar si el almacén pertenece a alguno de sus hospitales
            $hospitalesAsignados = $this->userLocationService->getAssignedHospitals($userId);
            foreach ($hospitalesAsignados as $hospital) {
                if ($hospital->getId() === $almacen->getIdHospital()) {
                    return true;
                }
            }
            return false;
        } elseif ($userRole === 'GESTOR_PLANTA') {
            // Si es almacén de planta, verificar si la planta está asignada
            if ($almacen->getIdPlanta()) {
                $plantasAsignadas = $this->userLocationService->getAssignedPlantas($userId);
                foreach ($plantasAsignadas as $planta) {
                    if ($planta->getId() === $almacen->getIdPlanta()) {
                        return true;
                    }
                }
                return false;
            }
            
            // Si es almacén general, verificar si el hospital está relacionado con alguna planta asignada
            $plantasAsignadas = $this->userLocationService->getAssignedPlantas($userId);
            foreach ($plantasAsignadas as $planta) {
                if ($planta->getIdHospital() === $almacen->getIdHospital()) {
                    return true;
                }
            }
            return false;
        }
        
        return false;
    }

    public function create(): void
    {
        // Implementación de la creación de stock
    }
}
