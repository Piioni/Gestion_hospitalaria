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
    $cantidadProductos = $botiquinService->getStockByBotiquinId($id_botiquin);
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

    // Añadir el script de toasts a los scripts que se cargarán
    $scripts = "toasts.js";
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

                    <form method="POST" action="<?= url('botiquines.delete', ['id_botiquin' => $id_botiquin]) ?>"
                          id="deleteBotiquinForm">
                        <div class="text-center mt-4">
                            <?php if ($hasProducts): ?>
                                <div class="alert alert-info">
                                    <strong>El botiquín tiene <?= $cantidadProductos ?> productos asociados.</strong>
                                    <p>Debes seleccionar un almacén al que transferir estos productos:</p>

                                    <div class="form-group mt-3">
                                        <label for="almacen_destino" class="form-label">Almacén destino:</label>
                                        <select name="almacen_destino" id="almacen_destino" class="form-select"
                                                required>
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
                                <button type="submit" name="confirm" value="1" class="btn btn-danger">Confirmar
                                    Eliminación
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mostrar un toast de advertencia sobre la eliminación
            ToastSystem.warning(
                'Advertencia',
                'Esta acción eliminará el botiquín y no podrá ser recuperado. Esta acción no es reversible.',
                null,
                {autoClose: false}
            );

            <?php if (isset($error)): ?>
            // Mostrar errores en un toast de tipo danger
            ToastSystem.danger(
                'Error al eliminar el botiquín',
                '<?= htmlspecialchars($error) ?>',
                null,
                {autoClose: false}
            );
            <?php endif; ?>

            <?php if ($hasProducts): ?>
            // Si el botiquín tiene productos, mostrar un toast informativo
            ToastSystem.info(
                'Transferencia de productos',
                'El botiquín tiene productos que serán transferidos al almacén seleccionado.',
                null,
                {autoClose: true, closeDelay: 8000}
            );

            // Validar el formulario antes de enviar
            document.getElementById('deleteBotiquinForm').addEventListener('submit', function (event) {
                const almacenDestino = document.getElementById('almacen_destino').value;

                if (!almacenDestino) {
                    event.preventDefault();
                    ToastSystem.danger(
                        'Error',
                        'Debe seleccionar un almacén destino para transferir los productos.',
                        null,
                        {autoClose: true, closeDelay: 5000}
                    );
                } else {
                    // Confirmar la eliminación
                    if (!confirm('¿Está seguro de eliminar este botiquín? Esta acción no se puede deshacer.')) {
                        event.preventDefault();
                    }
                }
            });
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
