<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\BotiquinService;
use model\service\PlantaService;
use model\service\HospitalService;
use model\service\AlmacenService;
use model\service\StockBotiquinService;
use Exception;
use InvalidArgumentException;

class BotiquinController extends BaseController
{
    private BotiquinService $botiquinService;
    private PlantaService $plantaService;
    private HospitalService $hospitalService;
    private AlmacenService $almacenService;
    private StockBotiquinService $stockService;

    public function __construct()
    {
        $this->botiquinService = new BotiquinService();
        $this->plantaService = new PlantaService();
        $this->hospitalService = new HospitalService();
        $this->almacenService = new AlmacenService();
        $this->stockService = new StockBotiquinService();
    }

    public function index(): void
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        $data = $this->prepareDashboardData();
        $this->render('entity.botiquines.dashboard_botiquin', $data);
    }

    private function prepareDashboardData(): array
    {
        // Obtener el filtro de planta desde la URL, si existe
        $filtro_plantas = isset($_GET['planta']) ? (int)$_GET['planta'] : null;
        $id_botiquin = isset($_GET['id_botiquin']) ? (int)$_GET['id_botiquin'] : null;
        $filtrarActivo = isset($_GET['filtrar']) || $filtro_plantas || $id_botiquin || isset($_GET['nombre']);
        $filtroNombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : null;

        $plantas = $this->plantaService->getAllPlantas();
        // TODO: Modificar la forma en la que se filtran los botiquines (Hospital - planta) 
        $botiquines = $this->botiquinService->filterBotiquines($filtro_plantas, $id_botiquin, $filtroNombre);

        return [
            'botiquines' => $botiquines,
            'plantas' => $plantas,
            'filtro_plantas' => $filtro_plantas,
            'id_botiquin' => $id_botiquin,
            'filtrarActivo' => $filtrarActivo,
            'filtroNombre' => $filtroNombre,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'title' => "Sistema de Gestión Hospitalaria",
            'navTitle' => "Gestión de Botiquines",
            'scripts' => "toasts.js",
            'stockBotiquinService' => $this->stockService,
            'plantaService' => $this->plantaService,
            'hospitalService' => $this->hospitalService,
            'botiquinService' => $this->botiquinService
        ];
    }

    public function create(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        $plantas = $this->plantaService->getAllPlantas();

        // Inicializar el botiquín con valores por defecto
        $botiquin = [
            'id_planta' => isset($_GET['id_planta']) ? filter_input(INPUT_GET, 'id_planta', FILTER_SANITIZE_NUMBER_INT) : '',
            'nombre' => '',
            'capacidad' => 0,
        ];

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($botiquin, $errors, $success);
        }

        $data = [
            'plantas' => $plantas,
            'botiquin' => $botiquin,
            'errors' => $errors,
            'success' => $success,
            'title' => "Crear Botiquín",
            'scripts' => "toasts.js"
        ];

        $this->render('entity.botiquines.create_botiquin', $data);
    }

    private function handleCreate(array &$botiquin, array &$errors, bool &$success): void
    {
        // Sanitizar datos de entrada
        $id_planta = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_NUMBER_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_SANITIZE_NUMBER_INT);

        $botiquin = [
            'id_planta' => $id_planta,
            'nombre' => $nombre,
            'capacidad' => $capacidad,
        ];

        try {
            $success = $this->botiquinService->createBotiquin($id_planta, $nombre, $capacidad);

            if ($success) {
                // Redirigir a la página de lista de botiquines con mensaje de éxito
                header('Location: ' . url('botiquines', ['success' => 'created']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            // Manejo de errores de validación
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            // Manejo de errores
            $errors[] = "Error al crear el botiquín: " . $e->getMessage();
        }
    }

    public function edit(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);

        $id_botiquin = $_GET['id_botiquin'] ?? null;

        if (!$this->validateBotiquinId($id_botiquin)) {
            header("Location: " . url('botiquines'));
            exit;
        }

        // Validar que exista un botiquín con el ID proporcionado
        $botiquin = $this->loadBotiquinData($id_botiquin);
        $plantas = $this->plantaService->getAllPlantas();
        $errors = [];
        $success = false;

        // Procesar el formulario de edición
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id_botiquin, $errors, $success);
        }

        $data = [
            'botiquin' => $botiquin,
            'plantas' => $plantas,
            'errors' => $errors,
            'success' => $success,
            'title' => "Editar Botiquín",
            'scripts' => "toasts.js"
        ];

        $this->render('entity.botiquines.edit_botiquin', $data);
    }

    private function validateBotiquinId($id): bool
    {
        return !empty($id) && is_numeric($id);
    }

    private function loadBotiquinData($id_botiquin)
    {
        $botiquin = $this->botiquinService->getBotiquinById($id_botiquin);
        if (!$botiquin) {
            header("Location: " . url('botiquines', ['error' => 'id_not_found']));
            exit;
        }
        return $botiquin;
    }

    private function handleEdit($id_botiquin, array &$errors, bool &$success): void
    {
        $id_planta = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_NUMBER_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_SANITIZE_NUMBER_INT);

        try {
            $success = $this->botiquinService->updateBotiquin($id_botiquin, $id_planta, $nombre, $capacidad);

            if ($success) {
                // Redirigir a la página de lista de botiquines con mensaje de éxito
                header('Location: ' . url('botiquines', ['success' => 'updated']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "Error al actualizar el botiquín: " . $e->getMessage();
        }
    }

    public function delete(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL']);

        // Obtener el ID del botiquín
        $id_botiquin = $_GET["id_botiquin"] ?? null;

        if (!$this->validateBotiquinId($id_botiquin)) {
            header('Location: ' . url('botiquines', ['error' => 'id_invalid']));
            exit;
        }

        try {
            $data = $this->prepareDeleteData($id_botiquin);

            // Si es una solicitud de confirmación, eliminar el botiquín
            if (isset($_POST["confirm"])) {
                $this->handleDelete($id_botiquin, $data);
            }

            $this->render('entity.botiquines.delete_botiquin', $data);
        } catch (Exception $e) {
            // Error inesperado
            header('Location: ' . url('botiquines', ['error' => 'unexpected']));
            exit;
        }
    }

    private function prepareDeleteData($id_botiquin): array
    {
        // Obtener información del botiquín
        $botiquin = $this->botiquinService->getBotiquinById($id_botiquin);

        if (!$botiquin) {
            header('Location: ' . url('botiquines', ['error' => 'id_not_found']));
            exit;
        }

        // Obtener la planta asociada y los productos del botiquín
        $planta = $this->plantaService->getPlantaById($botiquin->getIdPlanta());
        $stockBotiquin = $this->stockService->getStockByBotiquinId($id_botiquin);
        $hasProducts = !empty($stockBotiquin);

        // Obtener todos los almacenes para que el usuario elija un destino
        $almacenes = $this->almacenService->getAllAlmacenes();

        return [
            'botiquin' => $botiquin,
            'planta' => $planta,
            'cantidadProductos' => count($stockBotiquin),
            'hasProducts' => $hasProducts,
            'almacenes' => $almacenes,
            'error' => null,
            'title' => "Confirmar Eliminación",
            'scripts' => "toasts.js"
        ];
    }

    private function handleDelete($id_botiquin, array &$data): void
    {
        try {
            $idAlmacenDestino = null;
            if ($data['hasProducts']) {
                $idAlmacenDestino = $_POST["almacen_destino"] ?? null;

                if (!$idAlmacenDestino || !is_numeric($idAlmacenDestino)) {
                    throw new InvalidArgumentException("Debe seleccionar un almacén destino válido para transferir los productos.");
                }
            }

            $this->botiquinService->deleteBotiquin($id_botiquin, $idAlmacenDestino);
            header('Location: ' . url('botiquines', ['success' => 'deleted']));
            exit;
        } catch (InvalidArgumentException $e) {
            $data['error'] = $e->getMessage();
        } catch (Exception $e) {
            error_log("Error al procesar eliminación: " . $e->getMessage());
            $data['error'] = "Ha ocurrido un error inesperado al procesar la eliminación.";
        }
    }
}