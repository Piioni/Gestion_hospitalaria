<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title text-center">Confirmar Eliminación</h1>
                <p class="text-center">¿Estás seguro de que deseas eliminar el almacén <strong><?= htmlspecialchars($almacen->getNombre()) ?></strong>?</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Información del Almacén</h3>
            </div>
            <div class="card-body">
                <div class="almacen-details">
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($almacen->getNombre()) ?></p>
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($almacen->getTipo()) ?></p>
                    <p><strong>Hospital:</strong> <?= htmlspecialchars($hospital->getNombre()) ?></p>
                    
                    <?php if ($planta): ?>
                        <p><strong>Planta:</strong> <?= htmlspecialchars($planta->getNombre()) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?= url('almacenes') ?>" class="btn btn-secondary">Cancelar</a>
                    <a href="<?= url('almacenes.delete', ['id_almacen' => $almacen->getId(), 'confirm' => 1]) ?>" 
                       class="btn btn-danger" 
                       id="confirmDelete">Confirmar Eliminación</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar un toast de advertencia sobre la eliminación
        ToastSystem.warning(
            'Advertencia',
            'Esta acción eliminará el almacén y todos sus registros asociados. Esta acción no se puede deshacer.',
            null,
            { autoClose: false }
        );
        
        <?php if ($tieneStock): ?>
            // Mostrar una advertencia adicional si el almacén tiene stock
            ToastSystem.info(
                'Información importante',
                'Este almacén tiene productos en stock. Al eliminarlo, todo el stock asociado también será eliminado.',
                null,
                { autoClose: false }
            );
        <?php endif; ?>
        
        <?php if ($error): ?>
            // Mostrar error si existe
            ToastSystem.danger(
                'Error al eliminar',
                '<?= htmlspecialchars($error) ?>',
                null,
                { autoClose: false }
            );
        <?php endif; ?>
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
