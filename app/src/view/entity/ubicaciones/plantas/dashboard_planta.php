<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="plantas-section">
                <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                    <h2 class="section-title">Plantas y sus botiquines</h2>
                    <div class="action-buttons">
                        <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                        </a>
                        <?php if ($canCreateDelete): ?>
                            <a href="<?= url('plantas.create') ?>" class="btn btn-primary"><i
                                        class="bi bi-plus-circle"></i> Crear planta</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($filtrarActivo): ?>
                    <div class="filter-section card mb-4">
                        <div class="card-body">
                            <h3 class="filter-title">Filtrar plantas</h3>
                            <form action="" method="GET" class="filter-form">
                                <input type="hidden" name="filtrar" value="1">
                                <div class="filter-fields">
                                    <div class="filter-field">
                                        <label for="hospital" class="form-label">Hospital:</label>
                                        <div class="form-field">
                                            <select name="hospital" id="hospital" class="form-select hospital-select">
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
                                        <label for="nombre" class="form-label">Nombre: </label>
                                        <div class="form-field">
                                            <input type="text" name="nombre" id="nombre" class="form-input"
                                                   placeholder="Buscar por nombre de planta"
                                                   value="<?= isset($filtroNombre) ? htmlspecialchars($filtroNombre) : '' ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar
                                    </button>
                                    <a href="<?= url('plantas') ?>" class="btn btn-secondary"><i
                                                class="bi bi-x-circle"></i> Limpiar filtros</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($plantas)): ?>
                    <div class="empty-state">
                        <?php if ($filtroHospital || $filtroNombre): ?>
                            No hay plantas que coincidan con los criterios de búsqueda.
                        <?php else: ?>
                            No hay plantas registradas en el sistema.
                        <?php endif; ?>
                        <?php if ($canCreateDelete): ?>
                            <a href="<?= url('plantas.create') ?>" class="btn btn-primary"><i
                                        class="bi bi-plus-circle"></i> Crear una planta</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="plantas-list">
                        <?php foreach ($plantas as $planta): ?>
                            <?php
                            // Obtener el hospital asociado
                            try {
                                $hospital = $hospitalService->getHospitalById($planta->getIdHospital());
                                $hospitalNombre = $hospital ? $hospital->getNombre() : "Hospital no encontrado";
                            } catch (Exception $e) {
                                $hospitalNombre = "Error al cargar el hospital";
                            }

                            // Obtener el almacén asociado
                            try {
                                $almacen = $almacenService->getAlmacenByPlantaId($planta->getId());
                                $almacenInfo = $almacen ? $almacen->getNombre() : "Sin almacén asignado";
                            } catch (Exception $e) {
                                $almacenInfo = "Error al cargar el almacén";
                            }
                            ?>
                            <div class="planta-card card">
                                <div class="collapsible"
                                     onclick="toggleCollapsible(this, 'planta-<?= $planta->getId() ?>')">
                                    <h3 class="collapsible-name"><?= htmlspecialchars($planta->getNombre()) ?></h3>
                                    <span class="collapsible-icon">▼</span>
                                </div>

                                <div id="planta-<?= $planta->getId() ?>" class="collapsible-content">
                                    <div class="card-body">
                                        <div class="planta-details">
                                            <div class="planta-info">
                                                <p>
                                                    <strong>Hospital:</strong> <?= htmlspecialchars($hospitalNombre) ?>
                                                </p>
                                                <p><strong>Almacén:</strong> <?= $almacenInfo ?></p>
                                            </div>
                                            <div class="planta-actions">
                                                <a href="<?= url('botiquines.create', ['id_planta' => $planta->getId()]) ?>"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-plus-circle"></i> Añadir botiquín
                                                </a>
                                                <a href="<?= url('plantas.edit', ['id_planta' => $planta->getId()]) ?>"
                                                   class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-pencil"></i> Editar planta
                                                </a>
                                                <?php
                                                $almacen = $almacenService->getAlmacenByPlantaId($planta->getId());
                                                if ($almacen) :?>
                                                    <a href="<?= url('almacenes.edit', ['id_almacen' => $almacen->getId()]) ?>"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Editar almacén
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= url('almacenes.create', ['id_planta' => $planta->getId()]) ?>"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Crear almacén
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($canCreateDelete): ?>
                                                    <a href="<?= url('plantas.delete', ['id_planta' => $planta->getId()]) ?>"
                                                       class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Eliminar planta
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <hr class="divider">

                                        <div class="botiquines-section">
                                            <h4 class="subsection-title">Botiquines de la planta</h4>
                                            <?php
                                            try {
                                                $botiquines = $botiquinService->getBotiquinesByPlantaId($planta->getId());

                                                if (empty($botiquines)): ?>
                                                    <div class="empty-plants">
                                                        Esta planta no tiene botiquines registrados.
                                                        <a href="<?= url('botiquines.create', ['id_planta' => $planta->getId()]) ?>"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="bi bi-plus-circle"></i> Añadir un botiquín
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th>Nombre</th>
                                                                <th>Capacidad</th>
                                                                <th>Inventario</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($botiquines as $botiquin): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($botiquin->getNombre()) ?></td>
                                                                    <td><?= htmlspecialchars($botiquin->getCapacidad()) ?></td>
                                                                    <td>
                                                                        <?php
                                                                        try {
                                                                            $stock = $stockBotiquinService->getTotalStockByBotiquinId($botiquin->getId());
                                                                            if ($stock) {
                                                                                echo htmlspecialchars($stock->getCantidad()) . ' unidades';
                                                                            } else {
                                                                                echo 'No hay stock disponible';
                                                                            }
                                                                        } catch (Exception $e) {
                                                                            echo 'Error al cargar el stock';
                                                                        }
                                                                        ?>
                                                                    <td class="actions-column">
                                                                        <div class="btn-container">
                                                                            <a href="<?= url('botiquines.edit', ['id_botiquin' => $botiquin->getId()]) ?>"
                                                                               class="btn btn-sm btn-secondary">
                                                                                <i class="bi bi-pencil"></i> Editar
                                                                            </a>
                                                                            <a href="<?= url('stocks.botiquines', ['id_botiquin' => $botiquin->getId()]) ?>"
                                                                               class="btn btn-sm btn-info">
                                                                                <i class="bi bi-box-seam"></i> Ver stock
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php
                                                endif;
                                            } catch (Exception $e) {
                                                echo '<div class="alert alert-danger">
                                                Error al cargar los botiquines: ' . htmlspecialchars($e->getMessage()) . '
                                              </div>';
                                            }
                                            ?>
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

        // Mostrar notificaciones toast según los parámetros de la URL
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($success): ?>
            <?php if ($success === 'deleted'): ?>
            ToastSystem.success('Éxito', 'Planta eliminada correctamente.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($success === 'created'): ?>
            ToastSystem.success('Éxito', 'Planta creada correctamente.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($success === 'updated'): ?>
            ToastSystem.success('Éxito', 'Planta actualizada correctamente.', null, {
                autoClose: true,
                closeDelay: 5000
            });
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($error): ?>
            <?php if ($error === 'id_invalid'): ?>
            ToastSystem.danger('Error', 'ID de planta no válido.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($error === 'id_not_found'): ?>
            ToastSystem.danger('Error', 'La planta no fue encontrada.', null, {autoClose: true, closeDelay: 5000});
            <?php elseif ($error === 'hospital_no_encontrado'): ?>
            ToastSystem.danger('Error', 'No se encontró el hospital seleccionado.', null, {
                autoClose: true,
                closeDelay: 5000
            });
            <?php elseif ($error === 'unexpected'): ?>
            ToastSystem.danger('Error', 'Ha ocurrido un error inesperado.', null, {autoClose: true, closeDelay: 5000});
            <?php else: ?>
            ToastSystem.danger('Error', '<?= htmlspecialchars(urldecode($error)) ?>', null, {
                autoClose: true,
                closeDelay: 5000
            });
            <?php endif; ?>
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
