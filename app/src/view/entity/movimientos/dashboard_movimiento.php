<?php
include __DIR__ . "/../../layouts/_header.php";
$pendientes = $pendientes ?? [];
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Gestión de Movimientos</h1>
            <p class="lead-text">
                Control de movimientos de productos entre almacenes y registro de nuevas entradas.
            </p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <div>
                    <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="cards-container movement-cards">
            <div class="card dashboard-card movement-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-arrow-left-right"></i> Traslado entre Almacenes</h3>
                    <p class="card-text">
                        Realice movimientos de productos entre diferentes almacenes del sistema.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('movimientos.create') ?>?tipo=TRASLADO" class="btn btn-primary">
                            <i class="bi bi-box-arrow-right"></i> Solicitar Traslado
                        </a>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card movement-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-box-arrow-in-down"></i> Entrada de Productos</h3>
                    <p class="card-text">
                        Registre la entrada de nuevos productos a cualquier almacén del sistema.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('movimientos.create') ?>?tipo=ENTRADA" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Registrar Entrada
                        </a>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card movement-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-clock-history"></i> Historial de Movimientos</h3>
                    <p class="card-text">
                        Consulte el historial completo de movimientos con filtros avanzados.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('movimientos.list') ?>" class="btn btn-info">
                            <i class="bi bi-search"></i> Ver Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-header mt-5">
            <h2 class="section-title">Movimientos Pendientes</h2>
            <p class="section-description">
                Listado de movimientos que requieren confirmación o pueden ser cancelados.
            </p>
        </div>

        <?php if (empty($pendientes)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox" style="font-size: 3rem; color: var(--light-text);"></i>
                <div>
                    <h3>No hay movimientos pendientes</h3>
                    <p>Actualmente no existen movimientos que requieran confirmación.</p>
                </div>
                <div class="action-buttons">
                    <a href="<?= url('movimientos.create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nuevo Movimiento
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendientes as $movimiento): ?>
                                    <tr>
                                        <td><?= $movimiento['id_movimiento'] ?></td>
                                        <td>
                                            <span class="estado-badge <?= $movimiento['tipo_movimiento'] === 'TRASLADO' ? 'bajo-stock' : 'stock-ok' ?>">
                                                <?= $movimiento['tipo_movimiento'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($movimiento['nombre_producto']) ?></td>
                                        <td><?= $movimiento['cantidad'] ?></td>
                                        <td><?= $movimiento['origen_nombre'] ? htmlspecialchars($movimiento['origen_nombre']) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($movimiento['destino_nombre']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) ?></td>
                                        <td>
                                            <div class="acciones-stock">
                                                <a href="<?= url('movimientos.edit') ?>?id=<?= $movimiento['id_movimiento'] ?>" class="btn btn-secondary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= url('movimientos.complete') ?>?id=<?= $movimiento['id_movimiento'] ?>" 
                                                   onclick="return confirm('¿Está seguro que desea confirmar este movimiento?')" 
                                                   class="btn btn-success" title="Confirmar">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <a href="<?= url('movimientos.cancel') ?>?id=<?= $movimiento['id_movimiento'] ?>" 
                                                   class="btn btn-danger" title="Cancelar">
                                                    <i class="bi bi-x-circle"></i>
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

            <div class="mt-4 text-center">
                <a href="<?= url('movimientos.list') ?>" class="btn btn-info">
                    <i class="bi bi-clock-history"></i> Ver Historial Completo
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
