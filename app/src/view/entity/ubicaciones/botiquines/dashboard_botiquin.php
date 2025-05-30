<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="botiquines-section">
            <?php if ($filtrarActivo): ?>
                <div class="filter-section card">
                    <div class="card-body">
                        <h3 class="filter-title">Filtrar botiquines</h3>
                        <form action="" method="GET" class="filter-form">
                            <input type="hidden" name="filtrar" value="1">
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
                                    <label for="nombre" class="form-label">Nombre: </label>
                                    <div class="form-field">
                                        <input type="text" name="nombre" id="nombre" class="form-input"
                                               placeholder="Buscar por nombre de botiquín"
                                               value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                                <a href="<?= url('botiquines') ?>" class="btn btn-secondary"><i
                                            class="bi bi-x-circle"></i> Limpiar filtros</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                <h2 class="section-title">Botiquines registrados</h2>
                <div class="action-buttons">
                    <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                        <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                    </a>
                    <a href="<?= url('botiquines.create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i>
                        Crear botiquín</a>
                </div>
            </div>

            <?php if (empty($botiquines)): ?>
                <div class="empty-state">
                    <?php if ($filtro_plantas): ?>
                        No hay botiquines registrados para la planta seleccionada.
                    <?php elseif ($id_botiquin): ?>
                        No se encontró el botiquín especificado.
                    <?php else: ?>
                        No hay botiquines registrados en el sistema.
                    <?php endif; ?>
                    <a href="<?= url('botiquines.create') ?>" class="btn btn-primary"><i
                                class="bi bi-plus-circle"></i> Crear un botiquín</a>
                </div>
            <?php else: ?>
                <div class="botiquines-list">
                    <?php if (count($botiquines) === 1 && $id_botiquin): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Mostrando el botiquín seleccionado.
                            <a href="<?= url('botiquines') ?>">Ver todos los botiquines</a>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Planta</th>
                                <th>Hospital</th>
                                <th>Capacidad</th>
                                <th>Inventario</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($botiquines as $botiquin):
                                // Obtener la planta asociada
                                try {
                                    $planta = $plantaService->getPlantaById($botiquin->getIdPlanta());
                                    $plantaNombre = $planta->getNombre();
                                    // Obtener el hospital asociado a la planta
                                    $hospital = $hospitalService->getHospitalById($planta->getIdHospital());
                                    $hospitalNombre = $hospital ? $hospital->getNombre() : "No disponible";
                                } catch (Exception $e) {
                                    $plantaNombre = "Error al cargar la planta";
                                    $hospitalNombre = "No disponible";
                                }

                                // Determinar si este botiquín es el seleccionado para resaltarlo
                                $isSelected = ($id_botiquin && $id_botiquin == $botiquin->getId()) ? 'selected-row' : '';
                                ?>
                                <tr class="<?= $isSelected ?>">
                                    <td><?= htmlspecialchars($botiquin->getNombre()) ?></td>
                                    <td><?= htmlspecialchars($plantaNombre) ?></td>
                                    <td><?= htmlspecialchars($hospitalNombre) ?></td>
                                    <td><?= $botiquin->getCapacidad() ?> medicamentos</td>
                                    <td>
                                        <?php
                                        // Obtener el stock del botiquín
                                        $stock = $botiquinService->getStockByBotiquinId($botiquin->getId());
                                        if ($stock) {
                                            echo htmlspecialchars($stock, ENT_QUOTES) . " productos";
                                        } else {
                                            echo "0 productos";
                                        }
                                        ?>
                                    <td class="actions-column">
                                        <div class="btn-container">
                                            <a href="<?= url('botiquines.edit', ['id_botiquin' => $botiquin->getId()]) ?>"
                                               class="btn btn-sm btn-secondary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <a href="<?= url('stocks.botiquines', ['id_botiquin' => $botiquin->getId()]) ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-box-seam"></i> Ver stock
                                            </a>
                                            <a href="<?= url('botiquines.delete', ['id_botiquin' => $botiquin->getId()]) ?>"
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
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Función para resaltar el botiquín seleccionado cuando se carga la página
    document.addEventListener('DOMContentLoaded', function () {
        // Hacer scroll automáticamente a la fila seleccionada si existe
        const selectedRow = document.querySelector('.selected-row');
        if (selectedRow) {
            selectedRow.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Mostrar notificaciones toast según los parámetros
        <?php if ($success): ?>
        <?php if ($success === 'created'): ?>
        ToastSystem.success(
            'Botiquín creado',
            'El botiquín se ha creado correctamente.',
            null,
            {autoClose: true, closeDelay: 5000}
        );
        <?php elseif ($success === 'updated'): ?>
        ToastSystem.success(
            'Botiquín actualizado',
            'El botiquín se ha actualizado correctamente.',
            null,
            {autoClose: true, closeDelay: 5000}
        );
        <?php elseif ($success === 'deleted'): ?>
        ToastSystem.success(
            'Botiquín eliminado',
            'El botiquín se ha eliminado correctamente.',
            null,
            {autoClose: true, closeDelay: 5000}
        );
        <?php endif; ?>
        <?php endif; ?>

        <?php if ($error): ?>
        <?php if ($error === 'id_not_found'): ?>
        ToastSystem.danger(
            'Error',
            'No se encontró el botiquín con el ID especificado.',
            null,
            {autoClose: true, closeDelay: 7000}
        );
        <?php elseif ($error === 'id_invalid'): ?>
        ToastSystem.danger(
            'Error',
            'El ID proporcionado no es válido.',
            null,
            {autoClose: true, closeDelay: 7000}
        );
        <?php elseif ($error === 'unexpected'): ?>
        ToastSystem.danger(
            'Error inesperado',
            'Ocurrió un error inesperado al procesar su solicitud.',
            null,
            {autoClose: true, closeDelay: 7000}
        );
        <?php endif; ?>
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
