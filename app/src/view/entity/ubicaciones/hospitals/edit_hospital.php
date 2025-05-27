<?php

use model\service\HospitalService;

$hospitalService = new HospitalService();

// Obtener el ID del hospital de la URL
$id_hospital = $_GET["id_hospital"] ?? null;

if (!$id_hospital || !is_numeric($id_hospital)) {
    header("Location: " . url('hospitals.dashboard'));
    exit;
}

// Obtener los datos del hospital
$hospitalData = $hospitalService->getHospitalById($id_hospital);
if (empty($hospitalData)) {
    header("Location: " . url('hospitals.dashboard'));
    exit;
}


// Inicializar variables y mensajes
$hospital = [
    'id' => $hospitalData->getId(),
    'name' => $hospitalData->getNombre(),
    'address' => $hospitalData->getUbicacion(),
];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $hospital['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $hospital['address'] = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar actualizar el hospital con los datos sanitizados
        $success = $hospitalService->updateHospital($hospital['id'], $hospital['name'], $hospital['address']);

        // Si la actualización fue exitosa, redirigir al dashboard
        if ($success) {
            header("Location: " . url('hospitals.dashboard', ['success' => 'updated']));
            exit;
        }

    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación para desplegar el mensaje.
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al actualizar el hospital: " . $e->getMessage();
    }
}

$scripts = "toasts.js";
$title = "Editar Hospital";
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">Editar Hospital</h1>
                    <p class="page-description">
                        Modifique la información del hospital "<?= htmlspecialchars($hospital['name']) ?>".
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <form method="POST" action="" class="form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($hospital['id']) ?>">
                        <div class="form-group">
                            <label for="name" class="form-label">Nombre del Hospital</label>
                            <div class="form-field">
                                <input type="text" id="name" name="name" class="form-input"
                                       value="<?= htmlspecialchars($hospital['name']) ?>"
                                       placeholder="Ingrese el nombre del hospital" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="form-label">Dirección</label>
                            <div class="form-field">
                                <input type="text" id="address" name="address" class="form-input"
                                       value="<?= htmlspecialchars($hospital['address']) ?>"
                                       placeholder="Ingrese la dirección del hospital" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Actualizar Hospital</button>
                            <a href="<?= url('hospitals.dashboard') ?>" class="btn btn-secondary">Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
            ToastSystem.danger('Error', <?= json_encode($error) ?>, null, {autoClose: true, closeDelay: 5000});
            <?php endforeach; ?>
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
