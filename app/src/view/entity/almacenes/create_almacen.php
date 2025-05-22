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

$title = "Crear Almacen";
$scripts = "create_almacen.js";
include __DIR__ . "/../../layouts/_header.php";

// Inicializar variables y mensajes
$almacen = [
    'tipo' => '',
    'id_hospital' => '',
    'id_planta' => '',
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $almacen['tipo'] = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_SPECIAL_CHARS);
    $almacen['id_planta'] = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar crear el almacen con los datos sanitizados
        $success = $almacenService->createAlmacen($almacen['tipo'], $almacen['id_hospital'], $almacen['id_planta']);

        // Reiniciar el formulario después de un envío exitoso
        $almacen = [
            'tipo' => '',
            'id_hospital' => '',
            'id_planta' => '',
        ];

    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al crear el almacen: " . $e->getMessage();
    }
}

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
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                Almacen creado correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="tipo" class="form-label">Tipo de Almacen</label>
                        <div class="form-field">
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Seleccione un tipo de almacen</option>
                                <option value="GENERAL" <?= $almacen['tipo'] === 'GENERAL' ? 'selected' : '' ?>>GENERAL</option>
                                <option value="PLANTA" <?= $almacen['tipo'] === 'PLANTA' ? 'selected' : '' ?>>PLANTA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="id_hospital" class="form-label">Hospital</label>
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
                        <label for="id_planta" class="form-label">Planta</label>
                        <div class="form-field">
                            <select name="id_planta" id="id_planta" class="form-select">
                                <option value="">Seleccione una planta</option>
                                <!-- Se rellenara dinámicamente con javascript -->
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear Almacen</button>
                        <a href="/almacenes/list" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Asegurarnos de que estamos pasando el array con la estructura correcta
    const allPlantas = <?= json_encode($plantas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    console.log("Plantas disponibles:", allPlantas); // Para depuración
</script>

<?php
include __DIR__ . "/../../layouts/_footer.php";
