<?php

use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;

$almacenService = new AlmacenService();
$hospitalService = new HospitalService();
$plantaService = new PlantaService();

// Obtener lista de hospitales y plantas
$hospitals = $hospitalService->getAllHospitals();
$plantas = $plantaService->getAllArray();

// Inicializar variables
$almacen = [];
$errors = [];
$success = false;

// Verificar si se ha proporcionado un ID de almacén para editar
if (!isset($_GET['id_almacen']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirigir al usuario si no se proporciona un ID
    header("Location: /almacenes/dashboard?error=no_id");
    exit;
}

// Sí es una solicitud POST, procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $almacen['id'] = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $almacen['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['tipo'] = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['id_planta'] = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar actualizar el almacén con los datos sanitizados
        $success = $almacenService->updateAlmacen(
            $almacen['id'],
            $almacen['nombre'],
            $almacen['tipo'],
            $almacen['id_hospital'],
            $almacen['id_planta']
        );

        // Redirigir a la misma página con un mensaje de éxito
        if ($success) {
            header("Location: /almacenes/edit?success=true");
            exit;
        }
    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al actualizar el almacén: " . $e->getMessage();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Verificar si hay un mensaje de éxito
    if (isset($_GET['success']) && $_GET['success'] === 'true') {
        $success = true;
    } else {
        // Obtener el ID del almacén a editar
        $id = filter_input(INPUT_GET, 'id_almacen', FILTER_SANITIZE_NUMBER_INT);

        try {
            // Cargar los datos del almacén desde la base de datos
            $almacenObj = $almacenService->getAlmacenById($id);

            if ($almacenObj) {
                // Llenar el array con los datos del almacén
                $almacen['id'] = $almacenObj->getId();
                $almacen['nombre'] = $almacenObj->getNombre();
                $almacen['tipo'] = $almacenObj->getTipo();
                $almacen['id_hospital'] = $almacenObj->getIdHospital();
                $almacen['id_planta'] = $almacenObj->getIdPlanta();
            } else {
                // El almacén no existe, redirigir
                header("Location: /almacenes/?error=not_found");
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Error al cargar el almacén: " . $e->getMessage();
        }
    }
}

$title = "Editar Almacén";
$scripts = "almacenes.js"; // Reutilizamos el mismo JS
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">
                        <?= $success ? "Almacén actualizado exitosamente" : "Editar Almacén" ?>
                    </h1>
                    <p class="page-description">
                        Modifique los datos del almacén según sea necesario.
                    </p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Error al actualizar el almacén</strong>
                        <ul class="error-list">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-content">
                        <strong>¡Operación exitosa!</strong>
                        <p>El almacén ha sido actualizado correctamente.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="almacen-form-container">
                <div class="card almacen-card">
                    <div class="card-header">
                        <h3>Información del Almacén</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="form almacen-form">
                            <!-- Campo oculto para el ID -->
                            <input type="hidden" name="id" value="<?= htmlspecialchars($almacen['id']) ?>">

                            <div class="form-group">
                                <label for="nombre" class="form-label field-required">Nombre del Almacén</label>
                                <div class="form-field">
                                    <input type="text" name="nombre" id="nombre" class="form-input"
                                           value="<?= htmlspecialchars($almacen['nombre']) ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipo" class="form-label field-required">Tipo de Almacén</label>
                                <div class="form-field">
                                    <select name="tipo" id="tipo" class="form-select" required>
                                        <option value="">Seleccione un tipo de almacén</option>
                                        <option value="GENERAL" <?= $almacen['tipo'] === 'GENERAL' ? 'selected' : '' ?>>
                                            GENERAL
                                        </option>
                                        <option value="PLANTA" <?= $almacen['tipo'] === 'PLANTA' ? 'selected' : '' ?>>
                                            PLANTA
                                        </option>
                                    </select>
                                    <div class="tipo-info">
                                        <i class="fas fa-info-circle"></i>
                                        <span>El tipo define el nivel de acceso y las funcionalidades disponibles.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="field-group">
                                <div class="form-group">
                                    <label for="id_hospital" class="form-label field-required">Hospital</label>
                                    <div class="form-field">
                                        <select name="id_hospital" id="id_hospital" class="form-select" required>
                                            <option value="">Seleccione un hospital</option>
                                            <?php foreach ($hospitals as $hospital): ?>
                                                <option value="<?= $hospital->getId() ?>" <?= $almacen['id_hospital'] == $hospital->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($hospital->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_planta" class="form-label">Planta <span class="field-optional">(Opcional)</span></label>
                                    <div class="form-field">
                                        <select name="id_planta" id="id_planta" class="form-select">
                                            <option value="">Seleccione una planta</option>
                                            <!-- Se rellenará dinámicamente con JavaScript -->
                                        </select>
                                        <div class="field-help">
                                            <i class="fas fa-info-circle"></i> Solo necesario para almacenes de tipo
                                            PLANTA
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                                <a href="/almacenes/" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pasar los datos al JavaScript -->
    <script>
        // Inicializar variables globales necesarias para el script
        window.allPlantas = <?= json_encode($plantas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        // Para preseleccionar la planta en edición
        window.selectedPlantaId = '<?= $almacen['id_planta'] ?? '' ?>';
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
