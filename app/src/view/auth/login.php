<?php
include(__DIR__ . '/../layouts/_header.php');
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-header">
            <h1 class="auth-title">Iniciar sesión</h1>
        </div>
    </div>

    <div class="container">
        <div class="auth-card card">
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars(urldecode($error)) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success">
                    <?php
                    switch ($success) {
                        case 'logout_success':
                            echo 'Ha cerrado sesión correctamente.';
                            break;
                        default:
                            echo htmlspecialchars(urldecode($success));
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('login') ?>" class="form">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect ?? '/') ?>">

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="form-field">
                        <input type="text" name="email" id="email" class="form-input"
                               value="<?= htmlspecialchars($input['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="form-field">
                        <input type="password" name="password" id="password" class="form-input" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
                </div>

                <div class="form-footer">
                    <p>¿Problemas para iniciar sesión? Contacte con el administrador del sistema.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../layouts/_footer.php'); ?>
