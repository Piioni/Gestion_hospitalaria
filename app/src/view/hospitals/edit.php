<?php

use model\service\HospitalService;

$hospitalService = new HospitalService();

// Obtener el ID del hospital de la URL
$hospital_id = $_GET["id"] ?? null;

if (!$hospital_id || !is_numeric($hospital_id)) {
    header("Location: /hospitals/list");
    exit;
}

// Obtener los datos del hospital
$hospitalData = $hospitalService->getHospitalById($hospital_id);
if (empty($hospitalData)) {
    header("Location: /hospitals/list");
    exit;
}

$title = "Edición de Hospital";
include __DIR__ . "/../layouts/_header.php";

// Inicializar variables y mensajes
$hospital = [
    'id' => $hospitalData['id_hospital'],
    'name' => $hospitalData['nombre'],
    'address' => $hospitalData['ubicacion'],
];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $hospital['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $hospital['address'] = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Intentar actualizar el hospital con los datos sanitizados
        $hospitalService->updateHospital($hospital['id'], $hospital['name'], $hospital['address']);
        $success = true;
    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al actualizar el hospital: " . $e->getMessage();
    }
}
?>

<div class="container">
    <h1 class="mt-4">Editar Hospital</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Hospital actualizado correctamente.
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($hospital['id']) ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?= htmlspecialchars($hospital['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="address" name="address"
                   value="<?= htmlspecialchars($hospital['address']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="/hospitals/list" class="btn btn-secondary">Volver</a>
    </form>
</div>

<?php include __DIR__ . "/../layouts/_footer.php"; ?>
