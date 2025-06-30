<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\LecturaService;
use Exception;
use InvalidArgumentException;
use model\service\PlantaService;
use model\service\ProductoService;
use model\service\UserService;

class LecturaController extends BaseController
{
    private LecturaService $lecturaService;
    private ProductoService $productoService;
    private BotiquinService $botiquinService;
    private UserService $usuarioService;
    private PlantaService $plantaService;
    private HospitalService $hospitalService;

    
    public function __construct()
    {
        $this->lecturaService = new LecturaService();
        $this->productoService = new ProductoService();
        $this->botiquinService = new BotiquinService();
        $this->usuarioService = new UserService();
        $this->plantaService = new PlantaService();
        $this->hospitalService = new HospitalService();
    }

    public function index(): void
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);
        
        // Obtener parámetros de filtro
        $filtroProducto = $_GET['producto'] ?? null;
        $filtroBotiquin = $_GET['botiquin'] ?? null;
        $filtroUsuario = $_GET['usuario'] ?? null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtroProducto || $filtroBotiquin || $filtroUsuario;
        
        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;
        
        // Preparar filtros
        $filters = [];
        if ($filtroProducto) $filters['id_producto'] = $filtroProducto;
        if ($filtroBotiquin) $filters['id_botiquin'] = $filtroBotiquin;
        if ($filtroUsuario) $filters['id_usuario'] = $filtroUsuario;
        
        try {
            // Obtener lecturas filtradas
            $lecturas = $this->lecturaService->getAllLecturas($filters);
            
            // Obtener listas para los filtros
            $hospitales = $this->hospitalService->getAllHospitals();
            $plantas = $this->plantaService->getAllPlantas();
            $productos = $this->productoService->getAllProducts();
            $botiquines = $this->botiquinService->getAllBotiquines();
            $usuarios = $this->usuarioService->getAllBotiquinUsers();


            
            $data = [
                'lecturas' => $lecturas,
                'hospitales' => $hospitales,
                'plantas' => $plantas,
                'botiquines' => $botiquines,
                'productos' => $productos,
                'usuarios' => $usuarios,
                'filtroProducto' => $filtroProducto,
                'filtroBotiquin' => $filtroBotiquin,
                'filtroUsuario' => $filtroUsuario,
                'filtrarActivo' => $filtrarActivo,
                'title' => 'Registro de Lecturas',
                'scripts' => ['toasts.js', 'lecturas.js', 'hospital_planta_botiquin.js'],
                'navTitle' => 'Lecturas',
                'success' => $success,
                'error' => $error
            ];
            
            $this->render('entity.lecturas.dashboard_lectura', $data);
        } catch (Exception $e) {
            // Redirigir con error en caso de fallo
            header("Location: " . url('lecturas', ['error' => 'unexpected']));
            exit;
        }
    }

    public function create(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'USUARIO_BOTIQUIN']);
        
        // Inicializar variables
        $lectura = [
            'id_hospital' => $_GET['hospital'] ?? '',
            'id_planta' => $_GET['planta'] ?? '',
            'id_botiquin' => $_GET['botiquin'] ?? '',
            'id_producto' => $_GET['producto'] ?? '',
            'cantidad' => $_GET['cantidad'] ?? '',
        ];
        
        $errors = [];
        $success = false;
        
        // Obtener listas para los formularios
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $hospitales = $this->hospitalService->getHospitalsForUser($userId, $userRole);
        $plantas = $this->plantaService->getAllPlantas();
        $productos = $this->productoService->getAllProducts();
        $botiquines = $this->botiquinService->getAllBotiquines();
        
        // Procesar el formulario si es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($lectura, $errors, $success);
        }
        
        $data = [
            'lectura' => $lectura,
            'hospitales' => $hospitales,
            'plantas' => $plantas,
            'botiquines' => $botiquines,
            'productos' => $productos,
            'errors' => $errors,
            'success' => $success,
            'title' => 'Registrar Lectura',
            'scripts' => ['toasts.js', 'hospital_planta_botiquin.js', 'lecturas.js'],
        ];
        
        $this->render('entity.lecturas.create_lectura', $data);
    }
    
    private function handleCreate(array &$lectura, array &$errors, bool &$success): void
    {
        // Sanitizar datos de entrada
        $lectura['id_botiquin'] = filter_input(INPUT_POST, 'id_botiquin', FILTER_SANITIZE_NUMBER_INT);
        $lectura['id_producto'] = filter_input(INPUT_POST, 'id_producto', FILTER_SANITIZE_NUMBER_INT);
        $lectura['cantidad'] = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);
        
        try {
            // Obtener ID del usuario actual
            $userId = $this->getCurrentUserId();
            
            // Intentar crear la lectura
            $success = $this->lecturaService->createLectura(
                (int)$lectura['id_botiquin'],
                (int)$lectura['id_producto'],
                (int)$lectura['cantidad'],
                $userId
            );
            
            // Redirigir tras crear con éxito
            if ($success) {
                header("Location: " . url('lecturas', ['success' => 'created']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "Error al registrar la lectura: " . $e->getMessage();
        }
    }
}
