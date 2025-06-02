<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title"><?= $navTitle ?></h1>
            <p class="lead-text">
                Control y visualización del stock de medicamentos en botiquines.
            </p>
            <div class="action-buttons">
                <a href="<?= url('botiquines') ?>" class="btn btn-secondary">Ver Botiquines</a>
                <a href="<?= url('stocks') ?>" class="btn btn-secondary">Dashboard de Stock</a>
            </div>
        </div>

        <div class="filter-section card">
            <div class="card-body">
                <h3 class="filter-title">Filtrar botiquines</h3>
                <form action="" method="GET" class="filter-form">
                    <div class="filter-fields">
                        <div class="filter-field">
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
                        <div class="filter-field">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <div class="form-field">
                                <input type="text" name="nombre" id="nombre" class="form-input"
                                       placeholder="Filtrar por nombre de botiquín"
                                       value="<?= isset($filtro_nombre) ? htmlspecialchars($filtro_nombre) : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                        <?php if ($filtro_plantas || $filtro_botiquin || $filtro_nombre): ?>
                            <a href="<?= url('stocks.botiquines') ?>" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Limpiar filtros</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="botiquines-section">
            <h2 class="section-title">Stock por botiquín</h2>

            <?php if (empty($botiquines)): ?>
                <div class="empty-state">
                    <?php if ($filtro_plantas || $filtro_nombre): ?>
                        No hay botiquines que coincidan con los criterios de filtrado.
                    <?php elseif ($filtro_botiquin): ?>
                        No se encontró el botiquín especificado.
                    <?php else: ?>
                        No hay botiquines registrados o asignados en el sistema.
                    <?php endif; ?>
                    <?php if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA'])): ?>
                        <a href="<?= url('botiquines.create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Crear un botiquín</a>
                    <?php endif; ?>
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
                        
                        // Determinar si este botiquín es el seleccionado para mantenerlo expandido
                        $isSelected = ($filtro_botiquin && $filtro_botiquin == $botiquin->getId()) ? true : false;
                    ?>
                        <div class="botiquin-card card">
                            <div class="collapsible-header botiquin-header <?= $isSelected ? 'active' : '' ?>"
                                 onclick="toggleCollapsible(this, 'botiquin-<?= $botiquin->getId() ?>')">
                                <h3 class="botiquin-name"><?= htmlspecialchars($botiquin->getNombre()) ?></h3>
                                <span class="badge"><?= count($productosEnStock) ?> productos</span>
                                <span class="collapsible-icon">▼</span>
                            </div>

                            <div id="botiquin-<?= $botiquin->getId() ?>" class="collapsible-content <?= $isSelected ? 'active' : '' ?>">
                                <div class="card-body">
                                    <div class="botiquin-details">
                                        <p><strong>Ubicación:</strong> <?= htmlspecialchars($plantaNombre) ?></p>
                                        <p><strong>Capacidad:</strong> <?= $botiquin->getCapacidad() ?> medicamentos</p>
                                        <p><strong>Productos en stock:</strong> <?= count($productosEnStock) ?></p>
                                    </div>

                                    <div class="stock-section">
                                        <h4>Productos en Stock</h4>
                                        
                                        <?php if (empty($productosEnStock)): ?>
                                            <div class="empty-stock">
                                                <p>No hay productos en stock para este botiquín.</p>
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
                                                                    <a href="#" class="btn btn-sm btn-warning" title="Consumir">
                                                                        <i class="bi bi-dash-circle"></i> Consumir
                                                                    </a>
                                                                    <a href="#" class="btn btn-sm btn-danger" title="Desechar">
                                                                        <i class="bi bi-trash"></i> Desechar
                                                                    </a>
                                                                    <a href="#" class="btn btn-sm btn-success" title="Reponer">
                                                                        <i class="bi bi-plus-circle"></i> Reponer
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
        document.getElementById('planta').addEventListener('change', function () {
            this.form.submit();
        });
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
