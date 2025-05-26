<?php

use model\service\HospitalService;

$hospitalService = new HospitalService();

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

$title = "Crear Hospital";
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Hospital</h1>
                <p class="page-description">
                    Complete el formulario para registrar un nuevo hospital en el sistema.
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
                Hospital creado correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-content">
                <form method="POST" action="" class="form">
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
                        <button type="submit" class="btn btn-primary">Guardar Hospital</button>
                        <a href="/hospitals" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
