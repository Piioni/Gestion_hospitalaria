<?php
// Variables recibidas del controlador
$title = $title ?? 'Editar Usuario';
$navTitle = $navTitle ?? 'Editar Usuario';
$user = $user ?? null;
$roles = $roles ?? [];
$errors = $errors ?? [];
$success = $success ?? false;

include(__DIR__ . '/../../layouts/_header.php');
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-header">
            <h1 class="auth-title">Editar Usuario</h1>
        </div>
    </div>

    <div class="container">
        <div class="auth-card card">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Usuario actualizado exitosamente.
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre</label>
                    <div class="form-field">
                        <input type="text" id="nombre" name="nombre" class="form-input"
                               value="<?= htmlspecialchars($user->getNombre()) ?>" required>
                        <?php if (!empty($errors['nombre'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['nombre']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="form-field">
                        <input type="email" id="email" name="email" class="form-input"
                               value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="roles" class="form-label">Rol</label>
                    <div class="form-field">
                        <select id="roles" name="role" class="form-select" required>
                            <option value="">Seleccione un rol</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= htmlspecialchars($rol->getId()) ?>"
                                    <?= ($rol->getId() == $user->getRol()) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rol->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['role'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['role']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= url('users') ?>" class="btn btn-secondary">Volver</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($success): ?>
    // Toast de éxito
    ToastSystem.success(
        'Usuario actualizado',
        'La información del usuario ha sido actualizada correctamente',
        null,
        {autoClose: true, closeDelay: 5000}
    );
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
    // Toast de error
    ToastSystem.danger(
        'Error en el formulario',
        'Por favor revise los errores en el formulario',
        null,
        {autoClose: true, closeDelay: 5000}
    );
    <?php endif; ?>
});
</script>

<?php include(__DIR__ . '/../../layouts/_footer.php'); ?>
