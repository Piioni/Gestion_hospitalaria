<?php
// Esta vista ahora será manejada por el controlador PlantaController
// y no necesita instanciar servicios ni procesar lógica de negocio

include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title text-center">Confirmar Eliminación</h1>
                <p class="text-center">¿Estás seguro de que deseas eliminar la planta
                    <strong><?= htmlspecialchars($planta->getNombre()) ?></strong>
                    del hospital
                    <strong><?= htmlspecialchars($hospital->getNombre()) ?></strong>?
                </p>

                <form method="POST" action="<?= url('plantas.delete', ['id_planta' => $planta->getId()]) ?>">
                    <div class="text-center mt-5">
                        <div class="action-buttons-row text-center mt-5">
                            <a href="<?= url('plantas') ?>" class="btn btn-secondary mx-2">Cancelar</a>
                            <?php if (!$hasAlmacen && empty($botiquines)): ?>
                                <button type="submit" name="confirm" value="1" class="btn btn-danger mx-2">Confirmar
                                    Eliminación
                                </button>
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
        // Toast de advertencia
        ToastSystem.warning(
            'Advertencia',
            'Esta acción desactivará la planta, por lo que no podrá ser utilizada. Esta acción solo es reversible por un Administrador.',
            null,
            {autoClose: true, closeDelay: 7000}
        );

        <?php if ($hasAlmacen): ?>
        // Toast para el almacén
        ToastSystem.danger(
            'No se puede eliminar la planta',
            'Tiene un almacén asociado. Primero debes eliminar el almacén asociado a esta planta.',
            `<a href="<?= url('almacenes.delete', ['id_almacen' => $almacen->getId()]) ?>" 
               class="btn btn-primary">
                Eliminar almacén "<?= htmlspecialchars($almacen->getNombre()) ?>"
            </a>`,
            {autoClose: false}
        );
        <?php endif; ?>

        <?php if (!empty($botiquines)): ?>
        // Toast para los botiquines
        ToastSystem.danger(
            'No se puede eliminar la planta',
            'Tiene <?= count($botiquines) ?> botiquín(es) asociado(s). Primero debes eliminar todos los botiquines asociados a esta planta.',
            `<a href="<?= url('botiquines', ['planta' => $planta->getId()]) ?>" 
               class="btn btn-primary">
                Ver botiquines de la planta
            </a>`,
            {autoClose: false}
        );
        <?php endif; ?>

        <?php if (isset($error)): ?>
        // Toast para errores de procesamiento
        ToastSystem.danger('Error', '<?= htmlspecialchars($error) ?>', null, {autoClose: false});
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
