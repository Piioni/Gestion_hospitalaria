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
    $productos = $productoService->getAll();
}

$title = "Dashboard de Productos";
include __DIR__ . '/../../layouts/_header.php';
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Gestión de Productos</h1>
            <p class="lead-text">
                Control y gestión de productos disponibles en el sistema.
            </p>
            <div class="action-buttons">
                <a href="/productos/create" class="btn btn-primary">Crear nuevo producto</a>
            </div>
        </div>

        <?php if (isset($_GET['error'])) :
            if ($_GET['error'] == 'almacen_and_botiquin') : ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> No se puede filtrar por almacén y botiquín al mismo tiempo.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="filter-section card">
            <div class="card-body">
                <h3 class="filter-title">Filtrar productos</h3>
                <form action="" method="GET" class="form-inline filter-form">
                    <div class="form-group">
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
                    <div class="form-group">
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
                    <div class="form-group">
                        <label for="codigo" class="form-label">Codigo:</label>
                        <div class="form-field">
                            <input type="text" name="codigo" id="codigo" class="form-input"
                                   value="<?= isset($filtro_codigo) ? htmlspecialchars($filtro_codigo): '' ?>"
                                   placeholder="Buscar por codigo">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <?php if ($filtros['botiquin'] || $filtros['codigo'] || $filtros['almacen']) : ?>
                            <a href="?" class="btn btn-secondary">Limpiar filtros</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="productos-section">
            <h2 class="section-title">Productos registrados</h2>

            <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <?php if ($filtros['botiquin'] || $filtros['codigo'] || $filtros['almacen'] ) : ?>
                        No hay productos que coincidan con los filtros seleccionados.
                    <?php else: ?>
                        No hay productos registrados en el sistema.
                    <?php endif; ?>
                    <a href="/productos/create" class="btn btn-primary">Crear un producto</a>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
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
                                        <td>
                                            <div class="planta-actions">
                                                <a href="/productos/edit?id=<?= $producto->getId() ?>"
                                                   class="btn btn-sm btn-secondary">
                                                    Editar
                                                </a>
                                                <a href="/productos/view?id=<?= $producto->getId() ?>"
                                                   class="btn btn-sm btn-primary">
                                                    Ver detalles
                                                </a>
                                                <a href="/productos/delete?id=<?= $producto->getId() ?>"
                                                   class="btn btn-sm btn-danger">
                                                    Eliminar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Actualizar automáticamente el formulario cuando cambian los selects
    document.getElementById('almacen').addEventListener('change', function () {
        document.getElementById('botiquin').value = ''; // Limpiar el otro filtro
        this.form.submit();
    });

    document.getElementById('botiquin').addEventListener('change', function () {
        document.getElementById('almacen').value = ''; // Limpiar el otro filtro
        this.form.submit();
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
