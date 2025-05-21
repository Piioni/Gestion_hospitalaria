<?php

use model\service\HospitalService;

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$hospitalService = new HospitalService();
$hospitals = $hospitalService->getAllHospitals();

$title = "Listado de Hospitales";
include __DIR__ . '/../layouts/_header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Hospitales</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="/hospitals/create" class="btn btn-primary">Crear Hospital</a>
        </div>
    </div>

    <?php if ($success === 'created'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Hospital creado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($success === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Hospital eliminado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            echo match ($error) {
                'id_invalid' => 'ID de hospital inválido.',
                'delete_failed' => 'No se pudo eliminar el hospital.',
                'unexpected' => 'Ocurrió un error inesperado.',
                default => htmlspecialchars($error),
            };
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($hospitals)): ?>
                <p class="text-muted">No hay hospitales registrados.</p>
            <?php else: ?>
                <table class="table table-striped">
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
                            <td>
                                <a href="/hospitals/edit?id=<?= $hospital->getIdHospital() ?>"
                                   class="btn btn-sm btn-info">Editar</a>
                                <a href="/hospitals/delete?id=<?= $hospital->getIdHospital() ?>"
                                   class="btn btn-sm btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/_footer.php'; ?>
