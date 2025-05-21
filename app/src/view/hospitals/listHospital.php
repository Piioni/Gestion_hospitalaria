<?php

use model\service\HospitalService;

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$hospitalService = new HospitalService();
$hospitals = $hospitalService->getAllHospitals();

$title = "Listado de Hospitales";
include __DIR__ . '/../layouts/_header.php'; 
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Hospitales</h1>
                <p class="page-description">
                    Administre aquí todos los hospitales registrados en el sistema.
                </p>
                <a href="/hospitals/create" class="btn btn-primary">
                    <span>Crear Hospital</span>
                </a>
            </div>
        </div>

        <?php if ($success === 'created'): ?>
            <div class="alert alert-success" role="alert">
                Hospital creado exitosamente.
            </div>
        <?php elseif ($success === 'deleted'): ?>
            <div class="alert alert-success" role="alert">
                Hospital eliminado exitosamente.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php
                echo match ($error) {
                    'id_invalid' => 'ID de hospital inválido.',
                    'delete_failed' => 'No se pudo eliminar el hospital.',
                    'unexpected' => 'Ocurrió un error inesperado.',
                    default => htmlspecialchars($error),
                };
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-content">
                <?php if (empty($hospitals)): ?>
                    <div class="empty-state">
                        <p>No hay hospitales registrados en el sistema.</p>
                        <a href="/hospitals/create" class="btn btn-primary">Crear el primer hospital</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($hospitals as $hospital): ?>
                                <tr>
                                    <td><?= htmlspecialchars($hospital->getIdHospital()) ?></td>
                                    <td><?= htmlspecialchars($hospital->getNombre()) ?></td>
                                    <td><?= htmlspecialchars($hospital->getUbicacion()) ?></td>
                                    <td class="actions-column">
                                        <a href="/hospitals/edit?id=<?= $hospital->getIdHospital() ?>" class="btn btn-sm btn-secondary">
                                           Editar
                                        </a>
                                        <a href="/hospitals/delete?id=<?= $hospital->getIdHospital() ?>" class="btn btn-sm btn-danger">
                                           Eliminar
                                        </a>
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
</div>

<?php include __DIR__ . '/../layouts/_footer.php'; ?>
