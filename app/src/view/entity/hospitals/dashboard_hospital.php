<?php

use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;

// Obtener servicio de hospital
$hospitalService = new HospitalService();
$hospitals = $hospitalService->getAllHospitals();

// Crear instancia del servicio de plantas y almacenes
$plantaService = new PlantaService();
$almacenService = new AlmacenService();

// Verificar si se ha enviado un mensaje de éxito
$success = $_GET['success'] ?? null;

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
            <?php if ($success): ?>
                <?php if ($_GET['success'] == 'deleted'): ?>
                    <div class="alert alert-success">
                        Hospital eliminado correctamente.
                    </div>
                <?php elseif ($_GET['success'] == 'created'): ?>
                    <div class="alert alert-success">
                        Hospital creado correctamente.
                    </div>
                <?php elseif ($_GET['success'] == 'updated'): ?>
                    <div class="alert alert-success">
                        Hospital actualizado correctamente.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

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
                            <div class="collapsible-header hospital-header"
                                 onclick="toggleCollapsible(this, 'hospital-<?= $hospital->getId() ?>')">
                                <h3 class="hospital-name"><?= htmlspecialchars($hospital->getNombre()) ?></h3>
                                <span class="collapsible-icon">▼</span>
                            </div>

                            <div id="hospital-<?= $hospital->getId() ?>" class="collapsible-content">
                                <div class="card-body">
                                    <div class="hospital-details">
                                        <div class="hospital-info">
                                            <p>
                                                <strong>ID:</strong> <?= $hospital->getId() ?>
                                            </p>
                                            <p>
                                                <strong>Nombre:</strong> <?= htmlspecialchars($hospital->getNombre()) ?>
                                            </p>
                                            <p>
                                                <strong>Dirección:</strong> <?= htmlspecialchars($hospital->getUbicacion()) ?>
                                            </p>
                                            <p>
                                                <strong>
                                                    Almacen
                                                    General:
                                                </strong>
                                                <?php
                                                if ($almacenService->getAlmacenByHospitalId($hospital->getId()) == null) {
                                                    echo "No asignado";
                                                } else {
                                                    htmlspecialchars($almacenService->getAlmacenByHospitalId($hospital->getId()->getNombre()));
                                                }
                                                ?>
                                            </p>
                                        </div>
                                        <div class="hospital-actions">
                                            <a href="/hospitals/edit?id_hospital=<?= $hospital->getId() ?>"
                                               class="btn btn-sm btn-secondary">
                                                Editar hospital
                                            </a>
                                            <a href="/plantas/create?id_hospital=<?= $hospital->getId() ?>"
                                               class="btn btn-sm btn-primary">
                                                Añadir planta
                                            </a>
                                            <!-- Check if the hospital has a general warehouse already -->
                                            <?php if (!$almacenService->getAlmacenByHospitalId($hospital->getId())): ?>
                                                <a href="/almacenes/create?tipo=GENERAL&id_hospital=<?= $hospital->getId() ?>"
                                                   class="btn btn-sm btn-secondary">
                                                    Añadir Almacén General
                                                </a>
                                            <?php endif; ?>

                                            <a href="/hospitals/delete?id_hospital=<?= $hospital->getId() ?>"
                                               class="btn btn-sm btn-danger">
                                                Eliminar
                                            </a>
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
                                                            <th>Almacen</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($plantas as $planta): ?>
                                                            <tr>
                                                                <td><?= $planta->getId() ?></td>
                                                                <td><?= htmlspecialchars($planta->getNombre()) ?></td>
                                                                <td>
                                                                    <?php
                                                                    if ($almacenService->getAlmacenByPlantaId($planta->getId()) == null) {
                                                                        echo "No asignado";
                                                                    } else {
                                                                        htmlspecialchars($almacenService->getAlmacenByPlantaId($planta->getId())->getNombre());
                                                                    }
                                                                    ?>
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
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
