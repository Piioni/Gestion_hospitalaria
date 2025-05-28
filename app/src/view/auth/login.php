<?php

use model\service\AuthService;

$auth = new AuthService();
$errors = [];
$input = [];

$request = $_SERVER['REQUEST_METHOD'];

if ($request == 'POST') {
    // Obtener datos del formulario
    $input['identifier'] = $_POST['identifier'] ?? '';
    $input['password'] = $_POST['password'] ?? '';
    
    // Validar datos
    if (empty($input['identifier'])) {
        $errors['identifier'] = 'El email es obligatorio';
    }
    
    if (empty($input['password'])) {
        $errors['password'] = 'La contraseña es obligatoria';
    }
    
    // Si no hay errores, intentar iniciar sesión
    if (empty($errors)) {
        try {
            // Iniciar la sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Intentar autenticar al usuario
            if ($auth->login($input['identifier'], $input['password'])) {
                // Redirigir a la página principal o a la URL de redirección
                $redirect = $_GET['redirect'] ?? '/';
                header("Location: $redirect");
                exit;
            } else {
                $errors['general'] = 'Credenciales incorrectas. Por favor, comprueba tu email y contraseña.';
            }
        } catch (\Exception $e) {
            $errors['general'] = 'Error al iniciar sesión: ' . $e->getMessage();
        }
    }
} else {
    // Limpiar variables para el formulario GET
    $input['identifier'] = '';
    $input['password'] = '';
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
