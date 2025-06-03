<?php
include __DIR__ . "/../../layouts/_header.php";

// TODO: Mejorar estilo, e informar al usuario que no se puede eliminar un producto si tiene registros relacionados.
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Eliminar Producto</h1>
                <p class="page-description">
                    ¿Está seguro que desea eliminar este producto? Esta acción no se puede deshacer.
                </p>
            </div>
        </div>

        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>¡Advertencia!</strong> La eliminación de este producto podría afectar a registros relacionados.
        </div>

        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h3>Detalles del Producto a Eliminar</h3>
                </div>
                <div class="card-body">
                    <div class="product-details">
                        <div class="detail-item">
                            <span class="detail-label">Código:</span>
                            <span class="detail-value"><?= htmlspecialchars($producto->getCodigo()) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nombre:</span>
                            <span class="detail-value"><?= htmlspecialchars($producto->getNombre()) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Descripción:</span>
                            <span class="detail-value"><?= htmlspecialchars($producto->getDescripcion()) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Unidad de medida:</span>
                            <span class="detail-value"><?= htmlspecialchars($producto->getUnidadMedida()) ?></span>
                        </div>
                    </div>

                    <form method="POST" action="" class="delete-form">
                        <input type="hidden" name="confirm_delete" value="yes">
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Confirmar Eliminación
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
        document.addEventListener('DOMContentLoaded', function () {
            ToastSystem.danger('Error', 'Ha ocurrido un error al intentar eliminar el producto.', null, {
                autoClose: true,
                closeDelay: 5000
            });
        });
    </script>
<?php endif; ?>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
