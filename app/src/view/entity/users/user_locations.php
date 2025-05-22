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

        // Guardar las ubicaciones asignadas
        $userService->saveUserLocations($userId, $selectedHospitales, $selectedPlantas, $selectedBotiquines);

        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$title = 'Asignar Ubicaciones';
include(__DIR__ . '/../../layouts/_header.php');
?>

<div class="container mt-5">
    <!-- Header section -->
    <div class="row mb-4">
        <div class="col">
            <div class="page-header">
                <h1 class="page-title">Asignar Ubicaciones</h1>
                <div class="user-profile-badge">
                    <div class="user-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="user-info">
                        <p class="lead-text mb-0">Usuario: <strong><?= htmlspecialchars($user->getNombre()) ?></strong></p>
                        <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user->getEmail()) ?></small>
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
            <p class="text-muted mb-0">Seleccione las ubicaciones que desea asignar al usuario</p>
        </div>
        <div class="card-body">
            <form method="POST">

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="locationTabsContent">
                    <!-- Pestaña de Hospitales -->
                    <div class="tab-pane fade show active" id="hospitales" role="tabpanel" aria-labelledby="hospitales-tab">
                        <div class="location-selector">
                            <div class="location-selector-header">
                                <h4><i class="bi bi-hospital me-2 text-primary"></i>Seleccionar Hospitales</h4>
                                <p class="text-muted">Elija los hospitales a los que el usuario tendrá acceso</p>
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

                    <!-- Pestaña de Plantas -->
                    <div class="tab-pane fade" id="plantas" role="tabpanel" aria-labelledby="plantas-tab">
                        <div class="location-selector">
                            <div class="location-selector-header">
                                <h4><i class="bi bi-building me-2 text-success"></i>Seleccionar Plantas</h4>
                                <p class="text-muted">Elija las plantas a las que el usuario tendrá acceso</p>
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
                                            <button type="button" id="add-planta" class="btn btn-success">
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

                    <!-- Pestaña de Botiquines -->
                    <div class="tab-pane fade" id="botiquines" role="tabpanel" aria-labelledby="botiquines-tab">
                        <div class="location-selector">
                            <div class="location-selector-header">
                                <h4><i class="bi bi-box-seam me-2 text-info"></i>Seleccionar Botiquines</h4>
                                <p class="text-muted">Elija los botiquines a los que el usuario tendrá acceso</p>
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
                                            <button type="button" id="add-botiquin" class="btn btn-info text-white">
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

                <div class="action-buttons mt-4">
                    <a href="/users/dashboard" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver al listado
                    </a>
                    <button type="submit" name="save_locations" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> Guardar Ubicaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para almacenar las selecciones
    let selectedHospitales = {};
    let selectedPlantas = {};
    let selectedBotiquines = {};
    
    // Elementos de mensajes vacíos
    const noHospitalesMsg = document.getElementById('no-hospitals-msg');
    const noPlantasMsg = document.getElementById('no-plantas-msg');
    const noBotiquinesMsg = document.getElementById('no-botiquines-msg');

    // Inicializar pestañas manualmente ya que no tenemos Bootstrap JS
    initTabs();

    // Función para actualizar los campos ocultos
    function updateHiddenFields() {
        const container = document.getElementById('hidden-fields-container');
        container.innerHTML = '';

        // Agregar campos para hospitales
        Object.keys(selectedHospitales).forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'hospitales[]';
            input.value = id;
            container.appendChild(input);
        });

        // Agregar campos para plantas
        Object.keys(selectedPlantas).forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'plantas[]';
            input.value = id;
            container.appendChild(input);
        });

        // Agregar campos para botiquines
        Object.keys(selectedBotiquines).forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'botiquines[]';
            input.value = id;
            container.appendChild(input);
        });
        
        // Actualizar visibilidad de mensajes vacíos
        noHospitalesMsg.style.display = Object.keys(selectedHospitales).length > 0 ? 'none' : 'block';
        noPlantasMsg.style.display = Object.keys(selectedPlantas).length > 0 ? 'none' : 'block';
        noBotiquinesMsg.style.display = Object.keys(selectedBotiquines).length > 0 ? 'none' : 'block';
    }

    // Agregar hospitales
    document.getElementById('add-hospital').addEventListener('click', function() {
        const select = document.getElementById('hospital-select');
        const id = select.value;
        const name = select.options[select.selectedIndex].text;

        if (id && !selectedHospitales[id]) {
            selectedHospitales[id] = name;

            const list = document.getElementById('selected-hospitals');
            const item = document.createElement('li');
            item.className = 'selection-item';
            item.innerHTML = `
                <span class="selection-item-text">
                    <i class="bi bi-hospital text-primary"></i>
                    ${name}
                </span>
                <button type="button" class="btn-remove remove-hospital" data-id="${id}" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            list.appendChild(item);

            updateHiddenFields();
            select.value = '';
        }
    });

    // Agregar plantas
    document.getElementById('add-planta').addEventListener('click', function() {
        const select = document.getElementById('planta-select');
        const id = select.value;
        const name = select.options[select.selectedIndex].text;

        if (id && !selectedPlantas[id]) {
            selectedPlantas[id] = name;

            const list = document.getElementById('selected-plantas');
            const item = document.createElement('li');
            item.className = 'selection-item';
            item.innerHTML = `
                <span class="selection-item-text">
                    <i class="bi bi-building text-success"></i>
                    ${name}
                </span>
                <button type="button" class="btn-remove remove-planta" data-id="${id}" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            list.appendChild(item);

            updateHiddenFields();
            select.value = '';
        }
    });

    // Agregar botiquines
    document.getElementById('add-botiquin').addEventListener('click', function() {
        const select = document.getElementById('botiquin-select');
        const id = select.value;
        const name = select.options[select.selectedIndex].text;

        if (id && !selectedBotiquines[id]) {
            selectedBotiquines[id] = name;

            const list = document.getElementById('selected-botiquines');
            const item = document.createElement('li');
            item.className = 'selection-item';
            item.innerHTML = `
                <span class="selection-item-text">
                    <i class="bi bi-box-seam text-info"></i>
                    ${name}
                </span>
                <button type="button" class="btn-remove remove-botiquin" data-id="${id}" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            list.appendChild(item);

            updateHiddenFields();
            select.value = '';
        }
    });

    // Eliminar elementos (delegación de eventos)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-hospital')) {
            const button = e.target.closest('.remove-hospital');
            const id = button.dataset.id;
            delete selectedHospitales[id];
            button.closest('li').remove();
            updateHiddenFields();
        }

        if (e.target.closest('.remove-planta')) {
            const button = e.target.closest('.remove-planta');
            const id = button.dataset.id;
            delete selectedPlantas[id];
            button.closest('li').remove();
            updateHiddenFields();
        }

        if (e.target.closest('.remove-botiquin')) {
            const button = e.target.closest('.remove-botiquin');
            const id = button.dataset.id;
            delete selectedBotiquines[id];
            button.closest('li').remove();
            updateHiddenFields();
        }
    });
    
    // Función para inicializar pestañas manualmente
    function initTabs() {
        const tabLinks = document.querySelectorAll('.nav-link');
        const tabContents = document.querySelectorAll('.tab-pane');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remover la clase active de todas las pestañas
                tabLinks.forEach(l => l.classList.remove('active'));
                tabContents.forEach(c => {
                    c.classList.remove('show');
                    c.classList.remove('active');
                });
                
                // Agregar la clase active a la pestaña actual
                this.classList.add('active');
                
                // Mostrar el contenido correspondiente
                const targetId = this.getAttribute('data-bs-target').substring(1);
                const targetContent = document.getElementById(targetId);
                targetContent.classList.add('show');
                targetContent.classList.add('active');
            });
        });
    }
    
    // Inicializar los mensajes vacíos
    updateHiddenFields();
});
</script>

<?php include(__DIR__ . '/../../layouts/_footer.php'); ?>
