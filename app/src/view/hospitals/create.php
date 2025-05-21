<?php

use model\service\HospitalService;
$hospitalService = new HospitalService();

$title = "Crear Hospital";
include __DIR__ . "/../layouts/_header.php";

// Inicializar variables y mensajes
$hospital = [
    'name' => '',
    'address' => '',
];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar datos de entrada
    $hospital['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $hospital['address'] = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
    
    try {
        // Intentar crear el hospital con los datos sanitizados
        $hospitalService->createHospital($hospital['name'], $hospital['address']);
        $success = true;
        
        // Reiniciar el formulario después de un envío exitoso
        $hospital = [
            'name' => '',
            'address' => '',
        ];
    } catch (InvalidArgumentException $e) {
        // Capturar errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Capturar otros errores
        $errors[] = "Error al crear el hospital: " . $e->getMessage();
    }
}
?>

<div class="container">
    <h1 class="mt-4">Crear Hospital</h1>
    
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
            Hospital creado correctamente.
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($hospital['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($hospital['address']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/hospitals/list" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . "/../layouts/_footer.php"; ?>
