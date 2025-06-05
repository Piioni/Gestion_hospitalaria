<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Dashboard de Stock</h1>
            <p class="lead-text">
                Gestión y visualización del inventario en almacenes y botiquines.
            </p>
        </div>

        <div class="cards-container movement-cards">
            <div class="card dashboard-card movement-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-building"></i> Stock en Almacenes</h3>
                    <p class="card-text">
                        Visualice y gestione el stock disponible en los almacenes del sistema.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('stocks.almacenes') ?>" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> Ver Stock de Almacenes
                        </a>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card movement-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-briefcase-medical"></i> Stock en Botiquines</h3>
                    <p class="card-text">
                        Visualice y gestione el stock disponible en los botiquines de las plantas.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('stocks.botiquines') ?>" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> Ver Stock de Botiquines
                        </a>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card movement-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-exclamation-triangle"></i> Alertas de Stock</h3>
                    <p class="card-text">
                        Productos con niveles de stock por debajo del mínimo establecido.
                    </p>
                    <div class="card-actions">
                        <button type="button" class="btn btn-warning" id="toggleAlertas">
                            <i class="bi bi-bell"></i> Ver Alertas de Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="alertasContainer" class="mt-4" style="display:none;">
            <div class="section-header">
                <h2 class="section-title">Productos con Stock Bajo</h2>
                <p class="section-description">
                    Listado de productos que necesitan reposición urgente.
                </p>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if(empty($productosStockBajo)): ?>
                        <div class="empty-state">
                            <i class="bi bi-check-circle" style="font-size: 3rem; color: var(--accent-color);"></i>
                            <div>
                                <h3>Sin alertas de stock</h3>
                                <p>No hay productos con stock bajo en este momento.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Ubicación</th>
                                        <th>Tipo</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($productosStockBajo as $producto): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($producto['nombre_producto']) ?></td>
                                            <td>
                                                <?php if($producto['tipo_ubicacion'] === 'ALMACEN'): ?>
                                                    <?= htmlspecialchars($producto['nombre_almacen'] ?? 'Desconocido') ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($producto['nombre_botiquin'] ?? 'Desconocido') ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $producto['tipo_ubicacion'] === 'ALMACEN' ? 'Almacén' : 'Botiquín' ?></td>
                                            <td class="text-danger"><strong><?= $producto['cantidad'] ?></strong></td>
                                            <td><?= $producto['cantidad_minima'] ?></td>
                                            <td>
                                                <?php if($producto['tipo_ubicacion'] === 'BOTIQUIN'): ?>
                                                    <a href="<?= url('reposiciones.create', ['id_botiquin' => $producto['id_botiquin'], 'id_producto' => $producto['id_producto']]) ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-plus-circle"></i> Reponer
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= url('movimientos.create', ['tipo' => 'entrada', 'id_stock' => $producto['id_stock'], 'tipo_ubicacion' => 'almacen']) ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-plus-circle"></i> Reponer
                                                    </a>
                                                <?php endif; ?>
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

        <div class="section-header mt-5 mb-1">
            <h2 class="section-title">Acciones Rápidas</h2>
            <p class="section-description">
                Gestione movimientos de stock, entradas y reposiciones de productos.
            </p>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="action-buttons-row">
                    <a href="<?= url('movimientos.create', ['tipo' => 'entrada']) ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Registrar Entrada
                    </a>
                    <a href="<?= url('movimientos.create', ['tipo' => 'consumo']) ?>" class="btn btn-warning">
                        <i class="bi bi-dash-circle"></i> Registrar Consumo
                    </a>
                    <a href="<?= url('movimientos.create', ['tipo' => 'traslado']) ?>" class="btn btn-info">
                        <i class="bi bi-arrow-left-right"></i> Registrar Traslado
                    </a>
                    <a href="<?= url('reposiciones.create') ?>" class="btn btn-primary">
                        <i class="bi bi-briefcase-medical"></i> Solicitar Reposición
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleAlertas = document.getElementById('toggleAlertas');
        const alertasContainer = document.getElementById('alertasContainer');
        
        toggleAlertas.addEventListener('click', function() {
            if (alertasContainer.style.display === 'none') {
                alertasContainer.style.display = 'block';
                toggleAlertas.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar Alertas de Stock';
            } else {
                alertasContainer.style.display = 'none';
                toggleAlertas.innerHTML = '<i class="bi bi-bell"></i> Ver Alertas de Stock';
            }
        });
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
