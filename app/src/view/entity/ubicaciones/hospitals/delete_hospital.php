<?php include __DIR__ . "/../../../layouts/_header.php"; ?>

<div class="page-section mt-5">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title text-center">Confirmar Eliminación</h1>
                <p class="text-center">¿Estás seguro de que deseas eliminar el hospital 
                    <strong><?= htmlspecialchars($hospital->getNombre()) ?></strong>?
                </p>

                <?php if (!empty($relatedPlants)): ?>
                    <div class="card mt-4">
                        <div class="card-content">
                            <div class="table-responsive mt-4">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($relatedPlants as $plant): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($plant['id_planta']) ?></td>
                                                <td><?= htmlspecialchars($plant['nombre']) ?></td>
                                                <td class="actions-column text-center">
                                                    <div class="btn-container">
                                                        <a href="<?= url('plantas.delete', ['id_planta' => $plant['id_planta']]) ?>" 
                                                           class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Eliminar planta
                                                        </a>
                                                        <a href="<?= url('plantas.edit', ['id_planta' => $plant['id_planta']]) ?>" 
                                                           class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-pencil"></i> Editar
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="text-center mt-5">
                        <div class="action-buttons-row text-center mt-5">
                            <a href="<?= url('hospitals') ?>" class="btn btn-secondary mx-2">Cancelar</a>
                            <?php if (empty($relatedPlants)): ?>
                                <button type="submit" name="confirm" value="1" class="btn btn-danger mx-2">Confirmar Eliminación</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-danger mx-2" disabled>
                                    Confirmar Eliminación
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($relatedPlants)): ?>
        // Toast para plantas relacionadas
        ToastSystem.danger(
            'No se puede eliminar el hospital',
            'Tiene <?= count($relatedPlants) ?> planta(s) asociada(s). Primero debes eliminar todas las plantas asociadas a este hospital.',
            `<a href="<?= url('plantas', ['hospital' => $hospital->getId()]) ?>" 
               class="btn btn-primary">
                Ver plantas del hospital
            </a>`,
            {autoClose: false}
        );
        <?php else: ?>
        // Advertencia
        ToastSystem.warning(
            'Advertencia',
            'Esta acción desactivara el hospital, Esta accion solo es reversible por un administrador.',
            null,
            {autoClose: true}
        );
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . "/../../../layouts/_footer.php"; ?>
