<?php

use model\service\BotiquinService;
use model\service\PlantaService;
use model\service\ProductoService;
use model\service\StockService;

$botiquinService = new BotiquinService();
$plantaService = new PlantaService();
$productoService = new ProductoService();
$stockService = new StockService();

$plantas = $plantaService->getAllPlantas();

// Obtener el filtro de planta desde la URL, si existe
$filtro_plantas = isset($_GET['planta']) ? (int)$_GET['planta'] : null;

// Filtrar los botiquines por planta 
if ($filtro_plantas) {
    $botiquines = $botiquinService->getBotiquinesByPlantaId($filtro_plantas);
} else {
    $botiquines = $botiquinService->getAllBotiquines();
}

$title = "Stock de Botiquines";
include __DIR__ . '/../../layouts/_header.php';
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Gestión de Stock en Botiquines</h1>
            <p class="lead-text">
                Control y visualización del stock de medicamentos en botiquines.
            </p>
        </div>

        <div class="filter-section card">
            <div class="card-body">
                <h3 class="filter-title">Filtrar botiquines</h3>
                <form action="" method="GET" class="form-inline filter-form">
                    <div class="form-group">
                        <label for="planta" class="form-label">Planta:</label>
                        <div class="form-field">
                            <select name="planta" id="planta" class="form-select">
                                <option value="">Todas las plantas</option>
                                <?php foreach ($plantas as $planta): ?>
                                    <option value="<?= $planta->getId() ?>" <?= $filtro_plantas == $planta->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($planta->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <?php if ($filtro_plantas): ?>
                            <a href="?" class="btn btn-secondary">Limpiar filtro</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="botiquines-section">
            <h2 class="section-title">Stock por botiquín</h2>

            <?php if (empty($botiquines)): ?>
                <div class="empty-state">
                    <?php if ($filtro_plantas): ?>
                        No hay botiquines registrados para la planta seleccionada.
                    <?php else: ?>
                        No hay botiquines registrados en el sistema.
                    <?php endif; ?>
                    <a href="/botiquines/create" class="btn btn-primary">Crear un botiquín</a>
                </div>
            <?php else: ?>
                <div class="botiquines-list">
                    <?php foreach ($botiquines as $botiquin):
                        // Obtener la planta asociada
                        try {
                            $planta = $plantaService->getPlantaById($botiquin->getIdPlanta());
                            $plantaNombre = $planta->getNombre();
                        } catch (Exception $e) {
                            $plantaNombre = "Error al cargar la planta";
                        }

                        // Obtener los productos en stock para este botiquín
                        $productosEnStock = $stockService->getStockByBotiquinId($botiquin->getId());
                        ?>
                        <div class="botiquin-card card">
                            <div class="collapsible-header planta-header"
                                 onclick="toggleCollapsible(this, 'botiquin-<?= $botiquin->getId() ?>')">
                                <h3 class="planta-name"><?= htmlspecialchars($botiquin->getNombre()) ?></h3>
                                <span class="badge"><?= count($productosEnStock) ?> productos</span>
                                <span class="collapsible-icon">▼</span>
                            </div>

                            <div id="botiquin-<?= $botiquin->getId() ?>" class="collapsible-content">
                                <div class="card-body">
                                    <div class="botiquin-details">
                                        <p><strong>Ubicación:</strong> <?= htmlspecialchars($plantaNombre) ?></p>
                                        <p><strong>Capacidad:</strong> <?= $botiquin->getCapacidad() ?> medicamentos</p>
                                    </div>

                                    <div class="stock-section">
                                        <h4>Productos en Stock</h4>
                                        
                                        <?php if (empty($productosEnStock)): ?>
                                            <div class="empty-stock">
                                                <p>No hay productos en stock para este botiquín.</p>
                                                <a href="/stock/add?tipo=BOTIQUIN&id_ubicacion=<?= $botiquin->getId() ?>" class="btn btn-sm btn-primary">
                                                    Añadir producto
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Stock Actual</th>
                                                            <th>Stock Mínimo</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($productosEnStock as $stock):
                                                            $producto = $productoService->getProductoById($stock->getIdProducto());
                                                            $estadoClase = ($stock->getCantidad() < $stock->getStockMinimo()) ? 'bajo-stock' : 'stock-ok';
                                                            $estadoTexto = ($stock->getCantidad() < $stock->getStockMinimo()) ? 'Bajo stock' : 'OK';
                                                        ?>
                                                            <tr class="<?= $estadoClase ?>">
                                                                <td><?= htmlspecialchars($producto->getNombre()) ?></td>
                                                                <td><?= $stock->getCantidad() ?></td>
                                                                <td><?= $stock->getStockMinimo() ?></td>
                                                                <td><span class="estado-badge <?= $estadoClase ?>"><?= $estadoTexto ?></span></td>
                                                                <td class="acciones-stock">
                                                                    <a href="/movimientos/create?tipo=consumo&id_stock=<?= $stock->getId() ?>" class="btn btn-sm btn-warning" title="Consumir">
                                                                        <i class="fas fa-minus-circle"></i> Consumir
                                                                    </a>
                                                                    <a href="/movimientos/create?tipo=desecho&id_stock=<?= $stock->getId() ?>" class="btn btn-sm btn-danger" title="Desechar">
                                                                        <i class="fas fa-trash"></i> Desechar
                                                                    </a>
                                                                    <a href="/movimientos/create?tipo=reposicion&id_stock=<?= $stock->getId() ?>" class="btn btn-sm btn-success" title="Reponer">
                                                                        <i class="fas fa-plus-circle"></i> Reponer
                                                                    </a>
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
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleCollapsible(header, contentId) {
        const content = document.getElementById(contentId);
        content.classList.toggle('active');
        header.classList.toggle('active');
    }

    // Actualizar automáticamente el formulario cuando cambia el select
    document.getElementById('planta').addEventListener('change', function () {
        this.form.submit();
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
