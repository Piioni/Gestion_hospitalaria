<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="almacenes-section">
                <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                    <h2 class="section-title">Almacenes registrados</h2>
                    <div class="action-buttons">
                        <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                        </a>
                        <a href="<?= url('almacenes.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear almacén
                        </a>
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
                                                    <option value="<?= $hospital->getId() ?>" <?= $filtroHospital == $hospital->getId() ? 'selected' : '' ?>>
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
                                                <option value="GENERAL" <?= $filtroTipo === 'GENERAL' ? 'selected' : '' ?>>
                                                    General
                                                </option>
                                                <option value="PLANTA" <?= $filtroTipo === 'PLANTA' ? 'selected' : '' ?>>
                                                    Planta
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar
                                    </button>
                                    <a href="<?= url('almacenes') ?>" class="btn btn-secondary"><i
                                                class="bi bi-x-circle"></i> Limpiar filtros</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($almacenes)): ?>
                    <div class="empty-state">
                        <?php if ($filtroHospital || $filtroTipo): ?>
                            No hay almacenes que coincidan con los criterios de filtrado.
                        <?php else: ?>
                            No hay almacenes registrados en el sistema.
                        <?php endif; ?>
                        <a href="<?= url('almacenes.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear un almacén
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Hospital</th>
                                <th>Planta</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($almacenes as $almacen):
                                // Obtener información relacionada
                                $hospital = $hospitalService->getHospitalById($almacen->getIdHospital());
                                $hospitalNombre = $hospital ? $hospital->getNombre() : "No encontrado";

                                $plantaNombre = "N/A";
                                if ($almacen->getIdPlanta()) {
                                    $planta = $plantaService->getPlantaById($almacen->getIdPlanta());
                                    $plantaNombre = $planta ? $planta->getNombre() : "No encontrada";
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($almacen->getNombre()) ?></td>
                                    <td>
                                    <span class="badge <?= $almacen->getTipo() === 'GENERAL' ? 'bg-primary' : 'bg-info' ?>">
                                        <?= $almacen->getTipo() ?>
                                    </span>
                                    </td>
                                    <td><?= htmlspecialchars($hospitalNombre) ?></td>
                                    <td><?= htmlspecialchars($plantaNombre) ?></td>
                                    <td class="actions-column">
                                        <div class="btn-container">
                                            <a href="<?= url('almacenes.edit', ['id_almacen' => $almacen->getId()]) ?>"
                                               class="btn btn-sm btn-secondary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <a href="<?= url('stocks.almacenes', ['id_almacen' => $almacen->getId()]) ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-box-seam"></i> Ver stock
                                            </a>
                                            <a href="<?= url('almacenes.delete', ['id_almacen' => $almacen->getId()]) ?>"
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
        // Mostrar notificaciones Toast
        document.addEventListener('DOMContentLoaded', function () {
            // Mostrar notificaciones según los parámetros
            <?php if (isset($success)): ?>
            <?php if ($success === 'created'): ?>
            ToastSystem.success(
                'Almacén creado',
                'El almacén ha sido creado correctamente.',
                null,
                {autoClose: true, closeDelay: 5000}
            );
            <?php elseif ($success === 'updated'): ?>
            ToastSystem.success(
                'Almacén actualizado',
                'El almacén ha sido actualizado correctamente.',
                null,
                {autoClose: true, closeDelay: 5000}
            );
            <?php elseif ($success === 'deleted'): ?>
            ToastSystem.success(
                'Almacén eliminado',
                'El almacén ha sido eliminado correctamente.',
                null,
                {autoClose: true, closeDelay: 5000}
            );
            <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <?php if ($error === 'not_found'): ?>
            ToastSystem.danger(
                'Error',
                'El almacén no existe.',
                null,
                {autoClose: true, closeDelay: 7000}
            );
            <?php elseif ($error === 'no_id'): ?>
            ToastSystem.danger(
                'Error',
                'No se proporcionó un ID de almacén.',
                null,
                {autoClose: true, closeDelay: 7000}
            );
            <?php elseif ($error === 'id_invalid'): ?>
            ToastSystem.danger(
                'Error',
                'El ID del almacén no es válido.',
                null,
                {autoClose: true, closeDelay: 7000}
            );
            <?php elseif ($error === 'unexpected'): ?>
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
include __DIR__ . "/../../../layouts/_footer.php";
