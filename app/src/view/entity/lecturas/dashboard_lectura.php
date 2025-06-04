<?php
include __DIR__ . "/../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="lecturas-section">
                <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                    <h2 class="section-title">Historial de Lecturas</h2>
                    <div class="action-buttons">
                        <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                        </a>
                        <a href="<?= url('lecturas.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Registrar lectura
                        </a>
                    </div>
                </div>

                <?php if ($filtrarActivo): ?>
                    <div class="filter-section card mb-4">
                        <div class="card-body">
                            <h3 class="filter-title">Filtrar lecturas</h3>
                            <form action="" method="GET" class="filter-form">
                                <input type="hidden" name="filtrar" value="1">
                                <div class="filter-fields">
                                    <div class="filter-field">
                                        <label for="producto" class="form-label">Producto:</label>
                                        <div class="form-field">
                                            <select name="producto" id="producto" class="form-select">
                                                <option value="">Todos los productos</option>
                                                <?php foreach ($productos as $producto): ?>
                                                    <option value="<?= $producto->getId() ?>"
                                                        <?= $filtroProducto == $producto->getId() ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($producto->getNombre()) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="filter-field">
                                        <label for="botiquin" class="form-label">Botiquín:</label>
                                        <div class="form-field">
                                            <select name="botiquin" id="botiquin" class="form-select">
                                                <option value="">Todos los botiquines</option>
                                                <?php foreach ($botiquines as $botiquin): ?>
                                                    <option value="<?= $botiquin->getId() ?>"
                                                        <?= $filtroBotiquin == $botiquin->getId() ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($botiquin->getNombre()) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="filter-field">
                                        <label for="usuario" class="form-label">Usuario:</label>
                                        <div class="form-field">
                                            <select name="usuario" id="usuario" class="form-select">
                                                <option value="">Todos los usuarios</option>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                    <option value="<?= $usuario['id_usuario'] ?>" 
                                                        <?= $filtroUsuario == $usuario['id_usuario'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($usuario['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Filtrar
                                    </button>
                                    <a href="<?= url('lecturas') ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Limpiar filtros
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($lecturas)): ?>
                    <div class="empty-state">
                        <?php if ($filtroProducto || $filtroBotiquin || $filtroUsuario): ?>
                            No hay lecturas que coincidan con los criterios de filtrado.
                        <?php else: ?>
                            No hay lecturas registradas en el sistema.
                        <?php endif; ?>
                        <a href="<?= url('lecturas.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Registrar una lectura
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Botiquín</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($lecturas as $lectura): ?>
                                <tr>
                                    <td><?= $lectura->getId() ?></td>
                                    <td><?= htmlspecialchars($lectura->producto_nombre ?? 'Desconocido') ?></td>
                                    <td><?= htmlspecialchars($lectura->botiquin_nombre ?? 'Desconocido') ?></td>
                                    <td><?= $lectura->getCantidad() ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($lectura->getFechaLectura())) ?></td>
                                    <td><?= htmlspecialchars($lectura->usuario_nombre ?? 'Sistema') ?></td>
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
        // Mostrar notificaciones Toast
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($success)): ?>
            <?php if ($success === 'created'): ?>
            ToastSystem.success(
                'Lectura registrada',
                'La lectura ha sido registrada correctamente.',
                null,
                {autoClose: true, closeDelay: 5000}
            );
            <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <?php if ($error === 'unexpected'): ?>
            ToastSystem.danger(
                'Error inesperado',
                'Ha ocurrido un error inesperado al procesar su solicitud.',
                null,
                {autoClose: true, closeDelay: 7000}
            );
            <?php else: ?>
            ToastSystem.danger(
                'Error',
                'Ha ocurrido un error: <?= htmlspecialchars($error) ?>',
                null,
                {autoClose: true, closeDelay: 7000}
            );
            <?php endif; ?>
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../layouts/_footer.php";
