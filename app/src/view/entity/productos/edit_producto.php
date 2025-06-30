<?php include __DIR__ . "/../../layouts/_header.php"; ?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Editar Producto</h1>
                <p class="page-description">
                    Modifique la información del producto y guarde los cambios.
                </p>
            </div>
        </div>

        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h3>Información del Producto</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id']); ?>">
                        <div class="field-group">
                            <div class="form-group">
                                <label for="codigo" class="form-label field-required">Código</label>
                                <div class="form-field">
                                    <input type="text" name="codigo" id="codigo" class="form-input"
                                        value="<?= htmlspecialchars($producto['codigo']); ?>" 
                                        placeholder="Ingrese el código del producto" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nombre" class="form-label field-required">Nombre</label>
                                <div class="form-field">
                                    <input type="text" name="nombre" id="nombre" class="form-input"
                                        value="<?= htmlspecialchars($producto['nombre']); ?>" 
                                        placeholder="Ingrese el nombre del producto" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <div class="form-field">
                                <textarea name="descripcion" id="descripcion" class="form-input"
                                    rows="3" placeholder="Ingrese una descripción del producto"><?= htmlspecialchars($producto['descripcion']); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="unidad_medida" class="form-label">Unidad de medida</label>
                            <div class="form-field">
                                <input type="text" name="unidad_medida" id="unidad_medida" class="form-input"
                                    value="<?= htmlspecialchars($producto['unidad_medida']); ?>" 
                                    placeholder="Ej: unidad, kg, litros">
                                <div class="field-help">
                                    <i class="fas fa-info-circle"></i> Especifique la unidad de medida para este producto (opcional)
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                            <a href="<?= url('productos') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ToastSystem.danger('Error', 'Hay errores en el formulario que debes corregir.', null, {autoClose: true, closeDelay: 5000});
    });
</script>
<?php endif; ?>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
