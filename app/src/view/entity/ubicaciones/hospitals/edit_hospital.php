<?php include __DIR__ . "/../../../layouts/_header.php"; ?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Editar Hospital</h1>
                <p class="page-description">
                    Modifique la información del hospital "<?= htmlspecialchars($hospital->getNombre()) ?>".
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="name" class="form-label">Nombre del Hospital</label>
                        <div class="form-field">
                            <input type="text" id="name" name="nombre" class="form-input"
                                   value="<?= htmlspecialchars($hospital->getNombre()) ?>"
                                   placeholder="Ingrese el nombre del hospital" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Dirección</label>
                        <div class="form-field">
                            <input type="text" id="address" name="ubicacion" class="form-input"
                                   value="<?= htmlspecialchars($hospital->getUbicacion()) ?>"
                                   placeholder="Ingrese la dirección del hospital" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Actualizar Hospital</button>
                        <a href="<?= url('hospitals') ?>" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($error)): ?>
        ToastSystem.danger('Error', '<?= htmlspecialchars(urldecode($error)) ?>', null, {autoClose: true, closeDelay: 5000});
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . "/../../../layouts/_footer.php"; ?>
