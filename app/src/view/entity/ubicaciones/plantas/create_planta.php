<?php

use model\service\HospitalService;
use model\service\PlantaService;

$plantasService = new PlantaService();
$hospitalService = new HospitalService();

$hospitals = $hospitalService->getAllHospitals();

$title = "Crear Planta";
include __DIR__ . "/../../../layouts/_header.php";

$planta = [
    'id_hospital' => '',
    'nombre' => '',
];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $planta['id_hospital'] = filter_input(INPUT_POST, 'id_hospital', FILTER_SANITIZE_NUMBER_INT);
    $planta['nombre'] = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar crear la planta con los datos sanitizados
        $plantasService->createPlanta($planta['id_hospital'], $planta['nombre']);
        $success = true;

        // Reiniciar el formulario después de un envío exitoso
        $planta = [
            'id_hospital' => '',
            'nombre' => '',
        ];
    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al crear la planta: " . $e->getMessage();
    }
} elseif (isset($_GET['id_hospital'])) {
    // Si se pasa un ID de hospital, cargar los datos del hospital
    $hospital_id = filter_input(INPUT_GET, 'id_hospital', FILTER_SANITIZE_NUMBER_INT);
    $hospital = $hospitalService->getHospitalById($hospital_id);
    if ($hospital) {
        $planta['id_hospital'] = $hospital->getId();
    } else {
        header('Location: /plantas/list?error=hospital_no_encontrado');
        exit;
    }
}

?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Planta</h1>
                <p class="page-description">
                    Complete el formulario para registrar una nueva planta en el sistema.
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
                Planta creada correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3>Información de la Planta</h3>
            </div>
            <div class="card-body">
                <form class="form" method="POST" action="">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-input" id="nombre" name="nombre"
                               value="<?= htmlspecialchars($planta['nombre']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_hospital" class="form-label">Hospital</label>
                        <select class="form-select" id="id_hospital" name="id_hospital" required>
                            <option value="">Seleccione un hospital</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?= htmlspecialchars($hospital->getId()) ?>"
                                    <?= ($hospital->getId() == ($planta['id_hospital'] ?? '')) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hospital->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear Planta</button>
                        <a href="/plantas" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
