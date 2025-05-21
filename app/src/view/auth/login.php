<?php

use model\service\AuthService;

$auth = new AuthService();
$errors = [];
$input = [];

$request = $_SERVER['REQUEST_METHOD'];

if ($request == 'POST') {
    // tal
} else {
    // tal
}

$title = 'Login';
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
            <?php if (!empty($errors['general'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login" class="form">
                <div class="form-group">
                    <label for="identifier" class="form-label">Email</label>
                    <div class="form-field">
                        <input type="text" name="identifier" id="identifier" class="form-input"
                              value="<?= htmlspecialchars($input['identifier'] ?? '') ?>" required>
                        <?php if (!empty($errors['identifier'])) : ?>
                            <div class="form-error">
                                <?= htmlspecialchars($errors['identifier']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="form-field">
                        <input type="password" name="password" id="password" class="form-input" required>
                        <?php if (!empty($errors['password'])) : ?>
                            <div class="form-error">
                                <?= htmlspecialchars($errors['password']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
                </div>

                <div class="form-footer">
                    <p>¿No tienes una cuenta? <a href="/register" class="auth-link">Regístrate aquí</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../layouts/_footer.php'); ?>
