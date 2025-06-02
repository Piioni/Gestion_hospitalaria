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
            'nombre' => isset($_GET['nombre']) ? (int)$_GET['nombre'] : null,
        ];

        // Determinar si el filtro está activo
        $filtrarActivo = isset($_GET['filtrar']) || $filtros['codigo'] || $filtros['nombre'];

        // Verificar si se ha enviado un mensaje de éxito o error
        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

        try {
            // Usar el méto do optimizado de filtrado
            $productos = $this->productoService->filtrarProductos($filtros);

            // Preparar datos para la vista
            return  [
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
        
        // Valores por defecto
        $producto = [
            'codigo' => '',
            'nombre' => '',
            'descripcion' => '',
            'unidad_medida' => ''
        ];

        $errors = [];
        $success = false;

        // Procesar el formulario si es un POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto['codigo'] = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS);
            $producto['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
            $producto['descripcion'] = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
            $producto['unidad_medida'] = filter_input(INPUT_POST, 'unidad_medida', FILTER_SANITIZE_SPECIAL_CHARS);
            
            try {
                if ($this->productoService->create(
                    $producto['codigo'],
                    $producto['nombre'],
                    $producto['descripcion'],
                    $producto['unidad_medida']
                )) {
                    // Redirigir en caso de éxito
                    header('Location: ' . url('productos.dashboard', ['success' => 'created']));
                    exit;
                }
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            } catch (Exception $e) {
                $errors[] = "Error al crear el producto: " . $e->getMessage();
            }
        }
        
        // Preparar datos para la vista
        $data = [
            'producto' => $producto,
            'errors' => $errors,
            'success' => $success,
            'title' => 'Crear Producto',
        ];
        
        $this->render('entity.productos.create_producto', $data);
    }
    
    public function edit()
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL']);
        
        // Obtener el ID del producto
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: ' . url('productos.dashboard', ['error' => 'id_invalid']));
            exit;
        }
        
        try {
            $productoObj = $this->productoService->getProductoById($id);
            
            if (!$productoObj) {
                header('Location: ' . url('productos.dashboard', ['error' => 'producto_not_found']));
                exit;
            }
            
            // Inicializar array con datos del producto
            $producto = [
                'id' => $productoObj->getId(),
                'codigo' => $productoObj->getCodigo(),
                'nombre' => $productoObj->getNombre(),
                'descripcion' => $productoObj->getDescripcion(),
                'unidad_medida' => $productoObj->getUnidadMedida()
            ];
            
            $errors = [];
            $success = false;
            
            // Procesar el formulario si es un POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $producto['codigo'] = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS);
                $producto['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
                $producto['descripcion'] = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
                $producto['unidad_medida'] = filter_input(INPUT_POST, 'unidad_medida', FILTER_SANITIZE_SPECIAL_CHARS);
                
                try {
                    if ($this->productoService->update(
                        $id,
                        $producto['codigo'],
                        $producto['nombre'],
                        $producto['descripcion'],
                        $producto['unidad_medida']
                    )) {
                        // Redirigir en caso de éxito
                        header('Location: ' . url('productos.dashboard', ['success' => 'updated']));
                        exit;
                    }
                } catch (InvalidArgumentException $e) {
                    $errors[] = $e->getMessage();
                } catch (Exception $e) {
                    $errors[] = "Error al actualizar el producto: " . $e->getMessage();
                }
            }
            
            // Preparar datos para la vista
            $data = [
                'producto' => $producto,
                'errors' => $errors,
                'success' => $success,
                'title' => 'Editar Producto',
            ];
            
            $this->render('entity.productos.edit_producto', $data);
            
        } catch (Exception $e) {
            header('Location: ' . url('productos.dashboard', ['error' => 'unexpected']));
            exit;
        }
    }
    
    public function delete()
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR']);
        
        // Obtener el ID del producto
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: ' . url('productos.dashboard', ['error' => 'id_invalid']));
            exit;
        }
        
        try {
            $producto = $this->productoService->getProductoById($id);
            
            if (!$producto) {
                header('Location: ' . url('productos.dashboard', ['error' => 'producto_not_found']));
                exit;
            }
            
            $confirmDelete = filter_input(INPUT_POST, 'confirm_delete', FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Si se confirma la eliminación
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $confirmDelete === 'yes') {
                if ($this->productoService->delete($id)) {
                    header('Location: ' . url('productos.dashboard', ['success' => 'deleted']));
                    exit;
                } else {
                    header('Location: ' . url('productos.dashboard', ['error' => 'delete_failed']));
                    exit;
                }
            }
            
            // Preparar datos para la vista de confirmación
            $data = [
                'producto' => $producto,
                'title' => 'Eliminar Producto',
            ];
            
            $this->render('entity.productos.delete_producto', $data);
            
        } catch (Exception $e) {
            header('Location: ' . url('productos.dashboard', ['error' => 'unexpected']));
            exit;
        }
    }

    public function view()
    {
        // Verificar permisos
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA']);
        
        // Obtener el ID del producto
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: ' . url('productos.dashboard', ['error' => 'id_invalid']));
            exit;
        }
        
        try {
            $producto = $this->productoService->getProductoById($id);
            
            if (!$producto) {
                header('Location: ' . url('productos.dashboard', ['error' => 'producto_not_found']));
                exit;
            }
            
            // Preparar datos para la vista
            $data = [
                'producto' => $producto,
                'title' => 'Detalles del Producto',
            ];
            
            $this->render('entity.productos.view_producto', $data);
            
        } catch (Exception $e) {
            header('Location: ' . url('productos.dashboard', ['error' => 'unexpected']));
            exit;
        }
    }
}
