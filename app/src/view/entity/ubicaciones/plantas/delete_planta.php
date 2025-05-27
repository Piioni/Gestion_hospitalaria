<?php

use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\PlantaService;

$id_planta = $_GET["id_planta"] ?? null;

if (!$id_planta || !is_numeric($id_planta)) {
    header('Location: ' . url('plantas.dashboard', ['error' => 'id_invalid']));
    exit;
}

$plantaService = new PlantaService();
$almacenService = new AlmacenService();
$botiquinService = new BotiquinService();
$hospitalService = new HospitalService();

$error = null;

try {
    // Obtener información de la planta
    $planta = $plantaService->getPlantaById($id_planta);
    if (!$planta) {
        header('Location: ' . url('plantas.dashboard', ['error' => 'id_not_found']));
        exit;
    }

    // Comprobar relaciones
    $almacen = $almacenService->getAlmacenByPlantaId($id_planta);
    $botiquines = $botiquinService->getBotiquinesByPlantaId($id_planta);
    $hospital = $hospitalService->getHospitalById($planta->getIdHospital());

    // Si es una solicitud de confirmación y no hay dependencias, eliminar directamente
    if (isset($_POST["confirm"])) {
        try {
            if ($almacen) {
                throw new InvalidArgumentException("No se puede eliminar la planta porque tiene un almacén asociado.");
            }

            if (!empty($botiquines)) {
                throw new InvalidArgumentException("No se puede eliminar la planta porque tiene botiquines asociados.");
            }

            $success = $plantaService->deletePlanta($id_planta);

            if ($success) {
                header('Location: ' . url('plantas.dashboard', ['success' => 'deleted']));
                exit;
            } else {
                throw new Exception("No se pudo eliminar la planta. Por favor, inténtalo de nuevo más tarde.");
            }
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            error_log("Error al procesar eliminación: " . $e->getMessage());
            $error = "Ha ocurrido un error inesperado al procesar la eliminación.";
        }
    }

    // Incluir el script de toasts.js
    $title = "Confirmar Eliminación";
    $scripts = "toasts.js";
    include __DIR__ . "/../../../layouts/_header.php";

} catch (InvalidArgumentException $e) {
    // Error de validación
    header('Location: ' . url('plantas.dashboard', ['error' => urlencode($e->getMessage())]));
    exit;
} catch (Exception $e) {
    // Error inesperado
    error_log("Error al procesar eliminación: " . $e->getMessage());
    header('Location: ' . url('plantas.dashboard', ['error' => 'unexpected']));
    exit;
}
?>

    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title text-center">Confirmar Eliminación</h1>
                    <p class="text-center">¿Estás seguro de que deseas eliminar la planta
                        <strong><?= htmlspecialchars($planta->getNombre()) ?></strong>
                        del hospital
                        <strong><?= htmlspecialchars($hospital->getNombre()) ?></strong>?
                    </p>

                    <form method="POST" action="<?= url('plantas.delete', ['id_planta' => $id_planta]) ?>">
                        <div class="text-center mt-5">
                            <!-- Los botones principales siempre visibles -->
                            <div class="action-buttons-row text-center mt-5">
                                <a href="<?= url('plantas.dashboard') ?>" class="btn btn-secondary mx-2">Cancelar</a>
                                <?php if (!$almacen && empty($botiquines)): ?>
                                    <button type="submit" name="confirm" value="1" class="btn btn-danger mx-2">Confirmar
                                        Eliminación
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-danger mx-2" disabled>
                                        Confirmar Eliminación
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script que utiliza toasts.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toast de advertencia
            ToastSystem.warning(
                'Advertencia',
                'Esta acción desactivará la planta, por lo que no podrá ser utilizada. Esta acción solo es reversible por un Administrador.',
                null,
                {autoClose: true, closeDelay: 7000}
            );

            <?php if ($almacen): ?>
            // Toast para el almacén
            ToastSystem.danger(
                'No se puede eliminar la planta',
                'Tiene un almacén asociado. Primero debes eliminar el almacén asociado a esta planta.',
                `<a href="<?= url('almacenes.delete', ['id_almacen' => $almacen->getId()]) ?>" 
                   class="btn btn-primary">
                    Eliminar almacén "<?= htmlspecialchars($almacen->getNombre()) ?>"
                </a>`,
                {autoClose: false}
            );
            <?php endif; ?>

            <?php if (!empty($botiquines)): ?>
            // Toast para los botiquines
            ToastSystem.danger(
                'No se puede eliminar la planta',
                'Tiene <?= count($botiquines) ?> botiquín(es) asociado(s). Primero debes eliminar todos los botiquines asociados a esta planta.',
                `<a href="<?= url('botiquines.dashboard', ['planta' => $planta->getId()]) ?>" 
                   class="btn btn-primary">
                    Ver botiquines de la planta
                </a>`,
                {autoClose: false}
            );
            <?php endif; ?>

            <?php if (isset($error)): ?>
            // Toast para errores de procesamiento
            ToastSystem.danger('Error', '<?= htmlspecialchars($error) ?>', null, {autoClose: false});
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
