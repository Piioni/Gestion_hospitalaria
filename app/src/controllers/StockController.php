<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\ProductoService;
use model\service\StockAlmacenService;
use model\service\StockBotiquinService;
use model\service\StockService;
use model\service\UserLocationService;

class StockController extends BaseController
{
    private StockAlmacenService $stockAlmacenService;
    private StockBotiquinService $stockBotiquinService;
    private HospitalService $hospitalService;
    private StockService $stockService;
    private PlantaService $plantaService;
    private AlmacenService $almacenService;
    private BotiquinService $botiquinService;
    private ProductoService $productoService;

    public function __construct()
    {
        $this->stockAlmacenService = new StockAlmacenService();
        $this->stockBotiquinService = new StockBotiquinService();
        $this->hospitalService = new HospitalService();
        $this->stockService = new StockService();
        $this->plantaService = new PlantaService();
        $this->almacenService = new AlmacenService();
        $this->botiquinService = new BotiquinService();
        $this->productoService = new ProductoService();
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
        $filtrarActivo = isset($_GET['filtrar']) && $_GET['filtrar'] == 1;


        // Determinar qué plantas puede ver el usuario según su rol
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            $plantas = $this->plantaService->getAllPlantas();
        } else {
            $plantas = $this->plantaService->getPlantasForUser($userId, $userRole);
        }

        // Filtrar los botiquines según los parámetros y permisos del usuario
        $botiquines = $this->botiquinService->getBotiquinesForUser($userId, $userRole);

        return [
            'botiquines' => $botiquines,
            'plantas' => $plantas,
            'filtrarActivo' => $filtrarActivo,
            'filtro_plantas' => $filtro_plantas,
            'filtro_botiquin' => $filtro_botiquin,
            'filtro_nombre' => $filtro_nombre,
            'userRole' => $userRole,
            'hospitalService' => $this->hospitalService,
            'plantaService' => $this->plantaService,
            'productoService' => $this->productoService,
            'stockService' => $this->stockBotiquinService, // Cambiado a StockBotiquinService
            'title' => "Stock de Botiquines",
            'scripts' => "toasts.js"
        ];
    }

    public function indexAlmacen(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'GESTOR_ALMACEN']);

        $data = $this->prepareAlmacenStockData();

        $this->render('entity.stocks.stock_almacen', $data);
    }

    private function prepareAlmacenStockData(): array
    {
        // Obtener información del usuario actual
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();

        // Obtener filtros desde la URL
        $filtro_hospital = isset($_GET['id_hospital']) ? (int)$_GET['id_hospital'] : null;
        $filtro_plantas = isset($_GET['planta']) ? (int)$_GET['planta'] : null;
        $filtro_almacen = isset($_GET['id_almacen']) ? (int)$_GET['id_almacen'] : null;
        $filtro_tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : null; // Nuevo filtro para tipo de producto
        $filtro_nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : null;
        $filtrarActivo = isset($_GET['filtrar']) && $_GET['filtrar'] == 1;

        // Determinar qué plantas puede ver el usuario según su rol
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            $plantas = $this->plantaService->getAllPlantas();
        } else {
            $plantas = $this->plantaService->getPlantasForUser($userId, $userRole);
        }

        // Filtrar los almacenes según los parámetros y permisos del usuario
        $almacenes = $this->almacenService->getAlmacenesForUser($userId, $userRole);

        return [
            'almacenes' => $almacenes,
            'plantas' => $plantas,
            'filtrarActivo' => $filtrarActivo,
            'filtro_hospital' => $filtro_hospital,
            'filtro_tipo' => $filtro_tipo,
            'filtro_plantas' => $filtro_plantas,
            'filtro_almacen' => $filtro_almacen,
            'filtro_nombre' => $filtro_nombre,
            'userRole' => $userRole,
            'hospitalService' => $this->hospitalService,
            'plantaService' => $this->plantaService,
            'productoService' => $this->productoService,
            'stockService' => $this->stockAlmacenService, // Cambiado a StockAlmacenService
            'title' => "Stock de Almacenes",
            'scripts' => "toasts.js"
        ];
    }
}
