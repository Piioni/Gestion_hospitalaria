<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="container-title">
            <h1 class="page-title">Nueva Reposición</h1>
            <div class="action-buttons">
                <a href="<?= url('reposiciones') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="card create-reposicion-card">
            <div class="card-body">
                <form action="<?= url('reposiciones.create') ?>" method="POST" class="form" id="reposicionForm">
                    <!-- Información del Producto -->
                    <div class="form-section">
                        <h4 class="section-subtitle">Información del Producto</h4>
                        <div class="product-info-container">
                            <div class="form-group producto-selector">
                                <label for="id_producto" class="form-label">Producto</label>
                                <select id="id_producto" name="id_producto" class="form-select" required>
                                    <option value="">Seleccione un producto</option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto->getId() ?>">
                                            <?= htmlspecialchars($producto->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group producto-selector">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" id="cantidad" name="cantidad" min="1" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor de almacenes (origen y destino) -->
                    <div class="almacenes-container two-columns" id="almacenes-container">
                        <!-- Columna Almacén de Origen -->
                        <div class="almacen-section" id="origen-section">
                            <h4 class="section-subtitle">Almacén de Origen</h4>
                            
                            <div class="form-group">
                                <label class="form-label">Tipo Almacén</label>
                                <div class="almacen-tipo-selector btn-group">
                                    <input type="hidden" id="origen_es_general" name="origen_es_general" value="0">
                                    <button type="button" id="origen_btn_planta" class="btn btn-primary active" onclick="selectAlmacenTipo(false)">PLANTA</button>
                                    <button type="button" id="origen_btn_general" class="btn btn-secondary" onclick="selectAlmacenTipo(true)">GENERAL</button>
                                </div>
                            </div>
                            
                            <div id="origen_selectors" class="selectors-container">
                                <div class="form-group">
                                    <label for="origen_hospital" class="form-label">Hospital</label>
                                    <select id="origen_hospital" name="origen_hospital" class="form-select" onchange="cargarPlantas()" required>
                                        <option value="">Seleccione un hospital</option>
                                        <?php foreach ($hospitales as $hospital): ?>
                                            <option value="<?= $hospital->getId() ?>">
                                                <?= htmlspecialchars($hospital->getNombre()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group" id="origen_planta_container">
                                    <label for="origen_planta" class="form-label">Planta</label>
                                    <select id="origen_planta" name="origen_planta" class="form-select" onchange="actualizarAlmacen()" required>
                                        <option value="">Seleccione una planta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_almacen" class="form-label">Almacén</label>
                                    <input type="text" id="origen_almacen_nombre" class="form-input" readonly>
                                    <input type="hidden" id="id_almacen" name="id_almacen">
                                </div>
                            </div>
                        </div>

                        <!-- Columna Botiquín de Destino -->
                        <div class="almacen-section" id="destino-section">
                            <h4 class="section-subtitle">Botiquín de Destino</h4>
                            <div id="destino_selectors" class="selectors-container">
                                <div class="form-group">
                                    <label for="destino_hospital" class="form-label">Hospital</label>
                                    <select id="destino_hospital" name="destino_hospital" class="form-select" 
                                            onchange="cargarPlantasPorHospital(this, document.getElementById('destino_planta'))"
                                            required>
                                        <option value="">Seleccione un hospital</option>
                                        <?php foreach ($hospitales as $hospital): ?>
                                            <option value="<?= $hospital->getId() ?>">
                                                <?= htmlspecialchars($hospital->getNombre()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group" id="destino_planta_container">
                                    <label for="destino_planta" class="form-label">Planta</label>
                                    <select id="destino_planta" name="destino_planta" class="form-select"
                                            onchange="cargarBotiquinesPorPlanta(this, document.getElementById('id_botiquin'))"
                                            required>
                                        <option value="">Seleccione una planta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_botiquin" class="form-label">Botiquín</label>
                                    <select id="id_botiquin" name="id_botiquin" class="form-select" required>
                                        <option value="">Seleccione un botiquín</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Reposición
                        </button>
                        <a href="<?= url('reposiciones') ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Datos para selectores dinámicos
    window.allPlantas = <?= json_encode(array_map(function ($planta) {
        return [
            'id_planta' => $planta->getId(),
            'nombre' => $planta->getNombre(),
            'id_hospital' => $planta->getIdHospital()
        ];
    }, $plantas)) ?>;

    window.allAlmacenes = <?= json_encode(array_map(function ($almacen) {
        return [
            'id_almacen' => $almacen->getId(),
            'nombre' => $almacen->getNombre(),
            'id_planta' => $almacen->getIdPlanta(),
            'tipo' => $almacen->getTipo(),
            'id_hospital' => $almacen->getIdHospital()
        ];
    }, $almacenes)) ?>;

    window.allBotiquines = <?= json_encode(array_map(function ($botiquin) {
        return [
            'id_botiquin' => $botiquin->getId(),
            'nombre' => $botiquin->getNombre(),
            'id_planta' => $botiquin->getIdPlanta()
        ];
    }, $botiquines)) ?>;

    // Añadir console.log para diagnóstico
    console.log("Plantas cargadas:", window.allPlantas);
    console.log("Almacenes cargados:", window.allAlmacenes);
    console.log("Botiquines cargados:", window.allBotiquines);
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>