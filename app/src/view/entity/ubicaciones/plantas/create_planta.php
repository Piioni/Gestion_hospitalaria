<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Planta</h1>
                <p class="page-description">
                    Complete el formulario para registrar una nueva planta en el sistema.
                </p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h3>Información de la Planta</h3>
                </div>
                <div class="card-body">
                    <form class="form" method="POST" action="<?= url('plantas.create') ?>">
                        <div class="form-group">
                            <label for="nombre" class="form-label field-required">Nombre</label>
                            <div class="form-field">
                                <input type="text" class="form-input" id="nombre" name="nombre"
                                       value="<?= htmlspecialchars($planta['nombre'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_hospital" class="form-label field-required">Hospital</label>
                            <div class="form-field">
                                <select class="form-select" id="id_hospital" name="id_hospital" required>
                                    <option value="">Seleccione un hospital</option>
                                    <?php foreach ($hospitals as $hospital): ?>
                                        <option value="<?= htmlspecialchars($hospital->getId()) ?>"
                                            <?= ($hospital->getId() == ($planta['id_hospital'] ?? '')) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hospital->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Crear Planta
                            </button>
                            <a href="<?= url('plantas') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
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
