<?php

use model\service\BotiquinService;
use model\service\PlantaService;
use model\service\HospitalService;

$botiquinesService = new BotiquinService();

$plantasService = new PlantaService();
$plantas = $plantasService->getAllPlantas();

$hospitalService = new HospitalService();

// Obtener el filtro de planta desde la URL, si existe
$filtro_plantas = isset($_GET['planta']) ? (int)$_GET['planta'] : null;

// Filtrar los botiquines por planta 
if ($filtro_plantas) {
    $botiquines = $botiquinesService->getBotiquinesByPlantaId($filtro_plantas);
} else {
    $botiquines = $botiquinesService->getAllBotiquines();
}

$title = "Dashboard de Botiquines";
include __DIR__ . '/../../../layouts/_header.php';
?>

    <div class="page-section">
        <div class="container">
            <div class="overview-section">
                <h1 class="page-title">Gestión de Botiquines</h1>
                <p class="lead-text">
                    Control y gestión de botiquines en las diferentes plantas hospitalarias.
                </p>
                <div class="action-buttons">
                    <a href="<?= url('botiquines.create') ?>" class="btn btn-primary">Crear nuevo botiquín</a>
                </div>
            </div>

            <?php if (isset($_GET['error'])) :
                if ($_GET['error'] == 'id_not_found') : ?>
                    <div class="alert alert-danger">
                        <strong>Error:</strong> No se encontró el botiquin con el ID especificado.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['success'])) :
                if ($_GET['success'] == 'created') : ?>
                    <div class="alert alert-success">
                        Botiquín creado correctamente.
                    </div>
                <?php elseif ($_GET['success'] == 'updated') : ?>
                    <div class="alert alert-success">
                        Botiquín actualizado correctamente.
                    </div>
                <?php elseif ($_GET['success'] == 'deleted') : ?>
                    <div class="alert alert-success">
                        Botiquín eliminado correctamente.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="filter-section card">
                <div class="card-body">
                    <h3 class="filter-title">Filtrar botiquines</h3>
                    <form action="" method="GET" class="filter-form">
                        <div class="filter-fields">
                            <div class="filter-field">
                                <label for="planta" class="form-label">Planta:</label>
                                <select name="planta" id="planta" class="form-select">
                                    <option value="">Todas las plantas</option>
                                    <?php foreach ($plantas as $planta): ?>
                                        <option value="<?= $planta->getId() ?>" <?= $filtro_plantas == $planta->getId() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($planta->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                            <?php if ($filtro_plantas || isset($_GET['nombre'])): ?>
                                <a href="<?= url('botiquines.dashboard') ?>" class="btn btn-secondary">Limpiar filtro</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="botiquines-section">
                <h2 class="section-title">Botiquines registrados</h2>

                <?php if (empty($botiquines)): ?>
                    <div class="empty-state">
                        <?php if ($filtro_plantas): ?>
                            No hay botiquines registrados para la planta seleccionada.
                        <?php else: ?>
                            No hay botiquines registrados en el sistema.
                        <?php endif; ?>
                        <a href="<?= url('botiquines.create') ?>" class="btn btn-primary">Crear un botiquín</a>
                    </div>
                <?php else: ?>
                    <div class="botiquines-list">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Planta</th>
                                        <th>Hospital</th>
                                        <th>Capacidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($botiquines as $botiquin):
                                        // Obtener la planta asociada
                                        try {
                                            $planta = $plantasService->getPlantaById($botiquin->getIdPlanta());
                                            $plantaNombre = $planta->getNombre();
                                            // Obtener el hospital asociado a la planta
                                            $hospital = $hospitalService->getHospitalById($planta->getIdHospital());
                                            $hospitalNombre = $hospital ? $hospital->getNombre() : "No disponible";
                                        } catch (Exception $e) {
                                            $plantaNombre = "Error al cargar la planta";
                                            $hospitalNombre = "No disponible";
                                        }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($botiquin->getNombre()) ?></td>
                                        <td><?= htmlspecialchars($plantaNombre) ?></td>
                                        <td><?= htmlspecialchars($hospitalNombre) ?></td>
                                        <td><?= $botiquin->getCapacidad() ?> medicamentos</td>
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
        // Actualizar automáticamente el formulario cuando cambia el select
        document.getElementById('planta').addEventListener('change', function () {
            this.form.submit();
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
