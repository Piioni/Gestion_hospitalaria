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

<div class="container">
    <h1>Iniciar sesión</h1>
    <form method="POST" action="/login">
        <div class="form-group">
            <label for="identifier">Email</label>
            <input type="text" name="identifier" id="identifier" class="form-control"
                   value="<?= htmlspecialchars($input['identifier'] ?? '') ?>" required>
            <?php if (!empty($errors['identifier'])) : ?>
                <div class="error-message">
                    <?= htmlspecialchars($errors['identifier']) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <?php if (!empty($errors['password'])) : ?>
                <div class="error-message">
                    <?= htmlspecialchars($errors['password']) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
        </div>
        <div class="form-group">
            <p>¿No tienes una cuenta? <a href="/register">Regístrate aquí</a></p>
        </div>
    </form>
</div>

<?php include(__DIR__ . '/../layouts/_footer.php'); ?>


