<?php

// TODO: Alerta que le informe al usuario que editar el hospital de una planta puede afectar

use model\service\HospitalService;
use model\service\PlantaService;

$plantaService = new PlantaService();
$hospitalService = new HospitalService();

$hospitals = $hospitalService->getAllHospitals();

// Obtener el ID de la planta de la URL
$id_planta = $_GET["id_planta"] ?? null;

if (!$id_planta || !is_numeric($id_planta)) {
    header("Location: " . url('plantas.dashboard'));
    exit;
}

// Obtener los datos de la planta
$plantaData = $plantaService->getPlantaById($id_planta);

// Inicializar variables y mensajes
$planta = [
    'id' => $plantaData->getId(),
    'hospital_id' => $plantaData->getIdHospital(),
    'name' => $plantaData->getNombre(),
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $planta['hospital_id'] = filter_input(INPUT_POST, 'hospital_id', FILTER_SANITIZE_NUMBER_INT);
    $planta['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar actualizar la planta con los datos sanitizados
        $success = $plantaService->updatePlanta($planta['id'], $planta['hospital_id'], $planta['name']);

        if ($success) {
            // Redirigir a la página de lista de plantas con mensaje de éxito
            header('Location: ' . url('plantas.dashboard', ['success' => 'updated']));
            exit;
        }

    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación para desplegar el mensaje.
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al actualizar la planta: " . $e->getMessage();
    }
}

$title = "Editar Planta";
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Editar Planta</h1>
                <p class="page-description">
                    Modifique la información de la planta "<?= htmlspecialchars($planta['name']) ?>".
                </p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Planta actualizada correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-content">
                <form method="POST" action="" class="form">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($planta['id']) ?>">
                    <div class="form-group">
                        <label for="name" class="form-label">Nombre de la Planta</label>
                        <div class="form-field">
                            <input type="text" id="name" name="name" class="form-input"
                                   value="<?= htmlspecialchars($planta['name']) ?>"
                                   placeholder="Ingrese el nombre de la planta" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hospital_id" class="form-label">Hospital</label>
                        <div class="form-field">
                            <select name="hospital_id" id="hospital_id" class="form-select" required>
                                <?php foreach ($hospitals as $hospital): ?>
                                    <option value="<?= $hospital->getId() ?>" <?= $planta['hospital_id'] == $hospital->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hospital->getNombre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="<?= url('plantas.dashboard') ?>" class="btn btn-secondary">Volver</a>
                        <a href="<?= url('plantas.delete', ['id_planta' => $planta['id']]) ?>" class="btn btn-danger">Eliminar Planta</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
