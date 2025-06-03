<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\ProductoService;
use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\PlantaService;
use InvalidArgumentException;
use Exception;

class ProductController extends BaseController
{
    private ProductoService $productoService;
    private AlmacenService $almacenService;
    private BotiquinService $botiquinService;
    private PlantaService $plantaService;

    public function __construct()
    {
        $this->productoService = new ProductoService();
        $this->almacenService = new AlmacenService();
        $this->botiquinService = new BotiquinService();
        $this->plantaService = new PlantaService();
    }

    public function index(): void
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);
        $data = $this->prepareDashboardData();
        // Renderizar la vista del dashboard de productos
        $this->render('entity.productos.dashboard_producto', $data);
    }

    public function prepareDashboardData()
    {
        // Determinar qué filtros están activos
        $filtros = [
            'codigo' => isset($_GET['codigo']) ? trim($_GET['codigo']) : null,
            'nombre' => isset($_GET['nombre']) ? trim($_GET['nombre']) : null,
        ];

        // Determinar si el filtro está activo
        $filtrarActivo = isset($_GET['filtrar']) || $filtros['codigo'] || $filtros['nombre'];

        // Verificar si se ha enviado un mensaje de éxito o error
        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

        try {
            // Usar el método optimizado de filtrado
            $productos = $this->productoService->filtrarProductos($filtros);

            // Preparar datos para la vista
            return [
                'productos' => $productos,
                'filtros' => $filtros,
                'filtrarActivo' => $filtrarActivo,
                'title' => 'Pegasus Medical',
                'navTitle' => 'Pegasus Medical',
                'scripts' => ['toasts.js'],
                'success' => $success,
                'error' => $error
            ];

        } catch (Exception $e) {
            header('Location: ' . url('productos.dashboard', ['error' => 'unexpected']));
            exit;
        }
    }

    public function create(): void
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        // Inicializar variables
        $producto = [
            'codigo' => '',
            'nombre' => '',
            'descripcion' => '',
            'unidad_medida' => ''
        ];
        $errors = [];
        
        // Si es POST, procesar el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($producto, $errors);
        }

        // Preparar datos para la vista
        $data = [
            'producto' => $producto,
            'errors' => $errors,
            'title' => 'Crear Producto',
            'scripts' => ['toasts.js'],
        ];

        $this->render('entity.productos.create_producto', $data);
    }

    /**
     * Maneja el proceso de creación de un producto
     */
    private function handleCreate(array &$producto, array &$errors): void
    {
        $producto = $this->collectProductData();
        
        try {
            if ($this->productoService->create(
                $producto['codigo'],
                $producto['nombre'],
                $producto['descripcion'],
                $producto['unidad_medida']
            )) {
                // Redirigir al dashboard con mensaje de éxito
                header('Location: ' . url('productos', ['success' => 'created']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "Error al crear el producto: " . $e->getMessage();
        }
    }

    public function edit(): void
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);

        // Obtener el ID del producto
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: ' . url('productos', ['error' => 'id_invalid']));
            exit;
        }

        // Inicializar variables
        $producto = [];
        $errors = [];

        // Si es POST, procesar el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id, $producto, $errors);
        } else {
            // Cargar datos del producto existente
            $this->loadProductData($id, $producto);
        }

        // Preparar datos para la vista
        $data = [
            'producto' => $producto,
            'errors' => $errors,
            'title' => 'Editar Producto',
            'scripts' => ['toasts.js'],
        ];

        $this->render('entity.productos.edit_producto', $data);
    }

    /**
     * Carga los datos de un producto existente
     */
    private function loadProductData(int $id, &$producto): void
    {
        try {
            $productoObj = $this->productoService->getProductoById($id);
            
            if (!$productoObj) {
                header('Location: ' . url('productos', ['error' => 'producto_not_found']));
                exit;
            }
            
            $producto = [
                'id' => $productoObj->getId(),
                'codigo' => $productoObj->getCodigo(),
                'nombre' => $productoObj->getNombre(),
                'descripcion' => $productoObj->getDescripcion(),
                'unidad_medida' => $productoObj->getUnidadMedida()
            ];
        } catch (Exception $e) {
            header('Location: ' . url('productos', ['error' => 'unexpected']));
            exit;
        }
    }

    /**
     * Maneja el proceso de edición de un producto
     */
    private function handleEdit(int $id, array &$producto, array &$errors): void
    {
        $producto = $this->collectProductData();
        $producto['id'] = $id;
        
        try {
            if ($this->productoService->update(
                $id,
                $producto['codigo'],
                $producto['nombre'],
                $producto['descripcion'],
                $producto['unidad_medida']
            )) {
                // Redirigir al dashboard con mensaje de éxito
                header('Location: ' . url('productos', ['success' => 'updated']));
                exit;
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = "Error al actualizar el producto: " . $e->getMessage();
        }
    }

    public function delete(): void
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR']);

        // Obtener el ID del producto
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: ' . url('productos', ['error' => 'id_invalid']));
            exit;
        }

        $errors = [];
        $producto = null;

        // Si es POST, procesar la eliminación
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleDelete($id, $errors);
        } else {
            // Cargar datos del producto para mostrar en formulario de confirmación
            $this->loadProductForDelete($id, $producto);
        }

        // Preparar datos para la vista
        $data = [
            'producto' => $producto,
            'title' => 'Eliminar Producto',
            'errors' => $errors,
            'scripts' => ['toasts.js'],
        ];

        $this->render('entity.productos.delete_producto', $data);
    }

    /**
     * Carga los datos del producto para la confirmación de eliminación
     */
    private function loadProductForDelete(int $id, &$producto): void
    {
        try {
            $producto = $this->productoService->getProductoById($id);

            if (!$producto) {
                header('Location: ' . url('productos', ['error' => 'producto_not_found']));
                exit;
            }
        } catch (Exception $e) {
            header('Location: ' . url('productos', ['error' => 'unexpected']));
            exit;
        }
    }

    /**
     * Maneja el proceso de eliminación de un producto
     */
    private function handleDelete(int $id, array &$errors): void
    {
        $confirmDelete = filter_input(INPUT_POST, 'confirm_delete', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($confirmDelete === 'yes') {
            try {
                if ($this->productoService->delete($id)) {
                    header('Location: ' . url('productos', ['success' => 'deleted']));
                } else {
                    header('Location: ' . url('productos', ['error' => 'delete_failed']));
                }
                exit;
            } catch (Exception $e) {
                $errors[] = "Error al eliminar el producto: " . $e->getMessage();
            }
        } else {
            header('Location: ' . url('productos', ['error' => 'delete_cancelled']));
            exit;
        }
    }

    /**
     * Recolecta los datos del producto desde el formulario
     */
    private function collectProductData(): array
    {
        return [
            'codigo' => filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS),
            'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS),
            'descripcion' => filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS),
            'unidad_medida' => filter_input(INPUT_POST, 'unidad_medida', FILTER_SANITIZE_SPECIAL_CHARS)
        ];
    }
}
