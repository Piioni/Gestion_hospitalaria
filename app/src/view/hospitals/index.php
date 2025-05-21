<?php

use model\service\HospitalService;
use model\service\PlantaService;

// Obtener servicio de hospital
$hospitalService = new HospitalService();
$hospitals = $hospitalService->getAllHospitals();

// Crear instancia del servicio de plantas
$plantaService = new PlantaService();
$title = "Sistema de Gestión Hospitalaria";
include __DIR__ . "/../layouts/_header.php";
?>

<div class="container mt-4">
    <div class="jumbotron bg-light p-4 rounded">
        <h1 class="display-4">Sistema de Gestión Hospitalaria</h1>
        <p class="lead">
            Bienvenido al sistema de gestión de stock hospitalario, una herramienta diseñada para administrar
            eficientemente los recursos médicos en centros de salud.
        </p>
        <hr class="my-4">
        <p>
            Los hospitales son instituciones fundamentales en el sistema de salud, proporcionando atención médica
            integral a pacientes con diversas necesidades. Cada hospital está organizado en plantas especializadas
            que se centran en diferentes áreas de la medicina, como cardiología, pediatría, oncología y traumatología.
        </p>
        <p>
            La gestión eficiente de recursos hospitalarios es esencial para garantizar la calidad de la atención y
            optimizar los costos operativos. Este sistema permite administrar el inventario de medicamentos y
            suministros médicos en cada planta, asegurando que el personal sanitario tenga acceso a los recursos
            necesarios en todo momento.
        </p>
        <p class="mb-4">
            Utilice las opciones de navegación para administrar hospitales, plantas y stocks según sus necesidades.
        </p>
        <p>
            <a class="btn btn-primary btn-lg" href="/hospitals/list" role="button">Ver listado de hospitales</a>
            <a class="btn btn-success btn-lg" href="/hospitals/create" role="button">Crear nuevo hospital</a>
        </p>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h2 class="mb-4">Hospitales y sus plantas</h2>

            <?php if (empty($hospitals)): ?>
                <div class="alert alert-info">
                    No hay hospitales registrados en el sistema.
                    <a href="/hospitals/create" class="alert-link">Crear un hospital</a>
                </div>
            <?php else: ?>
                <div class="accordion" id="accordionHospitals">
                    <?php foreach ($hospitals as $index => $hospital): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $hospital->getIdHospital() ?>">
                                <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $hospital->getIdHospital() ?>"
                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                        aria-controls="collapse<?= $hospital->getIdHospital() ?>">
                                    <strong><?= htmlspecialchars($hospital->getNombre()) ?></strong> -
                                    <?= htmlspecialchars($hospital->getUbicacion()) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $hospital->getIdHospital() ?>"
                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                 aria-labelledby="heading<?= $hospital->getIdHospital() ?>"
                                 data-bs-parent="#accordionHospitals">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h5>Información del hospital:</h5>
                                            <p><strong>ID:</strong> <?= $hospital->getIdHospital() ?></p>
                                            <p><strong>Nombre:</strong> <?= htmlspecialchars($hospital->getNombre()) ?>
                                            </p>
                                            <p>
                                                <strong>Dirección:</strong> <?= htmlspecialchars($hospital->getUbicacion()) ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <a href="/hospitals/edit?id=<?= $hospital->getIdHospital() ?>"
                                               class="btn btn-info">
                                                <i class="bi bi-pencil-square"></i> Editar hospital
                                            </a>
                                            <a href="/plants/create?hospital_id=<?= $hospital->getIdHospital() ?>"
                                               class="btn btn-success ms-2">
                                                <i class="bi bi-plus-circle"></i> Añadir planta
                                            </a>
                                        </div>
                                    </div>

                                    <h5 class="mt-4 mb-3">Plantas del hospital:</h5>

                                    <?php
                                    try {
                                        $plantas = $plantaService->getPlantasByHospitalId($hospital->getIdHospital());
                                        
                                        if (empty($plantas)):
                                    ?>
                                        <div class="alert alert-warning">
                                            Este hospital no tiene plantas registradas.
                                            <a href="/plants/create?hospital_id=<?= $hospital->getIdHospital() ?>"
                                               class="alert-link">
                                                Añadir una planta
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped">
                                                <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Especialidad</th>
                                                    <th>Acciones</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($plantas as $planta): ?>
                                                    <tr>
                                                        <td><?= $planta->getId() ?></td>
                                                        <td><?= htmlspecialchars($planta->getNombre()) ?></td>
                                                        <td><?= htmlspecialchars($planta->getEspecialidad() ?: 'No especificada') ?></td>
                                                        <td>
                                                            <a href="/plants/edit?id=<?= $planta->getId() ?>"
                                                               class="btn btn-sm btn-info">
                                                                Editar
                                                            </a>
                                                            <a href="/plants/view?id=<?= $planta->getId() ?>"
                                                               class="btn btn-sm btn-primary">
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
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/_footer.php"; ?>
