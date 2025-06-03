<?php include __DIR__ . '/../../layouts/_header.php'; ?>

<div class="page-section">
    <div class="container">
        <div class="productos-section">
            <?php if (isset($error) && $error === 'almacen_and_botiquin'): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> No se puede filtrar por almacén y botiquín al mismo tiempo.
                </div>
            <?php endif; ?>

            <?php if ($filtrarActivo): ?>
                <div class="filter-section card">
                    <div class="card-body">
                        <h3 class="filter-title">Filtrar productos</h3>
                        <form action="" method="GET" class="filter-form">
                            <input type="hidden" name="filtrar" value="1">
                            <div class="filter-fields">
                                <div class="filter-field">
                                    <label for="codigo" class="form-label">Código:</label>
                                    <div class="form-field">
                                        <input type="text" name="codigo" id="codigo" class="form-input"
                                               value="<?= $filtros['codigo'] ? htmlspecialchars($filtros['codigo']) : '' ?>"
                                               placeholder="Buscar por código">
                                    </div>
                                </div>
                                <div class="filter-field">
                                    <label for="nombre" class="form-label">Nombre:</label>
                                    <div class="form-field">
                                        <input type="text" name="nombre" id="nombre" class="form-input"
                                               value="<?= $filtros['nombre'] ? htmlspecialchars($filtros['nombre']) : '' ?>"
                                               placeholder="Buscar por nombre">
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar
                                </button>
                                <a href="<?= url('productos.dashboard') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar filtros</a>
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
                    <a href="<?= url('productos.create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i>
                        Crear producto</a>
                </div>
            </div>

            <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <?php if ($filtros['codigo'] || $filtros['nombre']) : ?>
                        No hay productos que coincidan con los filtros seleccionados.
                    <?php else: ?>
                        No hay productos registrados en el sistema.
                    <?php endif; ?>
                    <a href="<?= url('productos.create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i>
                        Crear un producto</a>
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
    // Pasar datos de PHP a JavaScript como JSON
    document.addEventListener('DOMContentLoaded', function () {
        const notification = {
            success: <?= isset($success) ? json_encode($success) : 'null' ?>,
            error: <?= isset($error) ? json_encode($error) : 'null' ?>
        };

        // Manejar notificaciones de éxito
        if (notification.success) {
            const messages = {
                'deleted': 'Producto eliminado correctamente.',
                'created': 'Producto creado correctamente.',
                'updated': 'Producto actualizado correctamente.'
            };

            if (messages[notification.success]) {
                ToastSystem.success('Éxito', messages[notification.success], null, {
                    autoClose: true,
                    closeDelay: 5000
                });
            }
        }

        // Manejar notificaciones de error
        if (notification.error ) {
            const errorMessages = {
                'id_invalid': 'ID de producto no válido.',
                'unexpected': 'Ha ocurrido un error inesperado.',
                'producto_not_found': 'Producto no encontrado.'
            };

            const message = errorMessages[notification.error] || <?= isset($error) ? 'decodeURIComponent("' . urlencode(htmlspecialchars($error ?? '')) . '")' : '""' ?>;

            if (message) {
                ToastSystem.danger('Error', message, null, {
                    autoClose: true,
                    closeDelay: 5000
                });
            }
        }
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
