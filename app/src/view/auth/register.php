<?php
include(__DIR__ . '/../../../config/bootstrap.php');

use model\service\AuthService;
use model\service\HospitalService;
use model\service\RoleService;
use model\service\PlantaService;
use model\service\BotiquinService;

$auth = new AuthService();
$hospitalService = new HospitalService();
$roleService = new RoleService();
$plantaService = new PlantaService();
$botiquinService = new BotiquinService();

$errors = [];
$input = [];

// Obtener los valores de option de los selects
$roles = $roleService->getAllRoles();
$hospitals = $hospitalService->getAllHospitals();
$plantas = $plantaService->getAllArray();
$botiquines = $botiquinService->getAllBotiquines();

// Procesar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
    $input['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $input['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $input['confirm_password'] = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_SPECIAL_CHARS);
    $input['role'] = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);
    $input['hospital'] = filter_input(INPUT_POST, 'hospital', FILTER_SANITIZE_NUMBER_INT);
    $input['planta'] = filter_input(INPUT_POST, 'planta', FILTER_SANITIZE_NUMBER_INT);
    $input['botiquin'] = filter_input(INPUT_POST, 'botiquin', FILTER_SANITIZE_NUMBER_INT);

    if ($input['password'] !== $input['confirm_password']) {
        $errors['confirm_password'] = "Las contraseñas no coinciden.";
    }

    // Validar los datos de entrada
    if (empty($errors)) {
        try {
            $auth->register(
                $input['nombre'],
                $input['email'],
                $input['password'],
                $input['role'],
                $input['hospital'],
                $input['planta'],
                $input['botiquin']
            );
        } catch (InvalidArgumentException $e) {
            // Capturar errores de validación
            $errors['general'] = $e->getMessage();
        } catch (Exception $e) {
            // Capturar otros errores
            $errors['general'] = "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}

$title = 'Register';
$scripts = "register.js";
include(__DIR__ . '/../layouts/_header.php');
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

            <form method="POST" action="/register" class="form">
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
                                <option value="<?= htmlspecialchars($rol->getIdRol()) ?>"
                                    <?= ($rol->getIdRol() == ($input['role'] ?? '')) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rol->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['role'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['role']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hospital Selector -->
                <div class="form-group">
                    <label for="hospital" class="form-label">Hospital</label>
                    <div class="form-field">
                        <select id="hospital" name="hospital" class="form-select">
                            <option value="">Seleccione un hospital</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?= htmlspecialchars($hospital->getIdHospital()) ?>"
                                    <?= ($hospital->getIdHospital() == ($input['hospital'] ?? '')) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hospital->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['hospital'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['hospital']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Planta Selector -->
                <div class="form-group">
                    <label for="planta" class="form-label">Planta</label>
                    <div class="form-field">
                        <select id="planta" name="planta" class="form-select" disabled>
                            <option value="">Seleccione una planta</option>
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </select>
                        <?php if (!empty($errors['plantas'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['plantas']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Botiquin Selector -->
                <div class="form-group">
                    <label for="botiquin" class="form-label">Botiquín</label>
                    <div class="form-field">
                        <select id="botiquin" name="botiquin" class="form-select" disabled>
                            <option value="">Seleccione un botiquín</option>
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </select>
                        <?php if (!empty($errors['botiquin'])): ?>
                            <div class="form-error"><?= htmlspecialchars($errors['botiquin']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                </div>

                <div class="form-footer">
                    <p>¿Ya tienes una cuenta? <a href="/login" class="auth-link">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script con los datos para filtrado local -->
<script>
    console.log('Plantas:', <?= json_encode($plantas) ?>);
    console.log('Botiquines:', <?= json_encode($botiquines) ?>);
    // Pasar los datos desde PHP a JavaScript como arrays simples
    const allPlantas = <?= json_encode($plantas) ?>;
    const allBotiquines = <?= json_encode($botiquines) ?>;
</script>

<?php include(__DIR__ . '/../layouts/_footer.php'); ?>
