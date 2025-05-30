<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Editar Botiquín</h1>
                <p class="page-description">
                    Modifique la información del botiquín "<?= htmlspecialchars($botiquin->getNombre()) ?>".
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <form action="" method="POST" class="form">
                    <div class="form-group">
                        <label for="id_planta" class="form-label">Planta</label>
                        <div class="form-field">
                            <select name="id_planta" id="id_planta" class="form-select">
                                <?php foreach ($plantas as $planta): ?>
                                    <option value="<?= $planta->getId() ?>" <?= $botiquin->getIdPlanta() == $planta->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($planta->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Botiquín</label>
                        <div class="form-field">
                            <input type="text" id="nombre" name="nombre" class="form-input"
                                   value="<?= htmlspecialchars($botiquin->getNombre()) ?>"
                                   placeholder="Ingrese el nombre del botiquín" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="capacidad" class="form-label">Capacidad</label>
                        <div class="form-field">
                            <input type="number" id="capacidad" name="capacidad" class="form-input"
                                   value="<?= htmlspecialchars($botiquin->getCapacidad()) ?>"
                                   placeholder="Ingrese la capacidad del botiquín" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="<?= url('botiquines') ?>" class="btn btn-secondary">Volver</a>
                        <a href="<?= url('botiquines.delete', ['id_botiquin' => $botiquin->getId()]) ?>"
                           class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar botiquín
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($errors)): ?>
        // Mostrar errores en un toast de tipo danger
        ToastSystem.danger(
            'Error al actualizar el botiquín',
            `<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>`,
            null,
            {autoClose: false}
        );
        <?php endif; ?>

        <?php if ($success): ?>
        // Mostrar mensaje de éxito
        ToastSystem.success(
            'Botiquín actualizado',
            'El botiquín se ha actualizado correctamente.',
            null,
            {autoClose: true, closeDelay: 5000}
        );
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
