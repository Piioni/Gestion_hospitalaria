<?php

use model\service\BotiquinService;
use model\service\PlantaService;

// Obtener el ID del botiquín
$id_botiquin = $_GET["id_botiquin"] ?? null;

if (!$id_botiquin || !is_numeric($id_botiquin)) {
    header('Location: ' . url('botiquines.dashboard', ['error' => 'id_invalid']));
    exit;
}

$botiquinService = new BotiquinService();
$plantaService = new PlantaService();

try {
    // Obtener información del botiquín
    $botiquin = $botiquinService->getBotiquinById($id_botiquin);
    
    if (!$botiquin) {
        header('Location: ' . url('botiquines.dashboard', ['error' => 'id_not_found']));
        exit;
    }
    
    // Obtener la planta asociada
    $planta = $plantaService->getPlantaById($botiquin->getIdPlanta());

    // Si es una solicitud de confirmación, eliminar el botiquín
    if (isset($_GET["confirm"])) {
        // Aquí se implementará la lógica para eliminar el botiquín
        // Por ahora, simulamos que se ha eliminado correctamente
        header('Location: ' . url('botiquines.dashboard', ['success' => 'deleted']));
        exit;
    }

    $title = "Confirmar Eliminación";
    include __DIR__ . "/../../../layouts/_header.php";
} catch (Exception $e) {
    // Error inesperado
    header('Location: ' . url('botiquines.dashboard', ['error' => 'unexpected']));
    exit;
}
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title text-center">Confirmar Eliminación</h1>
                <p class="text-center">¿Estás seguro de que deseas eliminar el botiquín <strong><?= htmlspecialchars($botiquin->getNombre()) ?></strong> de la planta <strong><?= htmlspecialchars($planta->getNombre()) ?></strong>?</p>
                <div class="text-center mt-4">
                    <div class="alert alert-warning">
                        <strong>Advertencia:</strong> Esta acción eliminará el botiquín y todos sus registros asociados. Esta acción no se puede deshacer.
                    </div>
                    <a href="<?= url('botiquines.dashboard') ?>" class="btn btn-secondary">Cancelar</a>
                    <a href="<?= url('botiquines.delete', ['id_botiquin' => $id_botiquin, 'confirm' => 1]) ?>" class="btn btn-danger">Confirmar Eliminación</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
