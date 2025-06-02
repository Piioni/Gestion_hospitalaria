<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title"><?= $navTitle ?></h1>
            <p class="lead-text">
                Control y visualización del stock de productos en los almacenes.
            </p>
            <div class="action-buttons">
                <a href="<?= url('almacenes') ?>" class="btn btn-secondary">Ver Almacenes</a>
                <a href="<?= url('stocks') ?>" class="btn btn-secondary">Dashboard de Stock</a>
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

        <div class="almacenes-section">
            <h2 class="section-title">Stock por almacén</h2>

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

                        // Obtener los productos en stock para este almacén
                        $productosEnStock = $stockService->getStockByAlmacenId($almacen->getId());
                        
                        // Determinar si este almacén es el seleccionado para mantenerlo expandido
                        $isSelected = ($filtro_almacen && $filtro_almacen == $almacen->getId()) ? true : false;
                    ?>
                        <div class="almacen-card card">
                            <div class="collapsible-header almacen-header <?= $isSelected ? 'active' : '' ?>"
                                 onclick="toggleCollapsible(this, 'almacen-<?= $almacen->getId() ?>')">
                                <h3 class="almacen-name"><?= htmlspecialchars($almacen->getNombre()) ?></h3>
                                <span class="badge"><?= count($productosEnStock) ?> productos</span>
                                <span class="collapsible-icon">▼</span>
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

                                    <div class="stock-section">
                                        <h4>Productos en Stock</h4>
                                        
                                        <?php if (empty($productosEnStock)): ?>
                                            <div class="empty-stock">
                                                <p>No hay productos en stock para este almacén.</p>
                                                <a href="#" class="btn btn-sm btn-primary">
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
                                                                <td><?= $producto ? htmlspecialchars($producto->getNombre()) : 'Producto no encontrado' ?></td>
                                                                <td><?= $stock->getCantidad() ?></td>
                                                                <td><?= $stock->getStockMinimo() ?></td>
                                                                <td><span class="estado-badge <?= $estadoClase ?>"><?= $estadoTexto ?></span></td>
                                                                <td class="acciones-stock">
                                                                    <a href="#" class="btn btn-sm btn-warning" title="Retirar">
                                                                        <i class="bi bi-dash-circle"></i> Retirar
                                                                    </a>
                                                                    <a href="#" class="btn btn-sm btn-success" title="Añadir">
                                                                        <i class="bi bi-plus-circle"></i> Añadir
                                                                    </a>
                                                                    <a href="#" class="btn btn-sm btn-info" title="Trasladar">
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
