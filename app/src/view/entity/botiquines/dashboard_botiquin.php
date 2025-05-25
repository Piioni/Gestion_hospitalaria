<?php

// TODO: Implementar filtrado por nombre de botiquin
// TODO: Implementar muestreado de stock

use model\service\BotiquinService;
use model\service\PlantaService;

$botiquinesService = new BotiquinService();

$plantasService = new PlantaService();
$plantas = $plantasService->getAllPlantas();

// Obtener el filtro de planta desde la URL, si existe
$filtro_plantas = isset($_GET['planta']) ? (int)$_GET['planta'] : null;

// Filtrar los botiquines por planta 
if ($filtro_plantas) {
    $botiquines = $botiquinesService->getBotiquinesByPlantaId($filtro_plantas);
} else {
    $botiquines = $botiquinesService->getAllBotiquines();
}

$title = "Dashboard de Botiquines";
include __DIR__ . '/../../layouts/_header.php';
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Gestión de Botiquines</h1>
            <p class="lead-text">
                Control y gestión de botiquines, sus medicamentos y stock disponible.
            </p>
            <div class="action-buttons">
                <a href="/botiquines/create" class="btn btn-primary">Crear nuevo botiquín</a>
            </div>
        </div>

        <div class="filter-section card">
            <div class="card-body">
                <h3 class="filter-title">Filtrar botiquines</h3>
                <form action="" method="GET" class="form-inline filter-form">
                    <div class="form-group">
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
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <?php if ($filtro_plantas): ?>
                            <a href="?" class="btn btn-secondary">Limpiar filtro</a>
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
                    <a href="/botiquines/create" class="btn btn-primary">Crear un botiquín</a>
                </div>
            <?php else: ?>
                <div class="botiquines-list">
                    <?php foreach ($botiquines as $botiquin): 
                        // Obtener la planta asociada
                        try {
                            $planta = $plantasService->getPlantaById($botiquin->getIdPlanta());
                            $plantaNombre = $planta->getNombre();
                        } catch (Exception $e) {
                            $plantaNombre = "Error al cargar la planta";
                        }
                    ?>
                        <div class="botiquin-card card">
                            <div class="collapsible-header planta-header"
                                 onclick="toggleCollapsible(this, 'botiquin-<?= $botiquin->getId() ?>')">
                                <h3 class="planta-name"><?= htmlspecialchars($botiquin->getNombre()) ?></h3>
                                <span class="collapsible-icon">▼</span>
                            </div>

                            <div id="botiquin-<?= $botiquin->getId() ?>" class="collapsible-content">
                                <div class="card-body">
                                    <div class="planta-details">
                                        <div class="planta-info">
                                            <p><strong>ID:</strong> <?= $botiquin->getId() ?></p>
                                            <p><strong>Planta:</strong> <?= htmlspecialchars($plantaNombre) ?>
                                                (ID: <?= $botiquin->getIdPlanta() ?>)</p>
                                            <p><strong>Capacidad:</strong> <?= $botiquin->getCapacidad() ?> medicamentos</p>
                                        </div>
                                        <div class="planta-actions">
                                            <a href="/botiquines/edit?id_botiquin=<?= $botiquin->getId() ?>"
                                               class="btn btn-sm btn-secondary">
                                                Editar
                                            </a>
                                            <a href="/botiquines/view?id_botiquin=<?= $botiquin->getId() ?>"
                                               class="btn btn-sm btn-primary">
                                                Ver stock
                                            </a>
                                            <a href="/botiquines/delete?id_botiquin=<?= $botiquin->getId() ?>"
                                               class="btn btn-sm btn-danger">
                                                Eliminar
                                            </a>
                                        </div>
                                    </div>

                                    <hr class="divider">

                                    <div class="medicamentos-section">
                                        <h4 class="subsection-title">Medicamentos en stock</h4>
                                        <?php
                                        // Aquí se implementaría la lógica para mostrar los medicamentos
                                        // Por ahora mostramos un mensaje provisional
                                        ?>
                                        <div class="empty-plants">
                                            Funcionalidad de stock de medicamentos en desarrollo.
                                            <a href="/medicamentos/create?id_botiquin=<?= $botiquin->getId() ?>"
                                               class="btn btn-sm btn-primary">
                                                Añadir medicamento
                                            </a>
                                        </div>
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
    document.getElementById('planta').addEventListener('change', function () {
        this.form.submit();
    });
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
