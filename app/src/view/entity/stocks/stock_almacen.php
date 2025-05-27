<?php

use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\ProductoService;
use model\service\StockService;

$almacenService = new AlmacenService();
$hospitalService = new HospitalService();
$plantaService = new PlantaService();
$productoService = new ProductoService();
$stockService = new StockService();

// Obtener los filtros desde la URL
$filtro_hospital = isset($_GET['hospital']) ? (int)$_GET['hospital'] : null;
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
$filtro_almacen = isset($_GET['id_almacen']) ? (int)$_GET['id_almacen'] : null;

// Obtener la lista de hospitales para el filtro
$hospitals = $hospitalService->getAllHospitals();

// Filtrar los almacenes según los parámetros
if ($filtro_almacen) {
    // Si se especificó un almacén específico
    $almacen = $almacenService->getAlmacenById($filtro_almacen);
    if ($almacen) {
        $almacenes = [$almacen];
        // Obtenemos el hospital para preseleccionarlo en el filtro
        $filtro_hospital = $almacen->getIdHospital();
        $filtro_tipo = $almacen->getTipo();
    } else {
        $almacenes = [];
    }
} else {
    // Obtener todos los almacenes
    $almacenes = $almacenService->getAllAlmacenes();
    
    // Aplicar filtros si existen
    if ($filtro_hospital) {
        $almacenes = array_filter($almacenes, function($almacen) use ($filtro_hospital) {
            return $almacen->getIdHospital() == $filtro_hospital;
        });
    }
    
    if ($filtro_tipo) {
        $almacenes = array_filter($almacenes, function($almacen) use ($filtro_tipo) {
            return $almacen->getTipo() == $filtro_tipo;
        });
    }
}

$title = "Stock de Almacenes";
include __DIR__ . '/../../layouts/_header.php';
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Gestión de Stock en Almacenes</h1>
            <p class="lead-text">
                Control y visualización del stock de productos en los almacenes.
            </p>
            <div class="action-buttons">
                <a href="<?= url('almacenes.dashboard') ?>" class="btn btn-secondary">Volver a Almacenes</a>
            </div>
        </div>

        <div class="filter-section card">
            <div class="card-body">
                <h3 class="filter-title">Filtrar almacenes</h3>
                <form action="" method="GET" class="filter-form">
                    <div class="filter-fields">
                        <div class="filter-field">
                            <label for="hospital" class="form-label">Hospital:</label>
                            <div class="form-field">
                                <select name="hospital" id="hospital" class="form-select">
                                    <option value="">Todos los hospitales</option>
                                    <?php foreach ($hospitals as $hospital): ?>
                                        <option value="<?= $hospital->getId() ?>" <?= $filtro_hospital == $hospital->getId() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hospital->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="filter-field">
                            <label for="tipo" class="form-label">Tipo:</label>
                            <div class="form-field">
                                <select name="tipo" id="tipo" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    <option value="GENERAL" <?= $filtro_tipo === 'GENERAL' ? 'selected' : '' ?>>General</option>
                                    <option value="PLANTA" <?= $filtro_tipo === 'PLANTA' ? 'selected' : '' ?>>Planta</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <?php if ($filtro_hospital || $filtro_tipo || $filtro_almacen): ?>
                            <a href="<?= url('stocks.almacenes') ?>" class="btn btn-secondary">Limpiar filtros</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="almacenes-section">
            <h2 class="section-title">Stock por almacén</h2>

            <?php if (empty($almacenes)): ?>
                <div class="empty-state">
                    <?php if ($filtro_hospital || $filtro_tipo || $filtro_almacen): ?>
                        No hay almacenes que coincidan con los criterios de filtrado.
                    <?php else: ?>
                        No hay almacenes registrados en el sistema.
                    <?php endif; ?>
                    <a href="<?= url('almacenes.create') ?>" class="btn btn-primary">Crear un almacén</a>
                </div>
            <?php else: ?>
                <div class="almacenes-list">
                    <?php foreach ($almacenes as $almacen):
                        // Obtener información relacionada
                        $hospital = $hospitalService->getHospitalById($almacen->getIdHospital());
                        $hospitalNombre = $hospital ? $hospital->getNombre() : "No encontrado";
                        
                        $plantaNombre = "N/A";
                        if ($almacen->getIdPlanta()) {
                            $planta = $plantaService->getPlantaById($almacen->getIdPlanta());
                            $plantaNombre = $planta ? $planta->getNombre() : "No encontrada";
                        }

                        // Obtener los productos en stock para este almacén
                        // Esta es una simulación, necesitaría implementarse la funcionalidad real
                        $productosEnStock = []; // Esto debería ser reemplazado por la llamada real al servicio
                    ?>
                        <div class="almacen-card card">
                            <div class="collapsible-header almacen-header"
                                 onclick="toggleCollapsible(this, 'almacen-<?= $almacen->getId() ?>')">
                                <h3 class="almacen-name"><?= htmlspecialchars($almacen->getNombre()) ?></h3>
                                <span class="badge"><?= count($productosEnStock) ?> productos</span>
                                <span class="collapsible-icon">▼</span>
                            </div>

                            <div id="almacen-<?= $almacen->getId() ?>" class="collapsible-content <?= $filtro_almacen == $almacen->getId() ? 'active' : '' ?>">
                                <div class="card-body">
                                    <div class="almacen-details">
                                        <p><strong>Hospital:</strong> <?= htmlspecialchars($hospitalNombre) ?></p>
                                        <?php if ($almacen->getTipo() === 'PLANTA'): ?>
                                            <p><strong>Planta:</strong> <?= htmlspecialchars($plantaNombre) ?></p>
                                        <?php endif; ?>
                                        <p><strong>Tipo:</strong> <?= htmlspecialchars($almacen->getTipo()) ?></p>
                                    </div>

                                    <div class="stock-section">
                                        <h4>Productos en Stock</h4>
                                        
                                        <?php if (empty($productosEnStock)): ?>
                                            <div class="empty-stock">
                                                <p>No hay productos en stock para este almacén.</p>
                                                <a href="/stock/add?tipo=ALMACEN&id_ubicacion=<?= $almacen->getId() ?>" class="btn btn-sm btn-primary">
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
                                                                    <a href="/movimientos/create?tipo=salida&id_stock=<?= $stock->getId() ?>" class="btn btn-sm btn-warning" title="Retirar">
                                                                        <i class="bi bi-dash-circle"></i> Retirar
                                                                    </a>
                                                                    <a href="/movimientos/create?tipo=entrada&id_stock=<?= $stock->getId() ?>" class="btn btn-sm btn-success" title="Añadir">
                                                                        <i class="bi bi-plus-circle"></i> Añadir
                                                                    </a>
                                                                    <a href="/movimientos/create?tipo=traslado&id_stock=<?= $stock->getId() ?>" class="btn btn-sm btn-info" title="Trasladar">
                                                                        <i class="bi bi-arrow-left-right"></i> Trasladar
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
    document.getElementById('hospital').addEventListener('change', function () {
        this.form.submit();
    });
    document.getElementById('tipo').addEventListener('change', function () {
        this.form.submit();
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
