<?php

//TODO: Implementar funcinalidad para que para asignar plantas se deba asignar un hospital primero.

use model\service\UserService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\BotiquinService;

// Inicializar servicios
$userService = new UserService();
$hospitalService = new HospitalService();
$plantaService = new PlantaService();
$botiquinService = new BotiquinService();

// Obtener el ID del usuario de la URL
$userId = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);

// Validar que exista el usuario
$user = null;
if ($userId) {
    $user = $userService->getUserById($userId);
} else {
    // Redirigir si no se proporciona un ID de usuario válido
    header('Location: /users/dashboard');
    exit;
}

// Obtener listas de hospitales, plantas y botiquines
$hospitales = $hospitalService->getAllHospitals();
$plantas = $plantaService->getAllArray();
$botiquines = $botiquinService->getAllBotiquines();

// Obtener ubicaciones ya asignadas al usuario
$assignedHospitals = $userService->getUserHospitals($userId);
$assignedPlantas = $userService->getUserPlantas($userId);
$assignedBotiquines = $userService->getUserBotiquines($userId);

// Determinar el tipo de ubicación según el rol del usuario
$userRole = $user ? $user->getRol() : '';
$locationType = '';

switch ($userRole) {
    case '1':
    case '2':
        // Para el gestor admin, se pueden asignar todas las ubicaciones
        $locationType = 'admin';
        break;
    case '3':
        $locationType = 'hospitales';
        break;
    case '4':
        $locationType = 'plantas';
        break;
    case '5':
        $locationType = 'botiquines';
        break;
    default:
        break;
}

// Procesar solicitud de guardar ubicaciones
$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_locations'])) {
    try {
        // Eliminar todas las ubicaciones existentes
        $userLocationRepository = new \model\repository\UserLocationRepository();
        $userLocationRepository->deleteAllUserLocations($userId);

        // Procesar según el tipo de ubicación
        switch ($locationType) {
            case 'hospitales':
                if (isset($_POST['hospital_ids']) && is_array($_POST['hospital_ids'])) {
                    foreach ($_POST['hospital_ids'] as $hospitalId) {
                        $userLocationRepository->addUserHospital($userId, $hospitalId);
                    }
                }
                break;
            case 'plantas':
                if (isset($_POST['planta_ids']) && is_array($_POST['planta_ids'])) {
                    foreach ($_POST['planta_ids'] as $plantaId) {
                        $userLocationRepository->addUserPlanta($userId, $plantaId);
                    }
                }
                break;
            case 'botiquines':
                if (isset($_POST['botiquin_ids']) && is_array($_POST['botiquin_ids'])) {
                    foreach ($_POST['botiquin_ids'] as $botiquinId) {
                        $userLocationRepository->addUserBotiquin($userId, $botiquinId);
                    }
                }
                break;
        }

        $success = true;

        // Recargar las ubicaciones asignadas
        $assignedHospitals = $userService->getUserHospitals($userId);
        $assignedPlantas = $userService->getUserPlantas($userId);
        $assignedBotiquines = $userService->getUserBotiquines($userId);

    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Error al guardar ubicaciones: " . $e->getMessage());
    }
}

$scripts = ["toasts.js", "user_locations.js"];
$title = 'Asignar Ubicaciones';
include(__DIR__ . '/../../layouts/_header.php');
?>

    <div class="container mt-4">
        <!-- Header section mejorado -->
        <div class="row">
            <div class="col">
                <div class="page-header">
                    <h1 class="page-title"><i class="bi bi-geo-alt me-2"></i>Asignar Ubicaciones</h1>
                    <div class="user-info-container">
                        <div class="user-profile-badge">
                            <div class="user-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="user-info">
                                <p class="lead-text mb-0">Usuario:
                                    <strong><?= htmlspecialchars($user->getNombre()) ?></strong></p>
                            </div>
                        </div>
                        <div class="user-profile-badge">
                            <div class="user-avatar">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="user-info">
                                <p class="lead-text mb-0"><?= htmlspecialchars($user->getEmail()) ?></p>
                            </div>
                        </div>
                        <div class="user-profile-badge">
                            <div class="user-avatar">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="user-info">
                                <p class="lead-text mb-0">Rol: <strong><?= htmlspecialchars($user->getRol()) ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script para mostrar notificaciones de éxito o error del procesamiento del formulario -->
        <?php if ($success || $error): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof ToastSystem !== 'undefined') {
                    <?php if ($success): ?>
                    ToastSystem.success('Éxito', 'Ubicaciones asignadas correctamente', null, { autoClose: true });
                    <?php endif; ?>
                    <?php if ($error): ?>
                    ToastSystem.danger('Error', '<?= addslashes(htmlspecialchars($error)) ?>', null, { autoClose: true });
                    <?php endif; ?>
                }
            });
        </script>
        <?php endif; ?>

        <!-- Contenido según el rol -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-pin-map me-2"></i>Asignación de ubicaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($locationType === 'admin'): ?>
                            <!-- Para roles con acceso total -->
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Los usuarios con rol de ADMIN o GESTOR_GENERAL tienen acceso a todas las ubicaciones.
                                No es necesario asignar ubicaciones específicas.
                            </div>
<!--                        Boton para volver al dashboard-->
                            <div class="text-end">
                                <a href="<?= url("users") ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Formulario para asignar ubicaciones -->
                            <form method="post" id="locationForm">
                                <!-- Contenedor de las secciones de ubicación con espaciado optimizado -->
                                <div id="location-sections-container">
                                    <?php if ($locationType === 'hospitales'): ?>
                                        <!-- Sección de Hospitales -->
                                        <div class="location-section" id="hospitales-section">
                                            <div class="location-selector">
                                                <div class="location-selector-header">
                                                    <h4 class="subsection-title"><i
                                                                class="bi bi-hospital me-2 text-primary"></i>Seleccionar
                                                        Hospitales</h4>
                                                    <p class="text-light">Elija los hospitales a los que el usuario
                                                        tendrá acceso</p>
                                                </div>

                                                <div class="location-selector-content">
                                                    <div class="selection-container">
                                                        <div class="form-group">
                                                            <label for="hospital-select"
                                                                   class="form-label">Hospital:</label>
                                                            <div class="input-group">
                                                                <select id="hospital-select" class="form-select">
                                                                    <option value="">Seleccione un hospital</option>
                                                                    <?php foreach ($hospitales as $hospital): ?>
                                                                        <option value="<?= $hospital->getId() ?>"
                                                                                data-name="<?= htmlspecialchars($hospital->getNombre()) ?>">
                                                                            <?= htmlspecialchars($hospital->getNombre()) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="button" id="add-hospital"
                                                                        class="btn btn-primary">
                                                                    <i class="bi bi-plus-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="selection-list-container">
                                                        <h5 class="selection-list-title">
                                                            <i class="bi bi-list-check me-2"></i>Hospitales
                                                            seleccionados
                                                        </h5>
                                                        <div class="selection-list-wrapper">
                                                            <ul id="selected-hospitals" class="selection-list">
                                                                <!-- Lista de hospitales seleccionados -->
                                                                <?php if (empty($assignedHospitals)): ?>
                                                                    <li class="empty-selection-message"
                                                                        id="no-hospitals-msg">
                                                                        <i class="bi bi-info-circle me-2"></i>No hay
                                                                        hospitales seleccionados
                                                                    </li>
                                                                <?php else: ?>
                                                                    <?php foreach ($assignedHospitals as $hospital): ?>
                                                                        <li class="selection-item"
                                                                            data-id="<?= $hospital['id_hospital'] ?>">
                                                                            <span class="item-name"><?= htmlspecialchars($hospital['nombre']) ?></span>
                                                                            <span class="remove-item"
                                                                                  title="Eliminar"><i
                                                                                        class="bi bi-trash"></i></span>
                                                                            <input type="hidden" name="hospital_ids[]"
                                                                                   value="<?= $hospital['id_hospital'] ?>">
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($locationType === 'plantas'): ?>
                                        <!-- Sección de Plantas -->
                                        <div class="location-section" id="plantas-section">
                                            <div class="location-selector">
                                                <div class="location-selector-header">
                                                    <h4 class="subsection-title"><i
                                                                class="bi bi-building me-2 text-success"></i>Seleccionar
                                                        Plantas</h4>
                                                    <p class="text-light">Elija las plantas a las que el usuario tendrá
                                                        acceso</p>
                                                </div>

                                                <div class="location-selector-content">
                                                    <div class="selection-container">
                                                        <div class="form-group">
                                                            <label for="planta-select"
                                                                   class="form-label">Planta:</label>
                                                            <div class="input-group">
                                                                <select id="planta-select" class="form-select">
                                                                    <option value="">Seleccione una planta</option>
                                                                    <?php foreach ($plantas as $planta): ?>
                                                                        <option value="<?= $planta['id_planta'] ?>"
                                                                                data-name="<?= htmlspecialchars($planta['nombre']) ?>">
                                                                            <?= htmlspecialchars($planta['nombre']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="button" id="add-planta"
                                                                        class="btn btn-success">
                                                                    <i class="bi bi-plus-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="selection-list-container">
                                                        <h5 class="selection-list-title">
                                                            <i class="bi bi-list-check me-2"></i>Plantas seleccionadas
                                                        </h5>
                                                        <div class="selection-list-wrapper">
                                                            <ul id="selected-plantas" class="selection-list">
                                                                <!-- Lista de plantas seleccionadas -->
                                                                <?php if (empty($assignedPlantas)): ?>
                                                                    <li class="empty-selection-message"
                                                                        id="no-plantas-msg">
                                                                        <i class="bi bi-info-circle me-2"></i>No hay
                                                                        plantas seleccionadas
                                                                    </li>
                                                                <?php else: ?>
                                                                    <?php foreach ($assignedPlantas as $planta): ?>
                                                                        <li class="selection-item"
                                                                            data-id="<?= $planta['id_planta'] ?>">
                                                                            <span class="item-name"><?= htmlspecialchars($planta['nombre']) ?></span>
                                                                            <span class="remove-item"
                                                                                  title="Eliminar"><i
                                                                                        class="bi bi-trash"></i></span>
                                                                            <input type="hidden" name="planta_ids[]"
                                                                                   value="<?= $planta['id_planta'] ?>">
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($locationType === 'botiquines'): ?>
                                        <!-- Sección de Botiquines -->
                                        <div class="location-section" id="botiquines-section">
                                            <div class="location-selector">
                                                <div class="location-selector-header">
                                                    <h4 class="subsection-title"><i
                                                                class="bi bi-box-seam me-2 text-warning"></i>Seleccionar
                                                        Botiquines</h4>
                                                    <p class="text-light">Elija los botiquines a los que el usuario
                                                        tendrá acceso</p>
                                                </div>

                                                <div class="location-selector-content">
                                                    <div class="selection-container">
                                                        <div class="form-group">
                                                            <label for="botiquin-select"
                                                                   class="form-label">Botiquín:</label>
                                                            <div class="input-group">
                                                                <select id="botiquin-select" class="form-select">
                                                                    <option value="">Seleccione un botiquín</option>
                                                                    <?php foreach ($botiquines as $botiquin): ?>
                                                                        <option value="<?= $botiquin['id_botiquin'] ?>"
                                                                                data-name="<?= htmlspecialchars($botiquin['nombre']) ?>">
                                                                            <?= htmlspecialchars($botiquin['nombre']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="button" id="add-botiquin"
                                                                        class="btn btn-warning">
                                                                    <i class="bi bi-plus-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="selection-list-container">
                                                        <h5 class="selection-list-title">
                                                            <i class="bi bi-list-check me-2"></i>Botiquines
                                                            seleccionados
                                                        </h5>
                                                        <div class="selection-list-wrapper">
                                                            <ul id="selected-botiquines" class="selection-list">
                                                                <!-- Lista de botiquines seleccionados -->
                                                                <?php if (empty($assignedBotiquines)): ?>
                                                                    <li class="empty-selection-message"
                                                                        id="no-botiquines-msg">
                                                                        <i class="bi bi-info-circle me-2"></i>No hay
                                                                        botiquines seleccionados
                                                                    </li>
                                                                <?php else: ?>
                                                                    <?php foreach ($assignedBotiquines as $botiquin): ?>
                                                                        <li class="selection-item"
                                                                            data-id="<?= $botiquin['id_botiquin'] ?>">
                                                                            <span class="item-name"><?= htmlspecialchars($botiquin['nombre']) ?></span>
                                                                            <span class="remove-item"
                                                                                  title="Eliminar"><i
                                                                                        class="bi bi-trash"></i></span>
                                                                            <input type="hidden" name="botiquin_ids[]"
                                                                                   value="<?= $botiquin['id_botiquin'] ?>">
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-end">
                                        <a href=" <?= url("users") ?>" class="btn btn-secondary me-2">
                                            <i class="bi bi-arrow-left me-1"></i> Volver
                                        </a>
                                        <button type="submit" name="save_locations" class="btn btn-success"
                                                id="saveLocations">
                                            <i class="bi bi-save me-1"></i> Guardar ubicaciones
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos para JavaScript
        const locationType = "<?= $locationType ?>";

        // Prepara los datos de los elementos ya asignados para JavaScript
        const assignedData = {
            hospitales: <?= json_encode($assignedHospitals) ?>,
            plantas: <?= json_encode($assignedPlantas) ?>,
            botiquines: <?= json_encode($assignedBotiquines) ?>
        };
    </script>

<?php
include(__DIR__ . '/../../layouts/_footer.php');
