<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="hospitals-section">
            <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                <h2 class="section-title">Hospitales y sus plantas</h2>
                <div class="action-buttons">
                    <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                        <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                    </a>
                    <?php if ($canCreateDelete): ?>
                    <a href="<?= url('hospitals.create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Crear hospital
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($filtrarActivo): ?>
                <div class="filter-section card mb-4">
                    <div class="card-body">
                        <h3 class="filter-title">Filtrar hospitales</h3>
                        <form action="" method="GET" class="filter-form">
                            <input type="hidden" name="filtrar" value="1">
                            <div class="filter-fields">
                                <div class="filter-field">
                                    <label for="nombre" class="form-label">Nombre:</label>
                                    <div class="form-field">
                                        <input type="text" name="nombre" id="nombre" class="form-input"
                                               placeholder="Buscar por nombre de hospital"
                                               value="<?= isset($filtroNombre) ? htmlspecialchars($filtroNombre) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                                <a href="?" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar filtros
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($hospitals)): ?>
                <div class="empty-state">
                    <?php if ($filtroNombre): ?>
                        No hay hospitales que coincidan con el criterio de búsqueda.
                    <?php else: ?>
                        No hay hospitales registrados en el sistema.
                    <?php endif; ?>
                    <?php if ($canCreateDelete): ?>
                    <a href="<?= url('hospitals.create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Crear un hospital
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="hospitals-list">
                    <?php foreach ($hospitals as $hospital): ?>
                        <div class="hospital-card card">
                            <div class="collapsible"
                                 onclick="toggleCollapsible(this, 'hospital-<?= $hospital->getId() ?>')">
                                <h3 class="collapsible-name"><?= htmlspecialchars($hospital->getNombre()) ?></h3>
                                <span class="collapsible-icon">▼</span>
                            </div>

                            <div id="hospital-<?= $hospital->getId() ?>" class="collapsible-content">
                                <div class="card-body">
                                    <div class="hospital-details">
                                        <div class="hospital-info">
                                            <p>
                                                <strong>Dirección:</strong> <?= htmlspecialchars($hospital->getUbicacion()) ?>
                                            </p>
                                            <p>
                                                <strong>
                                                    Almacen General:
                                                </strong>
                                                <?php
                                                $almacen = $almacenService->getGeneralByHospitalId($hospital->getId());
                                                echo !$almacen ? "No asignado" : htmlspecialchars($almacen->getNombre());
                                                ?>
                                            </p>
                                        </div>
                                        <div class="hospital-actions">
                                            <a href="<?= url('hospitals.edit', ['id_hospital' => $hospital->getId()]) ?>"
                                               class="btn btn-sm btn-secondary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            
                                            <a href="<?= url('plantas.create', ['id_hospital' => $hospital->getId()]) ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-plus-circle"></i> Añadir planta
                                            </a>
                                            
                                            <?php if (!$almacen): ?>
                                                <a href="<?= url('almacenes.create', ['tipo' => 'GENERAL', 'id_hospital' => $hospital->getId()]) ?>"
                                                   class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-plus-circle"></i> Añadir Almacén General
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= url('almacenes.edit', ['id_almacen' => $almacen->getId()]) ?>"
                                                   class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-pencil"></i> Editar Almacén General
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($canCreateDelete): ?>
                                            <a href="<?= url('hospitals.delete', ['id_hospital' => $hospital->getId()]) ?>"
                                               class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <hr class="divider">

                                    <div class="plantas-section">
                                        <h4 class="subsection-title">Plantas del hospital</h4>
                                        <?php
                                        try {
                                            $plantas = $plantaService->getPlantasByHospitalId($hospital->getId());

                                            if (empty($plantas)):
                                                ?>
                                                <div class="empty-plants">
                                                    <p>
                                                        Este hospital no tiene plantas registradas.
                                                    </p>
                                                    <a href="<?= url('plantas.create', ['id_hospital' => $hospital->getId()]) ?>"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Añadir una planta
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Almacen</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($plantas as $planta): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($planta->getNombre()) ?></td>
                                                                <td>
                                                                    <?php
                                                                    $plantaAlmacen = $almacenService->getAlmacenByPlantaId($planta->getId());
                                                                    echo !$plantaAlmacen ? "No asignado" : htmlspecialchars($plantaAlmacen->getNombre());
                                                                    ?>
                                                                <td class="actions-column">
                                                                    <div class="btn-container">
                                                                        <a href="<?= url('plantas.edit', ['id_planta' => $planta->getId()]) ?>"
                                                                           class="btn btn-sm btn-secondary">
                                                                            <i class="bi bi-pencil"></i> Editar
                                                                        </a>
                                                                        <a href="<?= url('botiquines', ['planta' => $planta->getId()]) ?>"
                                                                           class="btn btn-sm btn-info">
                                                                            <i class="bi bi-box-seam"></i> Ver stock
                                                                        </a>
                                                                        <?php if ($canCreateDelete): ?>
                                                                        <a href="<?= url('plantas.delete', ['id_planta' => $planta->getId()]) ?>"
                                                                           class="btn btn-sm btn-danger">
                                                                            <i class="bi bi-trash"></i> Eliminar
                                                                        </a>
                                                                        <?php endif; ?>
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
                                            echo '<div class="alert alert-danger">Error al cargar las plantas: ' . htmlspecialchars($e->getMessage()) . '</div>';
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
        ToastSystem.success('Éxito', 'Hospital eliminado correctamente.', null, {
            autoClose: true,
            closeDelay: 5000
        });
        <?php elseif ($success === 'created'): ?>
        ToastSystem.success('Éxito', 'Hospital creado correctamente.', null, {autoClose: true, closeDelay: 5000});
        <?php elseif ($success === 'updated'): ?>
        ToastSystem.success('Éxito', 'Hospital actualizado correctamente.', null, {
            autoClose: true,
            closeDelay: 5000
        });
        <?php endif; ?>
        <?php endif; ?>

        <?php if ($error): ?>
        <?php if ($error === 'id_invalid'): ?>
        ToastSystem.danger('Error', 'ID de hospital no válido.', null, {autoClose: true, closeDelay: 5000});
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
?>