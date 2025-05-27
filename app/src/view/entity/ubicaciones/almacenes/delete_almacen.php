<?php

use model\service\AlmacenService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\StockService;

// Obtener el ID del almacén a eliminar
$id_almacen = $_GET["id_almacen"] ?? null;

if (!$id_almacen || !is_numeric($id_almacen)) {
    header('Location: ' . url('almacenes.dashboard', ['error' => 'id_invalid']));
    exit;
}

$almacenService = new AlmacenService();
$hospitalService = new HospitalService();
$plantaService = new PlantaService();
$stockService = new StockService();

try {
    // Obtener información del almacén
    $almacen = $almacenService->getAlmacenById($id_almacen);
    
    if (!$almacen) {
        header('Location: ' . url('almacenes.dashboard', ['error' => 'not_found']));
        exit;
    }
    
    // Obtener el hospital y planta asociados
    $hospital = $hospitalService->getHospitalById($almacen->getIdHospital());
    $planta = null;
    
    if ($almacen->getIdPlanta()) {
        $planta = $plantaService->getPlantaById($almacen->getIdPlanta());
    }
    
    // Verificar si tiene stock asociado
    // Por simplicidad, simularemos esta verificación
    $tieneStock = false; // Aquí se implementaría la lógica real para verificar si tiene stock

    // Si es una solicitud de confirmación, eliminar el almacén
    if (isset($_GET["confirm"])) {
        $result = $almacenService->deleteAlmacen($id_almacen);
        header('Location: ' . url('almacenes.dashboard', ['success' => 'deleted']));
        exit;
    }

    $title = "Confirmar Eliminación de Almacén";
    include __DIR__ . "/../../../layouts/_header.php";
} catch (Exception $e) {
    // Error inesperado
    header('Location: ' . url('almacenes.dashboard', ['error' => 'unexpected']));
    exit;
}
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title text-center">Confirmar Eliminación</h1>
                <p class="text-center">¿Estás seguro de que deseas eliminar el almacén <strong><?= htmlspecialchars($almacen->getNombre()) ?></strong>?</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Información del Almacén</h3>
            </div>
            <div class="card-body">
                <div class="almacen-details">
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($almacen->getNombre()) ?></p>
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($almacen->getTipo()) ?></p>
                    <p><strong>Hospital:</strong> <?= htmlspecialchars($hospital->getNombre()) ?></p>
                    
                    <?php if ($planta): ?>
                        <p><strong>Planta:</strong> <?= htmlspecialchars($planta->getNombre()) ?></p>
                    <?php endif; ?>
                </div>

                <div class="alert alert-warning">
                    <strong>Advertencia:</strong> Esta acción eliminará el almacén y todos sus registros asociados. Esta acción no se puede deshacer.
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?= url('almacenes.dashboard') ?>" class="btn btn-secondary">Cancelar</a>
                    <a href="<?= url('almacenes.delete', ['id_almacen' => $id_almacen, 'confirm' => 1]) ?>" class="btn btn-danger">Confirmar Eliminación</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
