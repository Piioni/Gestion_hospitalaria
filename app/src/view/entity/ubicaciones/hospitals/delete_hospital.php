<?php
//TODO: Implementar el mostrar plantas relacionadas al hospital
include __DIR__ . "/../../../layouts/_header.php"; ?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Confirmar Eliminación</h1>
                <p class="page-description">
                    Se requiere su confirmación para eliminar este hospital.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">¡Atención! Se encontraron dependencias</h4>
                    <p>El hospital "<strong><?= htmlspecialchars($hospital->getNombre()) ?></strong>" tiene plantas asociadas:
                    </p>

                    <ul class="dependencies-list">
                        <?php foreach ($relatedPlants as $plant): ?>
                            <li><?= htmlspecialchars($plant['nombre']) ?>
                                (ID: <?= htmlspecialchars($plant['id_planta']) ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p>Si elimina este hospital, las plantas asociadas podrían quedar sin referencia, afectando la
                        integridad de los datos.</p>
                </div>

                <div class="form-actions">
                    <form action="" method="POST" class="confirmation-form">
                        <input type="hidden" name="force" value="1">
                        <button type="submit" class="btn btn-danger">Eliminar de todos modos</button>
                    </form>
                    <a href="<?= url('hospitals') ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar notificación de advertencia
    document.addEventListener('DOMContentLoaded', function() {
        ToastSystem.warning('Advertencia', 
            'Está a punto de eliminar un hospital con dependencias. Esta acción podría afectar la integridad de los datos.',
            null,
            {autoClose: false}
        );
    });
</script>

<?php include __DIR__ . "/../../../layouts/_footer.php"; ?>
