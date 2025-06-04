<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\entity\Movimiento;
use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\MovimientoService;
use model\service\PlantaService;
use model\service\ProductoService;

class MovimientoController extends BaseController
{
    private MovimientoService $movimientoService;
    private AlmacenService $almacenService;
    private ProductoService $productoService;
    private HospitalService $hospitalService;
    private PlantaService $plantaService;

    public function __construct()
    {
        $this->movimientoService = new MovimientoService();
        $this->almacenService = new AlmacenService();
        $this->productoService = new ProductoService();
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
    }

    public function index(): void
    {
        // Verificar si el usuario tiene permisos para ver movimientos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        // Obtener información del usuario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Obtener movimientos pendientes según el rol y permisos
        $pendientes = [];

        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Admin y gestor general ven todos los movimientos pendientes
            $pendientes = $this->movimientoService->find(['estado' => 'PENDIENTE']);
        } else {
            // Gestor de hospital y gestor de planta ven solo movimientos asociados a sus almacenes
            $almacenes = $this->almacenService->getAlmacenesForUser($userId, $userRole);
            $almacenIds = $this->movimientoService->extractAlmacenIds($almacenes);

            if (!empty($almacenIds)) {
                $pendientes = $this->movimientoService->find(['estado' => 'PENDIENTE'], $almacenIds);
            }
        }

        $success = $_GET['success'] ?? null;
        $errors = [];

        $data = [
            'pendientes' => $pendientes,
            'movimientoService' => $this->movimientoService,
            'almacenService' => $this->almacenService,
            'productoService' => $this->productoService,
            'title' => 'Movimientos',
            'success' => $success,
            'errors' => $errors,
        ];

        $this->render('entity.movimientos.dashboard_movimiento', $data);
    }

    public function list(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        // Obtener filtros de la solicitud
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'tipo_movimiento' => $_GET['tipo_movimiento'] ?? '',
            'producto' => $_GET['producto'] ?? '',
            'destino' => $_GET['destino'] ?? '',
            'orden' => $_GET['orden'] ?? 'fecha_desc',
        ];

        // Obtener información del usuario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Obtener movimientos según el rol y permisos
        $movimientos = [];

        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Admin y gestor general ven todos los movimientos
            $movimientos = $this->movimientoService->find($filtros);
        } else {
            // Recuperar almacenes de usuario
            $almacenes = $this->almacenService->getAlmacenesForUser($userId, $userRole);
            $almacenIds = $this->movimientoService->extractAlmacenIds($almacenes);

            if (!empty($almacenIds)) {
                $movimientos = $this->movimientoService->find($filtros, $almacenIds);
            }
        }

        // Paginación y renderizado (ajusta según tu lógica actual)
        $total = count($movimientos);
        $pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $por_pagina = 20;
        $paginas = max(1, ceil($total / $por_pagina));
        $movimientos_pagina = array_slice($movimientos, ($pagina_actual - 1) * $por_pagina, $por_pagina);

        // Obtener productos para el filtro
        $productos = $this->productoService->getAllProducts();

        $data = [
            'movimientos' => $movimientos_pagina,
            'total' => $total,
            'pagina_actual' => $pagina_actual,
            'paginas' => $paginas,
            'filtros' => $filtros,
            'productos' => $productos,
        ];

        $this->render('entity.movimientos.list_movimiento', $data);
    }

    public function create(): void
    {
        // Verificar si el usuario tiene permisos para crear movimientos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        $tipoMovimiento = $_GET['tipo'] ?? 'TRASLADO';
        
        // Valores predeterminados para el movimiento
        $movimiento = [
            'tipo_movimiento' => $tipoMovimiento,
            'id_producto' => $_GET['id_producto'] ?? '',
            'cantidad' => $_GET['cantidad'] ?? 1,
            'id_origen' => $_GET['id_origen'] ?? '',
            'id_destino' => $_GET['id_destino'] ?? '',
        ];
        
        // Valores preseleccionados para configuración de almacenes
        $tiposAlmacen = [
            'origen' => $_GET['origen_tipo'] ?? 'planta',
            'destino' => $_GET['destino_tipo'] ?? 'planta'
        ];
        
        $seleccionados = [
            'origen_hospital' => $_GET['origen_hospital'] ?? '',
            'origen_planta' => $_GET['origen_planta'] ?? '',
            'destino_hospital' => $_GET['destino_hospital'] ?? '',
            'destino_planta' => $_GET['destino_planta'] ?? '',
        ];

        $success = false;
        $error = $_GET['error'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
            return;
        }

        // Obtener los almacenes para el usuario actual
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $almacenes = $this->almacenService->getAlmacenesForUser($userId, $userRole);

        // Obtener los productos disponibles
        $productos = $this->productoService->getAllProducts();

        // Obtener hospitales y plantas para que el usuario pueda seleccionar el almacen de origen y destino
        $hospitales = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        $plantas = $this->plantaService->getPlantasForUser($userId, $userRole);

        $data = [
            'movimiento' => $movimiento,
            'tiposAlmacen' => $tiposAlmacen,
            'seleccionados' => $seleccionados,
            'hospitales' => $hospitales,
            'plantas' => $plantas,
            'almacenes' => $almacenes,
            'productos' => $productos,
            'success' => $success,
            'error' => $error,
            'scripts' => ['movimientos.js', 'toasts.js']
        ];

        $this->render('entity.movimientos.create_movimiento', $data);
    }

    private function handleCreate(): void
    {
        $tipoMovimiento = $_POST['tipo_movimiento'] ?? '';
        $idProducto = (int)($_POST['id_producto'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $idOrigen = (int)($_POST['id_origen'] ?? 0);
        $idDestino = (int)($_POST['id_destino'] ?? 0);
        
        // Validaciones
        $errors = [];
        
        if (empty($tipoMovimiento)) {
            $errors[] = "Debe seleccionar un tipo de movimiento";
        }
        
        if ($idProducto <= 0) {
            $errors[] = "Debe seleccionar un producto válido";
        }
        
        if ($cantidad <= 0) {
            $errors[] = "La cantidad debe ser mayor a cero";
        }
        
        if ($tipoMovimiento === 'TRASLADO' && $idOrigen <= 0) {
            $errors[] = "Debe seleccionar un almacén de origen válido";
        }
        
        if ($idDestino <= 0) {
            $errors[] = "Debe seleccionar un almacén de destino válido";
        }
        
        if ($tipoMovimiento === 'TRASLADO' && $idOrigen === $idDestino) {
            $errors[] = "El almacén de origen y destino no pueden ser el mismo";
        }
        
        if (!empty($errors)) {
            $errorMsg = implode(". ", $errors);
            $this->redirect('/movimientos/create?error=' . urlencode($errorMsg));
        }
        
        // Crear movimiento
        try {
            $userId = $this->getCurrentUserId();
            $this->movimientoService->crearMovimiento(
                $tipoMovimiento,
                $idProducto,
                $cantidad,
                $tipoMovimiento === 'TRASLADO' ? $idOrigen : null,
                $idDestino,
                'PENDIENTE',
                $userId
            );
            $this->redirect('/movimientos?success=Movimiento creado correctamente');
        } catch (\Exception $e) {
            $this->redirect('/movimientos/create?error=' . urlencode($e->getMessage()));
        }
    }

    public function complete(): void
    {
       // TODO: Implementar lógica para completar un movimiento

    }

    public function cancel(): void
    {
        //TODO: Implementar lógica para cancelar un movimiento
    }
}