<?php

// TODO: Implementar function que le permita al usuario escoger un almacen destino para transferir los productos del botiquín

use model\service\BotiquinService;
use model\service\PlantaService;
use model\service\AlmacenService;
use model\service\StockService;

// Obtener el ID del botiquín
$id_botiquin = $_GET["id_botiquin"] ?? null;

if (!$id_botiquin || !is_numeric($id_botiquin)) {
    header('Location: ' . url('botiquines.dashboard', ['error' => 'id_invalid']));
    exit;
}

$botiquinService = new BotiquinService();
$plantaService = new PlantaService();
$almacenService = new AlmacenService();
$stockService = new StockService();

try {
    // Obtener información del botiquín
    $botiquin = $botiquinService->getBotiquinById($id_botiquin);

    if (!$botiquin) {
        header('Location: ' . url('botiquines.dashboard', ['error' => 'id_not_found']));
        exit;
    }

    // Obtener la planta asociada y los productos del botiquín
    $planta = $plantaService->getPlantaById($botiquin->getIdPlanta());
    $cantidadProductos = $botiquinService->getBotiquinProducts($id_botiquin);
    $hasProducts = $stockService->botiquinHasProducts($id_botiquin);
    
    // Obtener todos los almacenes para que el usuario elija un destino
    $almacenes = $almacenService->getAllAlmacenes();

    // Si es una solicitud de confirmación, eliminar el botiquín
    if (isset($_POST["confirm"])) {
        try {
            $idAlmacenDestino = null;
            if ($hasProducts) {
                $idAlmacenDestino = $_POST["almacen_destino"] ?? null;
                
                if (!$idAlmacenDestino || !is_numeric($idAlmacenDestino)) {
                    throw new InvalidArgumentException("Debe seleccionar un almacén destino válido para transferir los productos.");
                }
            }
            
            $botiquinService->deleteBotiquin($id_botiquin, $idAlmacenDestino);
            header('Location: ' . url('botiquines.dashboard', ['success' => 'deleted']));
            exit;
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            error_log("Error al procesar eliminación: " . $e->getMessage());
            $error = "Ha ocurrido un error inesperado al procesar la eliminación.";
        }
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
                    <p class="text-center">¿Estás seguro de que deseas eliminar el botiquín
                        <strong><?= htmlspecialchars($botiquin->getNombre()) ?></strong> de la planta
                        <strong><?= htmlspecialchars($planta->getNombre()) ?></strong>?</p>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('botiquines.delete', ['id_botiquin' => $id_botiquin]) ?>">
                        <div class="text-center mt-4">
                            <div class="alert alert-warning">
                                <div class="alert-message">
                                    <strong>Advertencia:</strong> Esta acción eliminará el botiquín y no podrá ser recuperado.
                                </div>
                                <div class="alert-message">
                                    Esta acción no es reversible.
                                </div>
                            </div>
                            
                            <?php if ($hasProducts): ?>
                                <div class="alert alert-info">
                                    <strong>El botiquín tiene <?= $cantidadProductos ?> productos asociados.</strong>
                                    <p>Debes seleccionar un almacén al que transferir estos productos:</p>
                                    
                                    <div class="form-group mt-3">
                                        <label for="almacen_destino" class="form-label">Almacén destino:</label>
                                        <select name="almacen_destino" id="almacen_destino" class="form-select" required>
                                            <option value="">Selecciona un almacén...</option>
                                            <?php foreach ($almacenes as $almacen): ?>
                                                <option value="<?= $almacen->getId() ?>">
                                                    <?= htmlspecialchars($almacen->getNombre()) ?> 
                                                    (<?= $almacen->getIdPlanta() ? 'Planta' : 'Hospital' ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <a href="<?= url('botiquines.dashboard') ?>" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" name="confirm" value="1" class="btn btn-danger">Confirmar Eliminación</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
