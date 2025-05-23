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

// Inicializar variables y mensajes
$almacen = [
    'nombre' => '',
    'tipo' => '',
    'id_hospital' => '',
    'id_planta' => '',
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $almacen['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['tipo'] = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['id_planta'] = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar crear el almacen con los datos sanitizados
        $success = $almacenService->createAlmacen(
            $almacen['nombre'],
            $almacen['tipo'],
            $almacen['id_hospital'],
            $almacen['id_planta']
        );

        // Reiniciar el formulario después de un envío exitoso
        $almacen = [
            'nombre' => '',
            'tipo' => '',
            'id_hospital' => '',
            'id_planta' => '',
        ];

        // Redirigir a la lista de almacenes después de crear uno nuevo
        header("Location: /almacenes/create?success=true");
        exit; // Importante: detener la ejecución después de la redirección

    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al crear el almacen: " . $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Sí se accede a la página por GET, reiniciar el formulario
    $almacen = [
        'nombre' => $_GET['nombre'] ?? '',
        'tipo' => $_GET['tipo'] ?? '',
        'id_hospital' => $_GET['id_hospital'] ?? '',
        'id_planta' => $_GET['id_planta'] ?? '',
    ];

    // Si se accede con éxito, mostrar el mensaje de éxito
    if (isset($_GET['success']) && $_GET['success'] === 'true') {
        $success = true;
    }
}

$title = "Crear Almacen";
$scripts = "almacenes.js";
include __DIR__ . "/../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">
                        <?= $success ? "Almacen creado exitosamente" : "Crear Almacen" ?>
                    </h1>
                    <p class="page-description">
                        Complete el formulario para registrar un nuevo almacen en el sistema.
                    </p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="alert-content">
                        <strong>Error al crear el almacen</strong>
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
                        <p>El almacen ha sido creado correctamente.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="almacen-form-container">
                <div class="card almacen-card">
                    <div class="card-header">
                        <h3>Información del Almacen</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="form almacen-form">
                            <div class="form-group">
                                <label for="nombre" class="form-label field-required">Nombre del Almacen</label>
                                <div class="form-field">
                                    <input type="text" name="nombre" id="nombre" class="form-input"
                                           value="<?= htmlspecialchars($almacen['nombre']) ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipo" class="form-label field-required">Tipo de Almacen</label>
                                <div class="form-field">
                                    <select name="tipo" id="tipo" class="form-select" required>
                                        <option value="">Seleccione un tipo de almacen</option>
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
                                                <option value="<?= $hospital->getId() ?>"
                                                    <?= $almacen['id_hospital'] == $hospital->getId() ? 'selected' : '' ?>>
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
                                            <!-- Se rellenará dinámicamente con javascript -->
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
                                    <i class="fas fa-save me-1"></i> Crear Almacen
                                </button>
                                <a href="/almacenes/" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <a href="/hospitals/" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Volver a hospitales
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pasar los datos de plantas al JavaScript global -->
    <script>
        // Inicializar variables globales necesarias para el script
        window.allPlantas = <?= json_encode($plantas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    </script>

<?php
include __DIR__ . "/../../layouts/_footer.php";
?>