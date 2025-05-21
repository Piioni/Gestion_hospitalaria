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

// Check if the user is already authenticated
if ($auth->isAuthenticated()) {
    header('Location: /');
    exit();
}
// Obtener los roles para el selector
$roles = $roleService->getAllRoles();

// Obtener los hospitales para el selector
$hospitals = $hospitalService->getAllHospitals();

// Obtener todas las plantas para el procesamiento en JavaScript
$plantas = $plantaService->getAllPlantas();

// Obtener todos los botiquines para el procesamiento en JavaScript
$botiquines = $botiquinService->getAllBotiquines();

//// Convertir plantas y botiquines a arrays para JSON
//$plantasArray = array_map(function($plantas) {
//    return [
//        'id_planta' => $plantas->getIdPlanta(),
//        'id_hospital' => $plantas->getIdHospital(),
//        'nombre' => $plantas->getNombre()
//    ];
//}, $plantas);
//
//$botiquinesArray = array_map(function($botiquin) {
//    return [
//        'id_botiquin' => $botiquin->getIdBotiquin(),
//        'id_planta' => $botiquin->getIdPlanta(),
//        'nombre' => $botiquin->getNombre()
//    ];
//}, $botiquines);

// Procesar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // tal

    if (empty($errors)) {
        header('Location: /login');
        exit();
    }
}

$title = 'Register';
$scripts = "register.js";
include(__DIR__ . '/../layouts/_header.php');
?>

<div>
    <div class="card">
        <div class="card-title">Registro</div>
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/register">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <div class="input-wrapper">
                    <input type="text" id="nombre" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($input['nombre'] ?? '') ?>" required>
                    <?php if (!empty($errors['nombre'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['nombre']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="text" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($input['email'] ?? '') ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Rol Selector -->
            <div class="form-group">
                <label for="roles">Rol</label>
                <div class="input-wrapper">
                    <select id="roles" name="role" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= htmlspecialchars($rol->getIdRol()) ?>"
                                <?= ($rol->getIdRol() == ($input['role'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['role'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['role']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hospital Selector -->
            <div class="form-group">
                <label for="hospital">Hospital</label>
                <div class="input-wrapper">
                    <select id="hospital" name="hospital" class="form-control">
                        <option value="">Seleccione un hospital</option>
                        <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?= htmlspecialchars($hospital->getIdHospital()) ?>"
                                <?= ($hospital->getIdHospital() == ($input['hospital'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hospital->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['hospital'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['hospital']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Planta Selector -->
            <div class="form-group">
                <label for="planta">Planta</label>
                <div class="input-wrapper">
                    <select id="planta" name="planta" class="form-control" disabled>
                        <option value="">Seleccione una planta</option>
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </select>
                    <?php if (!empty($errors['plantas'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['plantas']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botiquin Selector -->
            <div class="form-group">
                <label for="botiquin">Botiquín</label>
                <div class="input-wrapper">
                    <select id="botiquin" name="botiquin" class="form-control" disabled>
                        <option value="">Seleccione un botiquín</option>
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </select>
                    <?php if (!empty($errors['botiquin'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['botiquin']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>

            <div class="form-group">
                <p>¿Ya tienes una cuenta? <a href="/login">Inicia sesión aquí</a></p>
            </div>
        </form>
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
