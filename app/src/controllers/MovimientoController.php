<?php

namespace controllers;

use JetBrains\PhpStorm\NoReturn;
use middleware\AuthMiddleware;
use model\entity\Movimiento;
use model\service\AlmacenService;
use model\service\BotiquinService;
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
    private BotiquinService $botiquinService;

    public function __construct()
    {
        $this->movimientoService = new MovimientoService();
        $this->almacenService = new AlmacenService();
        $this->productoService = new ProductoService();
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
        $this->botiquinService = new BotiquinService();
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
        $filtros = ['estado' => 'PENDIENTE'];

        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Admin y gestor general ven todos los movimientos pendientes
            $pendientes = $this->movimientoService->find($filtros);
        } else {
            // Gestor de hospital y gestor de planta ven solo movimientos asociados a sus almacenes
            $almacenes = $this->almacenService->getAlmacenesForUser($userId, $userRole);
            $almacenIds = $this->movimientoService->extractAlmacenIds($almacenes);

            if (!empty($almacenIds)) {
                $pendientes = $this->movimientoService->find($filtros, $almacenIds);
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
            'scripts' => ['toasts.js'],
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
            'estado' => $_GET['estado'] ?? null,
            'tipo_movimiento' => $_GET['tipo_movimiento'] ?? null,
            'producto' => $_GET['producto'] ?? null,
            'destino' => $_GET['destino'] ?? null,
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

        // Inicializar datos del formulario desde parámetros GET de manera simplificada
        $tipo = $_GET['tipo'] ?? 'TRASLADO';
        
        // Configuración inicial del movimiento
        $movimiento = [
            'tipo_movimiento' => $tipo,
            'id_producto' => $_GET['id_producto'] ?? '',
            'cantidad' => $_GET['cantidad'] ?? 1,
            'id_origen' => $_GET['id_origen'] ?? '',
            'id_destino' => $_GET['id_destino'] ?? '',
            'id_botiquin_origen' => $_GET['id_botiquin_origen'] ?? '',
            'botiquin_hospital' => $_GET['botiquin_hospital'] ?? '',
        ];
        
        // Simplificar la configuración de tipos de almacén
        $tiposAlmacen = [
            'origen' => $_GET['origen_tipo'] ?? 'planta',
            'destino' => $_GET['destino_tipo'] ?? 'planta'
        ];
        
        // Agrupar los datos de selección en una estructura clara
        $seleccionados = [
            'origen_hospital' => $_GET['origen_hospital'] ?? '',
            'origen_planta' => $_GET['origen_planta'] ?? '',
            'destino_hospital' => $_GET['destino_hospital'] ?? '',
            'destino_planta' => $_GET['destino_planta'] ?? '',
        ];

        // Manejar envío del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
            return;
        }

        // Obtener datos de usuario y entidades necesarias
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Cargar los datos necesarios para los selectores
        $data = [
            'movimiento' => $movimiento,
            'tiposAlmacen' => $tiposAlmacen,
            'seleccionados' => $seleccionados,
            'hospitales' => $this->hospitalService->getHospitalsForUser($userId, $userRole),
            'plantas' => $this->plantaService->getPlantasForUser($userId, $userRole),
            'almacenes' => $this->almacenService->getAlmacenesForUser($userId, $userRole),
            'productos' => $this->productoService->getAllProducts(),
            'botiquines' => $this->botiquinService->getBotiquinesForUser($userId, $userRole),
            'error' => $_GET['error'] ?? null,
            'success' => isset($_GET['success']),
            'scripts' => ['almacen_common.js', 'movimientos.js', 'toasts.js', 'hospital_planta_botiquin.js'],
        ];

        $this->render('entity.movimientos.create_movimiento', $data);
    }

    private function handleCreate(): void
    {
        $tipoMovimiento = $_POST['tipo_movimiento'] ?? '';
        $idBotiquinOrigen = (int)($_POST['botiquin_origen'] ?? null);
        $idDestino = (int)($_POST['id_destino'] ?? 0);
        
        // Para devoluciones, no necesitamos producto ni cantidad específicos
        $esDevolucion = ($tipoMovimiento === 'DEVOLUCION');
        
        // Si no es devolución, obtenemos producto y cantidad normales
        if (!$esDevolucion) {
            $idProducto = (int)($_POST['id_producto'] ?? 0);
            $cantidad = (int)($_POST['cantidad'] ?? 0);
            $idOrigen = (int)($_POST['id_origen'] ?? null);
        } else {
            // Para devoluciones, estos valores serán NULL
            $idProducto = null;
            $cantidad = null;
            $idOrigen = null;
        }

        // Validaciones
        $errors = [];

        if (empty($tipoMovimiento)) {
            $errors[] = "Debe seleccionar un tipo de movimiento";
        }

        if (!$esDevolucion && $idProducto <= 0) {
            $errors[] = "Debe seleccionar un producto válido";
        }

        if (!$esDevolucion && $cantidad <= 0) {
            $errors[] = "La cantidad debe ser mayor a cero";
        }

        if ($tipoMovimiento === 'TRASLADO' && $idOrigen <= 0) {
            $errors[] = "Debe seleccionar un almacén de origen válido";
        }

        if ($tipoMovimiento === 'DEVOLUCION' && $idBotiquinOrigen <= 0) {
            $errors[] = "Debe seleccionar un botiquín de origen válido";
        }

        if ($idDestino <= 0) {
            $errors[] = "Debe seleccionar un almacén de destino válido";
        }

        if ($tipoMovimiento === 'TRASLADO' && $idOrigen === $idDestino) {
            $errors[] = "El almacén de origen y destino no pueden ser el mismo";
        }

        if (!empty($errors)) {
            $errorMsg = implode(". ", $errors);
            $this->redirect('/movimientos/create?error=' . urlencode($errorMsg) . '&tipo=' . urlencode($tipoMovimiento));
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
                $userId,
                $tipoMovimiento === 'DEVOLUCION' ? $idBotiquinOrigen : null
            );
            
            // Redirigir al dashboard con mensaje de éxito
            $successMsg = 'Movimiento de tipo ' . $tipoMovimiento . ' creado correctamente';
            $this->redirect('/movimientos?toast=success&toastmsg=' . urlencode($successMsg));
        } catch (\Exception $e) {
            // Preservar el tipo de movimiento al mostrar el error
            $this->redirect('/movimientos/create?error=' . urlencode($e->getMessage()) . '&tipo=' . urlencode($tipoMovimiento));
        }
    }

    #[NoReturn]
    public function complete(): void
    {
        // Permitir solo por AJAX
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $ok = $this->movimientoService->completarMovimiento($id);
            header('Content-Type: application/json');
            if ($ok) {
                echo json_encode(['success' => true, 'msg' => 'Movimiento completado correctamente']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'No se pudo completar el movimiento']);
            }
            exit;
        }
        // Si no es AJAX, redirigir
        $this->redirect('/movimientos');
    }

    #[NoReturn]
    public function cancel(): void
    {
        // Permitir solo por AJAX
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $ok = $this->movimientoService->cancelarMovimiento($id);
            header('Content-Type: application/json');
            if ($ok) {
                echo json_encode(['success' => true, 'msg' => 'Movimiento cancelado correctamente']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'No se pudo cancelar el movimiento']);
            }
            exit;
        }
        // Si no es AJAX, redirigir
        $this->redirect('/movimientos');
    }

}