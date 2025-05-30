<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title text-center">Confirmar Eliminación</h1>
                <p class="text-center">¿Estás seguro de que deseas eliminar el botiquín
                    <strong><?= htmlspecialchars($botiquin->getNombre()) ?></strong> de la planta
                    <strong><?= htmlspecialchars($planta->getNombre()) ?></strong>?</p>

                <form method="POST" action="<?= url('botiquines.delete', ['id_botiquin' => $botiquin->getId()]) ?>"
                      id="deleteBotiquinForm">
                    <div class="text-center mt-4">
                        <?php if ($hasProducts): ?>
                            <div class="alert alert-info">
                                <strong>El botiquín tiene <?= $cantidadProductos ?> productos asociados.</strong>
                                <p>Debes seleccionar un almacén al que transferir estos productos:</p>

                                <div class="form-group mt-3">
                                    <label for="almacen_destino" class="form-label">Almacén destino:</label>
                                    <select name="almacen_destino" id="almacen_destino" class="form-select"
                                            required>
                                        <option value="">Selecciona un almacén...</option>
                                        <?php foreach ($almacenes as $almacen): ?>
                                            <option value="<?= $almacen->getId() ?>">
                                                <?= htmlspecialchars($almacen->getNombre()) ?>
                                                (<?= $almacen->getIdPlanta() ? 'Planta' : 'Hospital' ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="<?= url('botiquines') ?>" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" name="confirm" value="1" class="btn btn-danger">Confirmar
                                Eliminación
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mostrar un toast de advertencia sobre la eliminación
        ToastSystem.warning(
            'Advertencia',
            'Esta acción eliminará el botiquín y no podrá ser recuperado. Esta acción no es reversible.',
            null,
            {autoClose: false}
        );

        <?php if (isset($error)): ?>
        // Mostrar errores en un toast de tipo danger
        ToastSystem.danger(
            'Error al eliminar el botiquín',
            '<?= htmlspecialchars($error) ?>',
            null,
            {autoClose: false}
        );
        <?php endif; ?>

        <?php if ($hasProducts): ?>
        // Si el botiquín tiene productos, mostrar un toast informativo
        ToastSystem.info(
            'Transferencia de productos',
            'El botiquín tiene productos que serán transferidos al almacén seleccionado.',
            null,
            {autoClose: true, closeDelay: 8000}
        );

        // Validar el formulario antes de enviar
        document.getElementById('deleteBotiquinForm').addEventListener('submit', function (event) {
            const almacenDestino = document.getElementById('almacen_destino').value;

            if (!almacenDestino) {
                event.preventDefault();
                ToastSystem.danger(
                    'Error',
                    'Debe seleccionar un almacén destino para transferir los productos.',
                    null,
                    {autoClose: true, closeDelay: 5000}
                );
            } else {
                // Confirmar la eliminación
                if (!confirm('¿Está seguro de eliminar este botiquín? Esta acción no se puede deshacer.')) {
                    event.preventDefault();
                }
            }
        });
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
