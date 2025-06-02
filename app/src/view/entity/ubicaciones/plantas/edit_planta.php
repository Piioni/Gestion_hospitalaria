<?php
// Esta vista ahora será manejada por el controlador PlantaController
// y no necesita instanciar servicios ni procesar lógica de negocio

include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Editar Planta</h1>
                <p class="page-description">
                    Modifique la información de la planta "<?= htmlspecialchars($planta['nombre'] ?? '') ?>".
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <form method="POST" action="<?= url('plantas.edit', ['id_planta' => $planta['id']]) ?>" class="form">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($planta['id']) ?>">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre de la Planta</label>
                        <div class="form-field">
                            <input type="text" id="nombre" name="nombre" class="form-input"
                                   value="<?= htmlspecialchars($planta['nombre']) ?>"
                                   placeholder="Ingrese el nombre de la planta" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="id_hospital" class="form-label">Hospital</label>
                        <div class="form-field">
                            <select name="id_hospital" id="id_hospital" class="form-select" required>
                                <?php foreach ($hospitals as $hospital): ?>
                                    <option value="<?= $hospital->getId() ?>" <?= $planta['id_hospital'] == $hospital->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hospital->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="<?= url('plantas') ?>" class="btn btn-secondary">Volver</a>
                        <?php if ($canCreateDelete): ?>
                        <a href="<?= url('plantas.delete', ['id_planta' => $planta['id']]) ?>" class="btn btn-danger">Eliminar Planta</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                ToastSystem.danger('Error', <?= json_encode($error) ?>, null, {autoClose: true, closeDelay: 5000});
            <?php endforeach; ?>
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
