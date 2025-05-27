<?php

use model\service\PlantaService;
use model\service\HospitalService;
use model\service\AlmacenService;
use model\service\BotiquinService;

// Obtener servicio de plantas
$plantaService = new PlantaService();

// Crear instancia del servicio de hospitales para obtener información relacionada
$hospitalService = new HospitalService();
$hospitals = $hospitalService->getAllHospitals();

// Obtener filtro de hospital si existe
$filtroHospital = isset($_GET['hospital']) ? (int)$_GET['hospital'] : null;
$filtroNombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : null;

// Obtener plantas filtradas según el hospital y nombre
if ($filtroHospital && $filtroNombre) {
    $plantas = $plantaService->getPlantasByHospitalAndNombre($filtroHospital, $filtroNombre);
} elseif ($filtroHospital) {
    $plantas = $plantaService->getPlantasByHospitalId($filtroHospital);
} elseif ($filtroNombre) {
    $plantas = $plantaService->getPlantasByNombre($filtroNombre);
} else {
    $plantas = $plantaService->getAllPlantas();
}

// Crear instancia del servicio de almacenes
$almacenService = new AlmacenService();

// Crear instancia del servicio de botiquines
$botiquinService = new BotiquinService();

// Verificar si se ha enviado un mensaje de éxito o error
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$scripts = "toasts.js";
$title = "Plantas";
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="overview-section">
                <h1 class="page-title">Gestión de Plantas Hospitalarias</h1>
                <p class="lead-text">
                    Control y gestión de plantas hospitalarias, sus almacenes asociados y botiquines.
                </p>
                <div class="action-buttons">
                    <a href="<?= url('plantas.create') ?>" class="btn btn-primary">Crear nueva planta</a>
                </div>
            </div>

            <div class="filter-section card">
                <div class="card-body">
                    <h3 class="filter-title">Filtrar plantas</h3>
                    <form action="" method="GET" class="filter-form">
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
                                           value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <?php if ($filtroHospital || isset($_GET['nombre'])): ?>
                                <a href="<?= url('plantas.dashboard') ?>" class="btn btn-secondary">Limpiar filtros</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="plantas-section">
                <h2 class="section-title">Plantas y sus botiquines</h2>

                <?php if (empty($plantas)): ?>
                    <div class="empty-state">
                        <?php if ($filtroHospital): ?>
                            No hay plantas registradas para el hospital seleccionado.
                        <?php else: ?>
                            No hay plantas registradas en el sistema.
                        <?php endif; ?>
                        <a href="<?= url('plantas.create') ?>" class="btn btn-primary">Crear una planta</a>
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
                                <div class="collapsible-header planta-header"
                                     onclick="toggleCollapsible(this, 'planta-<?= $planta->getId() ?>')">
                                    <h3 class="planta-name"><?= htmlspecialchars($planta->getNombre()) ?></h3>
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
                                                    Añadir botiquín
                                                </a>
                                                <a href="<?= url('plantas.edit', ['id_planta' => $planta->getId()]) ?>"
                                                   class="btn btn-sm btn-secondary">
                                                    Editar planta
                                                </a>
                                                <?php
                                                $almacen = $almacenService->getAlmacenByPlantaId($planta->getId());
                                                if ($almacen) :?>
                                                    <a href="<?= url('almacenes.edit', ['id_almacen' => $almacen->getId()]) ?>"
                                                       class="btn btn-sm btn-primary">
                                                        Editar almacén
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= url('almacenes.create', ['id_planta' => $planta->getId()]) ?>"
                                                       class="btn btn-sm btn-primary">
                                                        Crear almacén
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= url('plantas.delete', ['id_planta' => $planta->getId()]) ?>"
                                                   class="btn btn-sm btn-danger">
                                                    Eliminar planta
                                                </a>
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
                                                            Añadir un botiquín
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th>Nombre</th>
                                                                <th>Capacidad</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($botiquines as $botiquin): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($botiquin->getNombre()) ?></td>
                                                                    <td><?= htmlspecialchars($botiquin->getCapacidad()) ?></td>
                                                                    <td class="actions-column">
                                                                        <div class="btn-container">
                                                                            <a href="<?= url('botiquines.edit', ['id_botiquin' => $botiquin->getId()]) ?>"
                                                                               class="btn btn-sm btn-secondary">
                                                                                Editar
                                                                            </a>
                                                                            <a href="<?= url('stocks.botiquines', ['id_botiquin' => $botiquin->getId()]) ?>"
                                                                               class="btn btn-sm btn-info">
                                                                                Ver stock
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

        // Actualizar automáticamente el formulario cuando cambia el select
        document.getElementById('hospital').addEventListener('change', function () {
            this.form.submit();
        });

        // Mostrar notificaciones toast según los parámetros de la URL
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($success): ?>
                <?php if ($success === 'deleted'): ?>
                    ToastSystem.success('Éxito', 'Planta eliminada correctamente.', null, {autoClose: true, closeDelay: 5000});
                <?php elseif ($success === 'created'): ?>
                    ToastSystem.success('Éxito', 'Planta creada correctamente.', null, {autoClose: true, closeDelay: 5000});
                <?php elseif ($success === 'updated'): ?>
                    ToastSystem.success('Éxito', 'Planta actualizada correctamente.', null, {autoClose: true, closeDelay: 5000});
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <?php if ($error === 'id_invalid'): ?>
                    ToastSystem.danger('Error', 'ID de planta no válido.', null, {autoClose: true, closeDelay: 5000});
                <?php elseif ($error === 'id_not_found'): ?>
                    ToastSystem.danger('Error', 'La planta no fue encontrada.', null, {autoClose: true, closeDelay: 5000});
                <?php elseif ($error === 'hospital_no_encontrado'): ?>
                    ToastSystem.danger('Error', 'No se encontró el hospital seleccionado.', null, {autoClose: true, closeDelay: 5000});
                <?php elseif ($error === 'unexpected'): ?>
                    ToastSystem.danger('Error', 'Ha ocurrido un error inesperado.', null, {autoClose: true, closeDelay: 5000});
                <?php else: ?>
                    ToastSystem.danger('Error', '<?= htmlspecialchars(urldecode($error)) ?>', null, {autoClose: true, closeDelay: 5000});
                <?php endif; ?>
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
