<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="almacenes-section">
            <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                <h2 class="section-title">Stock de almacenes</h2>
                <div class="action-buttons">
                    <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                        <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                    </a>
                    <a href="<?= url('almacenes') ?>" class="btn btn-secondary">Ver Almacenes</a>
                    <a href="<?= url('stocks') ?>" class="btn btn-secondary">Dashboard de Stock</a>
                </div>
            </div>

            <?php if ($filtrarActivo): ?>
                <div class="filter-section card mb-4">
                    <div class="card-body">
                        <h3 class="filter-title">Filtrar almacenes</h3>
                        <form action="" method="GET" class="filter-form">
                            <input type="hidden" name="filtrar" value="1">
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

                                <div class="filter-field">
                                    <label for="nombre" class="form-label">Nombre:</label>
                                    <div class="form-field">
                                        <input type="text" name="nombre" id="nombre" class="form-input"
                                               placeholder="Filtrar por nombre de almacén"
                                               value="<?= isset($filtro_nombre) ? htmlspecialchars($filtro_nombre) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                                <?php if ($filtro_hospital || $filtro_tipo || $filtro_almacen || $filtro_nombre): ?>
                                    <a href="<?= url('stocks.almacenes') ?>" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Limpiar filtros</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($almacenes)): ?>
                <div class="empty-state">
                    <?php if ($filtro_hospital || $filtro_tipo || $filtro_almacen || $filtro_nombre): ?>
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
                        
                        // Determinar si este almacén es el seleccionado para mantenerlo expandido
                        $isSelected = $filtro_almacen && $filtro_almacen == $almacen->getId();
                        
                        // Obtener productos en stock para este almacén
                        $productosEnStock = $stockService->getStockByAlmacenId($almacen->getId());
                    ?>
                        <div class="almacen-card card">
                            <div class="collapsible <?= $isSelected ? 'active' : '' ?>"
                                 onclick="toggleCollapsible(this, 'almacen-<?= $almacen->getId() ?>')">
                                <div class="collapsible-left">
                                    <h3 class="collapsible-name"><?= htmlspecialchars($almacen->getNombre()) ?></h3>
                                </div>
                                <div class="collapsible-right">
                                    <span class="badge"><?= count($productosEnStock) ?> productos</span>
                                    <span class="collapsible-icon">▼</span>
                                </div>
                            </div>

                            <div id="almacen-<?= $almacen->getId() ?>" class="collapsible-content <?= $isSelected ? 'active' : '' ?>">
                                <div class="card-body">
                                    <div class="almacen-details">
                                        <p><strong>Hospital:</strong> <?= htmlspecialchars($hospitalNombre) ?></p>
                                        <?php if ($almacen->getTipo() === 'PLANTA'): ?>
                                            <p><strong>Planta:</strong> <?= htmlspecialchars($plantaNombre) ?></p>
                                        <?php endif; ?>
                                        <p><strong>Tipo:</strong> <?= htmlspecialchars($almacen->getTipo()) ?></p>
                                    </div>

                                    <hr class="divider">

                                    <div class="stock-section">
                                        <h4 class="subsection-title">Productos en Stock</h4>
                                        
                                        <?php if (empty($productosEnStock)): ?>
                                            <div class="empty-stock">
                                                <p>No hay productos en stock para este almacén.</p>
                                                <a href="<?= url('movimientos.create', ['tipo' => 'entrada', 'id_almacen' => $almacen->getId()]) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-plus-circle"></i> Añadir producto
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
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($productosEnStock as $stock):
                                                            $producto = $productoService->getProductoById($stock->getIdProducto());
                                                            $estadoClase = ($stock->getCantidad() < $stock->getStockMinimo()) ? 'bajo-stock' : 'stock-ok';
                                                            $estadoTexto = ($stock->getCantidad() < $stock->getStockMinimo()) ? 'Bajo stock' : 'OK';
                                                        ?>
                                                            <tr>
                                                                <td><?= $producto ? htmlspecialchars($producto->getNombre()) : 'Producto no encontrado' ?></td>
                                                                <td class="<?= $estadoClase ?>"><?= $stock->getCantidad() ?></td>
                                                                <td><?= $stock->getStockMinimo() ?></td>
                                                                <td class="actions-column">
                                                                    <div class="btn-container">
                                                                        <a href="<?= url('movimientos.create', ['tipo' => 'consumo', 'id_stock' => $stock->getId(), 'tipo_ubicacion' => 'almacen']) ?>"
                                                                           class="btn btn-sm btn-warning" title="Consumir">
                                                                            <i class="bi bi-dash-circle"></i> Consumir
                                                                        </a>
                                                                        <a href="<?= url('movimientos.create', ['tipo' => 'entrada', 'id_stock' => $stock->getId(), 'tipo_ubicacion' => 'almacen']) ?>"
                                                                           class="btn btn-sm btn-success" title="Reponer">
                                                                            <i class="bi bi-plus-circle"></i> Reponer
                                                                        </a>
                                                                        <a href="<?= url('movimientos.create', ['tipo' => 'traslado', 'id_stock' => $stock->getId(), 'tipo_ubicacion' => 'almacen']) ?>"
                                                                           class="btn btn-sm btn-info" title="Trasladar">
                                                                            <i class="bi bi-arrow-left-right"></i> Trasladar
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
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('hospital').addEventListener('change', function () {
            this.form.submit();
        });
        document.getElementById('tipo').addEventListener('change', function () {
            this.form.submit();
        });
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>