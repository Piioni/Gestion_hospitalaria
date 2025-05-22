<?php

use model\service\HospitalService;
use model\service\PlantaService;

// Obtener servicio de hospital
$hospitalService = new HospitalService();
$hospitals = $hospitalService->getAllHospitals();

// Crear instancia del servicio de plantas
$plantaService = new PlantaService();
$title = "Sistema de Gestión Hospitalaria";
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Sistema de Gestión Hospitalaria</h1>
            <p class="lead-text">
                Bienvenido al sistema de gestión de stock hospitalario, una herramienta diseñada para administrar
                eficientemente los recursos médicos en centros de salud.
            </p>
            <div class="overview-text">
                <p>
                    Los hospitales son instituciones fundamentales en el sistema de salud, proporcionando atención
                    médica
                    integral a pacientes con diversas necesidades. Cada hospital está organizado en plantas
                    especializadas
                    que se centran en diferentes áreas de la medicina, como cardiología, pediatría, oncología y
                    traumatología.
                </p>
                <p>
                    La gestión eficiente de recursos hospitalarios es esencial para garantizar la calidad de la atención
                    y
                    optimizar los costos operativos. Este sistema permite administrar el inventario de medicamentos y
                    suministros médicos en cada planta, asegurando que el personal sanitario tenga acceso a los recursos
                    necesarios en todo momento.
                </p>
            </div>
            <div class="action-buttons">
                <a href="/hospitals/list" class="btn btn-primary">Ver hospitales</a>
                <a href="/hospitals/create" class="btn btn-secondary">Crear hospital</a>
            </div>
        </div>

        <div class="hospitals-section">
            <h2 class="section-title">Hospitales y sus plantas</h2>

            <?php if (empty($hospitals)): ?>
                <div class="empty-state">
                    No hay hospitales registrados en el sistema.
                    <a href="/hospitals/create" class="btn btn-primary">Crear un hospital</a>
                </div>
            <?php else: ?>
                <div class="hospitals-list">
                    <?php foreach ($hospitals as $index => $hospital): ?>
                        <div class="hospital-card card">
                            <div class="card-header">
                                <h3 class="hospital-name"><?= htmlspecialchars($hospital->getNombre()) ?>
                            </div>

                            <div class="card-body">
                                <div class="hospital-details">
                                    <div class="hospital-info">
                                        <p><strong>ID:</strong> <?= $hospital->getId() ?></p>
                                        <p><strong>Nombre:</strong> <?= htmlspecialchars($hospital->getNombre()) ?></p>
                                        <p>
                                            <strong>Dirección:</strong> <?= htmlspecialchars($hospital->getUbicacion()) ?>
                                        </p>
                                    </div>

                                    <div class="hospital-actions">
                                        <a href="/hospitals/edit?id=<?= $hospital->getId() ?>"
                                           class="btn btn-sm btn-secondary">
                                            Editar hospital
                                        </a>
                                        <a href="/plantas/create?id_hospital=<?= $hospital->getId() ?>"
                                           class="btn btn-sm btn-primary">
                                            Añadir planta
                                        </a>
                                    </div>
                                </div>

                                <hr class="divider">

                                <h4 class="subsection-title">Plantas del hospital:</h4>

                                <?php
                                try {
                                    $plantas = $plantaService->getPlantasByHospitalId($hospital->getId());

                                    if (empty($plantas)):
                                        ?>
                                        <div class="empty-plants">
                                            Este hospital no tiene plantas registradas.
                                            <a href="/plantas/create?id_hospital=<?= $hospital->getId() ?>"
                                               class="btn btn-sm btn-primary">
                                                Añadir una planta
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Acciones</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($plantas as $planta): ?>
                                                    <tr>
                                                        <td><?= $planta->getId() ?></td>
                                                        <td><?= htmlspecialchars($planta->getNombre()) ?></td>
                                                        <td class="actions-column">
                                                            <a href="/plants/edit?id=<?= $planta->getId() ?>"
                                                               class="btn btn-sm btn-secondary">
                                                                Editar
                                                            </a>
                                                            <a href="/plants/view?id=<?= $planta->getId() ?>"
                                                               class="btn btn-sm btn-info">
                                                                Ver stock
                                                            </a>
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
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
