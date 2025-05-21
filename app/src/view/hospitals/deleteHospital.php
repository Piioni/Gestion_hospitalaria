<?php

use model\service\HospitalService;

// Obtener el ID del hospital
$hospital_id = $_GET["id"] ?? null;

if (!$hospital_id || !is_numeric($hospital_id)) {
    header('Location: /hospitals/list?error=id_invalid');
    exit;
}

$force = isset($_GET["force"]) && $_GET["force"] === "1";

try {
    $hospitalService = new HospitalService();
    
    // Si es una solicitud de confirmación, eliminar directamente
    if ($force || isset($_GET["confirm"])) {
        $result = $hospitalService->deleteHospital($hospital_id, $force);
        header('Location: /hospitals/list?success=deleted');
        exit;
    }
    
    // Comprobar relaciones
    $relationInfo = $hospitalService->checkHospitalRelations($hospital_id);
    
    // Si no hay relaciones, eliminar directamente
    if ($relationInfo['canDelete']) {
        $result = $hospitalService->deleteHospital($hospital_id);
        header('Location: /hospitals/list?success=deleted');
        exit;
    }
    
    // Si llegamos aquí, hay relaciones y debemos mostrar la página de confirmación
    $hospital = $relationInfo['hospital'];
    $relatedPlants = $relationInfo['relatedPlants'];
    
    $title = "Confirmar Eliminación";
    include __DIR__ . "/../layouts/_header.php";
    
} catch (InvalidArgumentException $e) {
    // Error de validación
    header('Location: /hospitals/list?error=' . urlencode($e->getMessage()));
    exit;
} catch (Exception $e) {
    // Error inesperado
    error_log("Error al procesar eliminación: " . $e->getMessage());
    header('Location: /hospitals/list?error=unexpected');
    exit;
}
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Confirmar Eliminación</h1>
                <p class="page-description">
                    Se requiere su confirmación para eliminar este hospital.
                </p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-content">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">¡Atención! Se encontraron dependencias</h4>
                    <p>El hospital "<strong><?= htmlspecialchars($hospital['nombre']) ?></strong>" tiene plantas asociadas:</p>
                    
                    <ul class="dependencies-list">
                        <?php foreach ($relatedPlants as $plant): ?>
                            <li><?= htmlspecialchars($plant['nombre']) ?> (ID: <?= htmlspecialchars($plant['id_planta']) ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <p>Si elimina este hospital, las plantas asociadas podrían quedar sin referencia, afectando la integridad de los datos.</p>
                </div>
                
                <div class="form-actions">
                    <form action="" method="get" class="confirmation-form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($hospital_id) ?>">
                        <input type="hidden" name="force" value="1">
                        <button type="submit" class="btn btn-danger">Eliminar de todos modos</button>
                    </form>
                    <a href="/hospitals/list" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/_footer.php"; ?>
