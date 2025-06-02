<?php

use model\service\ProductoService;
use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\PlantaService;

$productoService = new ProductoService();
$almacenService = new AlmacenService();
$botiquinService = new BotiquinService();
$plantaService = new PlantaService();

// Obtener listas para los filtros
$almacenes = $almacenService->getAllAlmacenes();
$botiquines = $botiquinService->getAllBotiquines();

// Determinar qué filtros están activos
$filtros = [
    'codigo' => isset($_GET['codigo']) ? trim($_GET['codigo']) : null,
    'almacen' => isset($_GET['almacen']) ? (int)$_GET['almacen'] : null,
    'botiquin' => isset($_GET['botiquin']) ? (int)$_GET['botiquin'] : null
];

// Determinar si el filtro está activo
$filtrarActivo = isset($_GET['filtrar']) || $filtros['codigo'] || $filtros['almacen'] || $filtros['botiquin'];

// Verificar si se ha enviado un mensaje de éxito o error
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

// Aplicar filtros a la consulta de productos
if ($filtros['almacen'] && $filtros['botiquin']) {
    header("Location: /productos/dashboard?error=almacen_and_botiquin");
    exit();
}

// Aplicar la estrategia de filtrado según filtros activos
if ($filtros['codigo'] && $filtros['almacen']) {
    $productos = $productoService->getProductosByCodigoAndAlmacen($filtros['codigo'], $filtros['almacen']);
} elseif ($filtros['codigo'] && $filtros['botiquin']) {
    $productos = $productoService->getProductosByCodigoAndBotiquin($filtros['codigo'], $filtros['botiquin']);
} elseif ($filtros['codigo']) {
    $productos = $productoService->getProductosByCodigo($filtros['codigo']);
} elseif ($filtros['almacen']) {
    $productos = $productoService->getProductosByAlmacen($filtros['almacen']);
} elseif ($filtros['botiquin']) {
    $productos = $productoService->getProductosByBotiquin($filtros['botiquin']);
} else {
    $productos = $productoService->getAllProducts();
}

$title = "Sistema de Gestión Hospitalaria";
$navTitle = "Gestión de Productos";
$scripts = "toasts.js";
include __DIR__ . '/../../layouts/_header.php';
?>

<div class="page-section">
    <div class="container">
        <div class="productos-section">
            <?php if (isset($_GET['error'])) :
                if ($_GET['error'] == 'almacen_and_botiquin') : ?>
                    <div class="alert alert-danger">
                        <strong>Error:</strong> No se puede filtrar por almacén y botiquín al mismo tiempo.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($filtrarActivo): ?>
                <div class="filter-section card">
                    <div class="card-body">
                        <h3 class="filter-title">Filtrar productos</h3>
                        <form action="" method="GET" class="filter-form">
                            <input type="hidden" name="filtrar" value="1">
                            <div class="filter-fields">
                                <div class="filter-field">
                                    <label for="almacen" class="form-label">Almacén:</label>
                                    <div class="form-field">
                                        <select name="almacen" id="almacen" class="form-select">
                                            <option value="">Todos los almacenes</option>
                                            <?php foreach ($almacenes as $almacen): ?>
                                                <option value="<?= $almacen->getId() ?>" <?= $filtros['almacen'] == $almacen->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($almacen->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-field">
                                    <label for="botiquin" class="form-label">Botiquín:</label>
                                    <div class="form-field">
                                        <select name="botiquin" id="botiquin" class="form-select">
                                            <option value="">Todos los botiquines</option>
                                            <?php foreach ($botiquines as $botiquin):
                                                try {
                                                    $planta = $plantaService->getPlantaById($botiquin->getIdPlanta());
                                                    $plantaNombre = $planta->getNombre();
                                                } catch (Exception $e) {
                                                    $plantaNombre = "Error";
                                                }
                                                ?>
                                                <option value="<?= $botiquin->getId() ?>" <?= $filtros['botiquin'] == $botiquin->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($botiquin->getNombre()) ?>
                                                    (<?= htmlspecialchars($plantaNombre) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-field">
                                    <label for="codigo" class="form-label">Código:</label>
                                    <div class="form-field">
                                        <input type="text" name="codigo" id="codigo" class="form-input"
                                               value="<?= $filtros['codigo'] ? htmlspecialchars($filtros['codigo']) : '' ?>"
                                               placeholder="Buscar por código">
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                                <a href="<?= url('productos.dashboard') ?>" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Limpiar filtros</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                <h2 class="section-title">Productos registrados</h2>
                <div class="action-buttons">
                    <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                        <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                    </a>
                    <a href="<?= url('productos.create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Crear producto</a>
                </div>
            </div>

            <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <?php if ($filtros['botiquin'] || $filtros['codigo'] || $filtros['almacen'] ) : ?>
                        No hay productos que coincidan con los filtros seleccionados.
                    <?php else: ?>
                        No hay productos registrados en el sistema.
                    <?php endif; ?>
                    <a href="<?= url('productos.create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Crear un producto</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Unidad de medida</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?= htmlspecialchars($producto->getCodigo()) ?></td>
                                <td><?= htmlspecialchars($producto->getNombre()) ?></td>
                                <td>
                                    <?= htmlspecialchars($producto->getDescripcion()) ?>
                                </td>
                                <td><?= htmlspecialchars($producto->getUnidadMedida()) ?></td>
                                <td class="actions-column">
                                    <div class="btn-container">
                                        <a href="<?= url('productos.edit', ['id' => $producto->getId()]) ?>"
                                           class="btn btn-sm btn-secondary">
                                           <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="<?= url('productos.view', ['id' => $producto->getId()]) ?>"
                                           class="btn btn-sm btn-primary">
                                           <i class="bi bi-eye"></i> Ver detalles
                                        </a>
                                        <a href="<?= url('productos.delete', ['id' => $producto->getId()]) ?>"
                                           class="btn btn-sm btn-danger">
                                           <i class="bi bi-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Mostrar notificaciones toast según los parámetros de la URL
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($success): ?>
            <?php if ($success === 'deleted'): ?>
                ToastSystem.success('Éxito', 'Producto eliminado correctamente.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($success === 'created'): ?>
                ToastSystem.success('Éxito', 'Producto creado correctamente.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($success === 'updated'): ?>
                ToastSystem.success('Éxito', 'Producto actualizado correctamente.', null, {autoClose: true, closeDelay: 5000});
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($error && $error !== 'almacen_and_botiquin'): ?>
            <?php if ($error === 'id_invalid'): ?>
                ToastSystem.danger('Error', 'ID de producto no válido.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($error === 'unexpected'): ?>
                ToastSystem.danger('Error', 'Ha ocurrido un error inesperado.', null, {autoClose: true, closeDelay: 5000});
            <?php else: ?>
                ToastSystem.danger('Error', '<?= htmlspecialchars(urldecode($error)) ?>', null, {autoClose: true, closeDelay: 5000});
            <?php endif; ?>
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
