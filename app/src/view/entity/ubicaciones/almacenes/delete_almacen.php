<?php
include __DIR__ . "/../../../layouts/_header.php";
?>
    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content text-center">
                    <h1 class="page-title">Confirmar Eliminación</h1>
                    <p class="lead-text">¿Estás seguro de que deseas eliminar este almacén?</p>
                </div>
            </div>

            <div class="almacen-delete-container">
                <div class="almacen-delete-card">
                    <div class="card-header">
                        <h3><i class="bi bi-exclamation-triangle-fill"></i> Información del Almacén</h3>
                    </div>
                    <div class="card-body">
                        <div class="almacen-info-card">
                            <div class="almacen-info-header">
                                <div class="almacen-icon">
                                    <i class="bi <?= $almacen->getTipo() === 'GENERAL' ? 'bi-building' : 'bi-box-seam' ?>"></i>
                                </div>
                                <h2 class="almacen-name"><?= htmlspecialchars($almacen->getNombre()) ?></h2>
                            </div>
                            <div class="almacen-info-details">
                                <div class="info-group">
                                    <span class="info-label">Tipo:</span>
                                    <span class="info-value"><?= htmlspecialchars($almacen->getTipo()) ?></span>
                                </div>
                                <div class="info-group">
                                    <span class="info-label">Hospital:</span>
                                    <span class="info-value"><?= htmlspecialchars($hospital->getNombre()) ?></span>
                                </div>
                                <div class="info-group">
                                    <span class="info-label">Planta:</span>
                                    <span class="info-value"><?= htmlspecialchars($planta ? $planta->getNombre() : 'N/A'  ) ?></span>
                                </div>
                                <div class="info-group">
                                    <span class="info-label">Nº. Productos</span>
                                    <span class="info-value"><?= $numProductos ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons-row mt-4">
                            <a href="<?= url('almacenes') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>

                            <?php if ($tieneStock): ?>
                                <div class="action-buttons">
                                    <a href="<?= url('movimientos.create', ['tipo' => 'TRASLADO', 'id_origen' => $almacen->getId(), 'origen_tipo' => 'planta', 'origen_hospital' => $almacen->getIdHospital(), 'origen_planta' => $almacen->getIdPlanta()]) ?>"
                                       class="btn btn-warning">
                                        <i class="bi bi-truck"></i> Trasladar Productos
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="<?= url('almacenes.delete', ['id_almacen' => $almacen->getId(), 'confirm' => 1]) ?>"
                                   class="btn btn-danger"
                                   id="confirmDelete">
                                    <i class="bi bi-trash"></i> Confirmar Eliminación
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mostrar un toast de advertencia sobre la eliminación
            ToastSystem.warning(
                'Advertencia',
                'Esta acción eliminará el almacén y todos sus registros asociados. Esta acción no se puede deshacer.',
                null,
                {autoClose: false}
            );

            <?php if ($error): ?>
            // Mostrar error si existe
            ToastSystem.danger(
                'Error al eliminar',
                '<?= htmlspecialchars($error) ?>',
                null,
                {autoClose: false}
            );
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
