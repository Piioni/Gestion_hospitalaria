<?php
// Ahora los datos vienen del controlador
$title = $title ?? 'Crear Usuario';
$navTitle = $navTitle ?? 'Crear Usuario';
$success = $success ?? false;
$errors = $errors ?? [];
$input = $input ?? [];
$roles = $roles ?? [];

include(__DIR__ . '/../../layouts/_header.php');
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-header">
            <h1 class="auth-title">Registro de Usuario</h1>
        </div>
    </div>

    <div class="container">
        <div class="auth-card card">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Usuario registrado exitosamente.
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre</label>
                    <div class="form-field">
                        <input type="text" id="nombre" name="nombre" class="form-input"
                               value="<?= htmlspecialchars($input['nombre'] ?? '') ?>" required>
                        <?php if (!empty($errors['nombre'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['nombre']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="form-field">
                        <input type="text" id="email" name="email" class="form-input"
                               value="<?= htmlspecialchars($input['email'] ?? '') ?>" required>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="form-field">
                        <input type="password" id="password" name="password" class="form-input" required>
                        <?php if (!empty($errors['password'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmar</label>
                    <div class="form-field">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                               required>
                        <?php if (!empty($errors['confirm_password'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Rol Selector -->
                <div class="form-group">
                    <label for="roles" class="form-label">Rol</label>
                    <div class="form-field">
                        <select id="roles" name="role" class="form-select" required>
                            <option value="">Seleccione un rol</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= htmlspecialchars($rol->getId()) ?>"
                                    <?= ($rol->getId() == ($input['role'] ?? '')) ? 'selected' : '' ?>>
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
                    <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../../layouts/_footer.php'); ?>
