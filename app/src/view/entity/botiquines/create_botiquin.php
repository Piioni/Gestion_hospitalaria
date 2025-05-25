<?php

use model\service\BotiquinService;
use model\service\PlantaService;

$botiquinService = new BotiquinService();
$plantaService = new PlantaService();

$plantas = $plantaService->getAllPlantas();

// Inicializar el botiquín con valores por defecto
$botiquin = [
    'id_planta' => '',
    'nombre' => '',
    'capacidad' => 0,
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos de entrada
    $id_planta = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_NUMBER_INT);
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
    $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_SANITIZE_NUMBER_INT);

    try {
        $success = $botiquinService->createBotiquin($id_planta, $nombre, $capacidad);

        // Reiniciar el formulario después de un envío exitoso
        $botiquin = [
            'id_planta' => '',
            'nombre' => '',
            'capacidad' => 0,
        ];

    } catch (InvalidArgumentException $e) {
        // Manejo de errores de validación
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        // Manejo de errores
        $errors[] = "Error al crear el botiquín: " . $e->getMessage();
    }

} elseif (isset($_GET['id_planta'])) {
    // Si se pasa un ID de planta, cargar los datos de la planta
    $id_planta = filter_input(INPUT_GET, 'id_planta', FILTER_SANITIZE_NUMBER_INT);
    $planta = $plantaService->getPlantaById($id_planta);
    $botiquin['id_planta'] = $planta->getId();
}


$title = "Crear Botiquín";
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Botiquín</h1>
                <p class="page-description">
                    Complete el formulario para registrar un nuevo botiquín en el sistema.
                </p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Botiquín creado correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3>Información del Botiquín</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="id_planta" class="form-label">Planta</label>
                        <div class="form-field">
                            <select name="id_planta" id="id_planta" class="form-select" required>
                                <option value="">Seleccione una planta</option>
                                <?php foreach ($plantas as $planta): ?>
                                    <option value="<?= htmlspecialchars($planta->getId()); ?>"
                                        <?= $botiquin['id_planta'] == $planta->getId() ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($planta->getNombre()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Botiquín</label>
                        <div class="form-field">
                            <input type="text" name="nombre" id="nombre" class="form-input"
                                value="<?= htmlspecialchars($botiquin['nombre']); ?>" 
                                placeholder="Ingrese el nombre del botiquín" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="capacidad" class="form-label">Capacidad</label>
                        <div class="form-field">
                            <input type="number" name="capacidad" id="capacidad" class="form-input"
                                value="<?= htmlspecialchars($botiquin['capacidad']); ?>" 
                                placeholder="Ingrese la capacidad del botiquín" >
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear Botiquín</button>
                        <a href="/botiquines" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include __DIR__ . "/../../layouts/_footer.php";
