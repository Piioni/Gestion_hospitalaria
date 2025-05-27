<?php
// TODO : Implementar la lógica de eliminación de plantas

use model\service\PlantaService;

$id_planta = $_GET["id_planta"] ?? null;

if (!$id_planta || !is_numeric($id_planta)) {
    header('Location: ' . url('plantas.dashboard', ['error' => 'id_invalid']));
    exit;
}

$force = isset($_GET["force"]) && $_GET["force"] === "1";

try {
    $plantaService = new PlantaService();

    // Sí es una solicitud de confirmación, eliminar directamente
    if ($force || isset($_GET["confirm"])) {
        $result = $plantaService->deletePlanta($id_planta);
        header('Location: ' . url('plantas.dashboard', ['success' => 'deleted']));
        exit;
    }

    // Comprobar relaciones
    $relationInfo = $plantaService->checkPlantaRelations($id_planta);

    // Si no hay relaciones, eliminar directamente
    if ($relationInfo['canDelete']) {
        $result = $plantaService->deletePlanta($id_planta);
        header('Location: ' . url('plantas.dashboard', ['success' => 'deleted']));
        exit;
    }

    // Si llegamos aquí, hay relaciones y debemos mostrar la página de confirmación
    $planta = $relationInfo['planta'];
    $relatedHospitals = $relationInfo['relatedHospitals'];

    $title = "Confirmar Eliminación";
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
                <p class="text-center">¿Estás seguro de que deseas eliminar la planta <strong><?= htmlspecialchars($planta->getNombre()) ?></strong>?</p>
                <!-- TODO: Mostrar Almacen y Botiquines Dependientes.                -->
                <div class="text-center mt-4">
                    <a href="<?= url('plantas.dashboard') ?>" class="btn btn-secondary">Cancelar</a>
                    <a href="<?= url('plantas.delete', ['id_planta' => $id_planta, 'confirm' => 1]) ?>" class="btn btn-danger">Confirmar Eliminación</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include __DIR__ . "/../../../layouts/_footer.php";
