<?php

namespace controllers;

use JetBrains\PhpStorm\NoReturn;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\ReposicionService;
use model\service\ProductoService;
use model\service\AlmacenService;
use model\service\BotiquinService;
use middleware\AuthMiddleware;

class ReposicionController extends BaseController
{
    private ReposicionService $reposicionService;
    private ProductoService $productoService;
    private AlmacenService $almacenService;
    private BotiquinService $botiquinService;
    private PlantaService $plantaService;
    private HospitalService $hospitalService;

    public function __construct()
    {
        $this->reposicionService = new ReposicionService();
        $this->productoService = new ProductoService();
        $this->almacenService = new AlmacenService();
        $this->botiquinService = new BotiquinService();
        $this->plantaService = new PlantaService();
        $this->hospitalService = new HospitalService();
    }

    public function index(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'USUARIO_BOTIQUIN']);

        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        $pendientes = [];
        $filtros = ['estado' => 'PENDIENTE'];

        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            $pendientes = $this->reposicionService->find($filtros);
        } else{
            // Obtener los botiquines del usuario
            $botiquines = $this->botiquinService->getBotiquinesForUser($userId, $userRole);
            $botiquinIds = array_map(fn($b) => $b->getId(), $botiquines);
            if (empty($botiquinIds)) {
                $botiquines = [];
            } else {
                $filtros['id_botiquin'] = $botiquinIds;
                $pendientes = $this->reposicionService->find($filtros);
            }
        }


        $data = [
            'pendientes' => $pendientes,

            'toast' => $_GET['toast'] ?? null,
            'toastmsg' => $_GET['toastmsg'] ?? null
        ];

        $this->render('entity.reposiciones.dashboard_reposicion', $data);
    }

    public function create(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        $success = false;
        $error = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $this->handleCreate();
           return;
        }

        // Filtrar hospitales, plantas y  según el rol del usuario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Obtener productos disponibles
        $productos = $this->productoService->getAllProducts();

        // Obtener los selectores según el rol del usuario
        $hospitales = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        $plantas = $this->plantaService->getPlantasForUser($userId, $userRole);
        $almacenes = $this->almacenService->getAlmacenesForUser($userId, $userRole);
        $botiquines = $this->botiquinService->getBotiquinesForUser($userId, $userRole);

        $this->render('entity.reposiciones.create_reposicion', [
            'productos' => $productos,
            'hospitales' => $hospitales,
            'plantas' => $plantas,
            'almacenes' => $almacenes,
            'botiquines' => $botiquines,
            'success' => $success,
            'error' => $error,
            'scripts' => ['almacen_common.js', 'reposiciones.js', 'hospital_planta_botiquin.js', 'toasts.js'],
            'title' => 'Crear Reposición',
        ]);
    }

    public function list(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'USUARIO_BOTIQUIN']);

        // Obtener filtros de la solicitud
        $filtros = [
            'estado' => $_GET['estado'] ?? null,
            'producto' => $_GET['producto'] ?? null,
            'orden' => $_GET['orden'] ?? 'fecha_desc',
        ];

        // Obtener información del usuario
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Obtener reposiciones según el rol y permisos
        $reposiciones = [];

        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            // Admin y gestor general ven todas las reposiciones
            $reposiciones = $this->reposicionService->find($filtros);
        } else {
            // Recuperar botiquines asociados al usuario
            $botiquines = $this->botiquinService->getBotiquinesForUser($userId, $userRole);
            $botiquinIds = array_map(fn($b) => $b->getId(), $botiquines);

            if (!empty($botiquinIds)) {
                $filtros['id_botiquin'] = $botiquinIds;
                $reposiciones = $this->reposicionService->find($filtros);
            }
        }

        // Paginación
        $total = count($reposiciones);
        $pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $por_pagina = 20;
        $paginas = max(1, ceil($total / $por_pagina));
        $reposiciones_pagina = array_slice($reposiciones, ($pagina_actual - 1) * $por_pagina, $por_pagina);

        // Obtener productos para el filtro
        $productos = $this->productoService->getAllProducts();

        $data = [
            'reposiciones' => $reposiciones_pagina,
            'total' => $total,
            'pagina_actual' => $pagina_actual,
            'paginas' => $paginas,
            'filtros' => $filtros,
            'productos' => $productos,
        ];

        $this->render('entity.reposiciones.list_reposicion', $data);
    }

    #[NoReturn]
    public function complete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $ok = $this->reposicionService->completarReposicion($id);
            header('Content-Type: application/json');
            if ($ok) {
                echo json_encode(['success' => true, 'msg' => 'Reposición completada correctamente']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'No se pudo completar la reposición']);
            }
            exit;
        }
        $this->redirect('/reposiciones');
    }

    #[NoReturn]
    public function cancel(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $ok = $this->reposicionService->cancelarReposicion($id);
            header('Content-Type: application/json');
            if ($ok) {
                echo json_encode(['success' => true, 'msg' => 'Reposición cancelada correctamente']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'No se pudo cancelar la reposición']);
            }
            exit;
        }
        $this->redirect('/reposiciones');
    }
}