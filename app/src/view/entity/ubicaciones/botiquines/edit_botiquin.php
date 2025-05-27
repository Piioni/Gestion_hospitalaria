<?php

use model\service\BotiquinService;
use model\service\PlantaService;

$botiquinesService = new BotiquinService();
$plantasService = new PlantaService();

$id_botiquin = $_GET['id_botiquin'] ?? null;

if (!$id_botiquin) {
    header("Location: " . url('botiquines.dashboard'));
    exit;
}

// Validar que exista un botiquín con el ID proporcionado
if ($botiquinesService->getBotiquinById($id_botiquin) == null) {
    header("Location: " . url('botiquines.dashboard', ['error' => 'id_not_found']));
    exit;
} else {
    $botiquin = $botiquinesService->getBotiquinById($id_botiquin);
}

$plantas = $plantasService->getAllPlantas();
$errors = [];
$success = false;

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_planta = filter_input(INPUT_POST, 'id_planta', FILTER_SANITIZE_NUMBER_INT);
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
    $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_SANITIZE_NUMBER_INT);

    try {
        $success = $botiquinesService->updateBotiquin($id_botiquin, $id_planta, $nombre, $capacidad);
        
        if ($success) {
            // Redirigir a la página de lista de botiquines con mensaje de éxito
            header('Location: ' . url('botiquines.dashboard', ['success' => 'updated']));
            exit;
        }
        
    } catch (InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
    } catch (Exception $e) {
        $errors[] = "Error al actualizar el botiquín: " . $e->getMessage();
    }
}

$title = "Editar Botiquín";
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">Editar Botiquín</h1>
                    <p class="page-description">
                        Modifique la información del botiquín "<?= htmlspecialchars($botiquin->getNombre()) ?>".
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
                    Botiquín actualizado correctamente.
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-content">
                    <form action="" method="POST" class="form">
                        <div class="form-group">
                            <label for="id_planta" class="form-label">Planta</label>
                            <div class="form-field">
                                <select name="id_planta" id="id_planta" class="form-select">
                                    <?php foreach ($plantas as $planta): ?>
                                        <option value="<?= $planta->getId() ?>" <?= $botiquin->getIdPlanta() == $planta->getId() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($planta->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre del Botiquín</label>
                            <div class="form-field">
                                <input type="text" id="nombre" name="nombre" class="form-input"
                                    value="<?= htmlspecialchars($botiquin->getNombre()) ?>" 
                                    placeholder="Ingrese el nombre del botiquín" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="capacidad" class="form-label">Capacidad</label>
                            <div class="form-field">
                                <input type="number" id="capacidad" name="capacidad" class="form-input"
                                    value="<?= htmlspecialchars($botiquin->getCapacidad()) ?>" 
                                    placeholder="Ingrese la capacidad del botiquín" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <a href="<?= url('botiquines.dashboard') ?>" class="btn btn-secondary">Volver</a>
                            <a href="<?= url('botiquines.delete', ['id_botiquin' => $botiquin->getId()]) ?>" class="btn btn-danger">Eliminar
                                botiquín</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
