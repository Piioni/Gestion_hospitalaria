<?php include __DIR__ . "/../../../layouts/_header.php"; ?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Hospital</h1>
                <p class="page-description">
                    Complete el formulario para registrar un nuevo hospital en el sistema.
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
                    <h3>Información del Hospital</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="form">
                        <div class="form-group">
                            <label for="name" class="form-label field-required">Nombre del Hospital</label>
                            <div class="form-field">
                                <input type="text" id="name" name="nombre" class="form-input"
                                    value="<?= htmlspecialchars($hospital_name ?? '') ?>" 
                                    placeholder="Ingrese el nombre del hospital" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="form-label field-required">Dirección</label>
                            <div class="form-field">
                                <input type="text" id="address" name="ubicacion" class="form-input"
                                    value="<?= htmlspecialchars($hospital_address ?? '') ?>" 
                                    placeholder="Ingrese la dirección del hospital" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Hospital
                            </button>
                            <a href="<?= url('hospitals') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../../layouts/_footer.php"; ?>
