<?php
// Variables recibidas del controlador
// TODO: Mejorar estilo de pagina y tal
$title = $title ?? 'Eliminar Usuario';
$navTitle = $navTitle ?? 'Eliminar Usuario';
$user = $user ?? null;
$roleName = $roleName ?? 'Desconocido';

include(__DIR__ . '/../../layouts/_header.php');
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1 class="page-title text-danger">
                <i class="bi bi-person-x me-3"></i>Eliminar Usuario
            </h1>
            <p class="lead-text mb-0">¿Está seguro de que desea eliminar este usuario?</p>
        </div>
    </div>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Advertencia:</strong> Esta acción no se puede deshacer. Se eliminarán todos los datos asociados a este usuario.
    </div>

    <div class="card">
        <div class="card-header bg-danger text-white">
            <h3><i class="bi bi-trash me-2"></i>Confirmación de eliminación</h3>
        </div>
        <div class="card-body">
            <div class="user-details">
                <div class="user-info-item">
                    <span class="info-label"><i class="bi bi-person-badge me-2"></i>ID:</span>
                    <span class="info-value"><?= htmlspecialchars($user->getId()) ?></span>
                </div>
                <div class="user-info-item">
                    <span class="info-label"><i class="bi bi-person me-2"></i>Nombre:</span>
                    <span class="info-value"><?= htmlspecialchars($user->getNombre()) ?></span>
                </div>
                <div class="user-info-item">
                    <span class="info-label"><i class="bi bi-envelope me-2"></i>Email:</span>
                    <span class="info-value"><?= htmlspecialchars($user->getEmail()) ?></span>
                </div>
                <div class="user-info-item">
                    <span class="info-label"><i class="bi bi-shield me-2"></i>Rol:</span>
                    <span class="info-value"><span class="badge bg-primary"><?= htmlspecialchars($roleName) ?></span></span>
                </div>
            </div>

            <hr class="my-4">

            <form method="POST" class="delete-form">
                <div class="row">
                    <div class="col-12 text-end">
                        <a href="<?= url('users') ?>" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash-fill me-1"></i> Eliminar Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toast de advertencia
    ToastSystem.warning(
        'Confirmación necesaria',
        'Está a punto de eliminar definitivamente a este usuario del sistema. Esta acción no puede deshacerse.',
        null,
        {autoClose: true, closeDelay: 7000}
    );
    
    <?php if (isset($error)): ?>
    // Toast para errores
    ToastSystem.danger(
        'Error', 
        '<?= htmlspecialchars($error) ?>', 
        null, 
        {autoClose: false}
    );
    <?php endif; ?>
});
</script>

<?php include(__DIR__ . '/../../layouts/_footer.php'); ?>
