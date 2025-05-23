<?php
include(__DIR__ . '/../../../../config/bootstrap.php');

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
}

// Obtener listas de hospitales, plantas y botiquines
$hospitales = $hospitalService->getAllHospitals();
$plantas = $plantaService->getAllPlantas();
$botiquines = $botiquinService->getAllBotiquines();

// Inicializar arreglos para almacenar las ubicaciones seleccionadas
$selectedHospitales = [];
$selectedPlantas = [];
$selectedBotiquines = [];

// Procesar solicitud de guardar ubicaciones
$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_locations'])) {
    try {
        // Obtener las ubicaciones seleccionadas
        $selectedHospitales = isset($_POST['hospitales']) ? $_POST['hospitales'] : [];
        $selectedPlantas = isset($_POST['plantas']) ? $_POST['plantas'] : [];
        $selectedBotiquines = isset($_POST['botiquines']) ? $_POST['botiquines'] : [];

        // Verificar que sólo se haya seleccionado un tipo de ubicación
        $hasHospitales = !empty($selectedHospitales);
        $hasPlantas = !empty($selectedPlantas);
        $hasBotiquines = !empty($selectedBotiquines);

        // Contar cuántos tipos se han seleccionado
        $selectedTypes = ($hasHospitales ? 1 : 0) + ($hasPlantas ? 1 : 0) + ($hasBotiquines ? 1 : 0);

        if ($selectedTypes > 1) {
            throw new Exception("Solo puede asignar un tipo de ubicación: hospitales, plantas o botiquines.");
        }

        // Guardar las ubicaciones asignadas
        $userService->saveUserLocations($userId, $selectedHospitales, $selectedPlantas, $selectedBotiquines);

        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$scripts = "user_locations.js";
$title = 'Asignar Ubicaciones';
include(__DIR__ . '/../../layouts/_header.php');
?>

<div class="container mt-5">
    <!-- Header section -->
    <div class="row mb-4">
        <div class="col">
            <div class="page-header">
                <h1 class="page-title">Asignar Ubicaciones</h1>
                <div class="user-info-container mb-3">
                    <div class="user-profile-badge">
                        <div class="user-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="user-info">
                            <p class="lead-text mb-0">Usuario: <strong><?= htmlspecialchars($user->getNombre()) ?></strong></p>
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
                </div>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Ubicaciones asignadas correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Error: <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Ubicaciones Disponibles</h3>
            <p class="text-light mb-0">Seleccione el tipo de ubicación que desea asignar al usuario</p>
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle-fill me-2"></i> Las ubicaciones son excluyentes. Solo puede asignar un tipo:
                hospitales, plantas o botiquines.
            </div>
        </div>
        <div class="card-body">
            <form method="POST" class="form">
                <!-- Selector de tipo de ubicación -->
                <div class="form-group mb-3">
                    <label for="location-type-select" class="form-label">Tipo de ubicación:</label>
                    <select id="location-type-select" class="form-select">
                        <option value="">Seleccione un tipo de ubicación</option>
                        <option value="hospitales">Hospitales</option>
                        <option value="plantas">Plantas</option>
                        <option value="botiquines">Botiquines</option>
                    </select>
                </div>

                <!-- Contenedor de las secciones de ubicación con espaciado optimizado -->
                <div id="location-sections-container">
                    <!-- Sección de Hospitales -->
                    <div class="location-section" id="hospitales-section" style="display: none;">
                        <div class="location-selector">
                            <div class="location-selector-header">
                                <h4 class="subsection-title"><i class="bi bi-hospital me-2 text-primary"></i>Seleccionar Hospitales</h4>
                                <p class="text-light">Elija los hospitales a los que el usuario tendrá acceso</p>
                            </div>

                            <div class="location-selector-content">
                                <div class="selection-container">
                                    <div class="form-group">
                                        <label for="hospital-select" class="form-label">Hospital:</label>
                                        <div class="input-group">
                                            <select id="hospital-select" class="form-select">
                                                <option value="">Seleccione un hospital</option>
                                                <?php foreach ($hospitales as $hospital): ?>
                                                    <option value="<?= $hospital->getId() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" id="add-hospital" class="btn btn-primary">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="selection-list-container">
                                    <h5 class="selection-list-title">
                                        <i class="bi bi-list-check me-2"></i>Hospitales seleccionados
                                    </h5>
                                    <div class="selection-list-wrapper">
                                        <ul id="selected-hospitals" class="selection-list">
                                            <!-- Lista de hospitales seleccionados -->
                                            <li class="empty-selection-message" id="no-hospitals-msg">
                                                <i class="bi bi-info-circle me-2"></i>No hay hospitales seleccionados
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Plantas -->
                    <div class="location-section" id="plantas-section" style="display: none;">
                        <div class="location-selector">
                            <div class="location-selector-header">
                                <h4 class="subsection-title"><i class="bi bi-building me-2 text-success"></i>Seleccionar Plantas</h4>
                                <p class="text-light">Elija las plantas a las que el usuario tendrá acceso</p>
                            </div>

                            <div class="location-selector-content">
                                <div class="selection-container">
                                    <div class="form-group">
                                        <label for="planta-select" class="form-label">Planta:</label>
                                        <div class="input-group">
                                            <select id="planta-select" class="form-select">
                                                <option value="">Seleccione una planta</option>
                                                <?php foreach ($plantas as $planta): ?>
                                                    <option value="<?= $planta->getId() ?>"><?= htmlspecialchars($planta->getNombre()) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" id="add-planta" class="btn btn-primary">
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
                                            <li class="empty-selection-message" id="no-plantas-msg">
                                                <i class="bi bi-info-circle me-2"></i>No hay plantas seleccionadas
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Botiquines -->
                    <div class="location-section" id="botiquines-section" style="display: none;">
                        <div class="location-selector">
                            <div class="location-selector-header">
                                <h4 class="subsection-title"><i class="bi bi-box-seam me-2 text-info"></i>Seleccionar Botiquines</h4>
                                <p class="text-light">Elija los botiquines a los que el usuario tendrá acceso</p>
                            </div>

                            <div class="location-selector-content">
                                <div class="selection-container">
                                    <div class="form-group">
                                        <label for="botiquin-select" class="form-label">Botiquín:</label>
                                        <div class="input-group">
                                            <select id="botiquin-select" class="form-select">
                                                <option value="">Seleccione un botiquín</option>
                                                <?php foreach ($botiquines as $botiquin): ?>
                                                    <option value="<?= $botiquin->getId() ?>"><?= htmlspecialchars($botiquin->getNombre()) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" id="add-botiquin" class="btn btn-primary">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="selection-list-container">
                                    <h5 class="selection-list-title">
                                        <i class="bi bi-list-check me-2"></i>Botiquines seleccionados
                                    </h5>
                                    <div class="selection-list-wrapper">
                                        <ul id="selected-botiquines" class="selection-list">
                                            <!-- Lista de botiquines seleccionados -->
                                            <li class="empty-selection-message" id="no-botiquines-msg">
                                                <i class="bi bi-info-circle me-2"></i>No hay botiquines seleccionados
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos para almacenar los IDs seleccionados -->
                <div id="hidden-fields-container">
                    <!-- Los campos se generarán dinámicamente -->
                </div>

                <div class="action-buttons">
                    <a href="/users/dashboard" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver al listado
                    </a>
                    <button type="submit" name="save_locations" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Guardar Ubicaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../../layouts/_footer.php'); ?>
