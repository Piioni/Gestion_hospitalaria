<?php
include __DIR__ . "/../../layouts/_header.php";

// Extraer variables relevantes para mejor legibilidad en la vista
$tipoMovimiento = $movimiento['tipo_movimiento'];
?>

<div class="page-section">
    <div class="container">
        <div class="container-title">
            <h1 class="page-title">Nuevo Movimiento</h1>
            <div class="action-buttons">
                <a href="<?= url('movimientos') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="card create-movimiento-card">
            <div class="card-body">
                <form action="<?= url('movimientos.create') ?>" method="POST" class="form" id="movimientoForm">
                    <!-- Sección de tipo movimiento e información de producto en 2 columnas -->
                    <div class="movement-info-grid">
                        <!-- Columna Información de Producto -->
                        <div class="movement-column">
                            <h4 class="section-subtitle">Información del Producto</h4>
                            <div class="product-info-container">
                                <div class="form-group producto-selector">
                                    <label for="id_producto" class="form-label">Producto</label>
                                    <select id="id_producto" name="id_producto" class="form-select" required>
                                        <option value="">Seleccione un producto</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto->getId() ?>" <?= $movimiento['id_producto'] == $producto->getId() ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($producto->getNombre()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group producto-selector">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" id="cantidad" name="cantidad" min="1"
                                           value="<?= $movimiento['cantidad'] ?>" class="form-input" required>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Tipo de Movimiento -->
                        <div class="movement-column">
                            <h4 class="section-subtitle">Tipo de Movimiento</h4>
                            <div class="movement-type-options centered-options">
                                <div class="movement-type-option">
                                    <input type="radio" id="tipo_traslado" name="tipo_movimiento" value="TRASLADO"
                                        <?= $tipoMovimiento === 'TRASLADO' ? 'checked' : '' ?>
                                           onchange="toggleMovimientoFields()">
                                    <label for="tipo_traslado">
                                        <i class="bi bi-arrow-left-right"></i>
                                        <span>Traslado entre Almacenes</span>
                                    </label>
                                </div>
                                <div class="movement-type-option">
                                    <input type="radio" id="tipo_entrada" name="tipo_movimiento" value="ENTRADA"
                                        <?= $tipoMovimiento === 'ENTRADA' ? 'checked' : '' ?>
                                           onchange="toggleMovimientoFields()">
                                    <label for="tipo_entrada">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                        <span>Entrada de Productos</span>
                                    </label>
                                </div>
                                <div class="movement-type-option">
                                    <input type="radio" id="tipo_devolucion" name="tipo_movimiento" value="DEVOLUCION"
                                        <?= $tipoMovimiento === 'DEVOLUCION' ? 'checked' : '' ?>
                                           onchange="toggleMovimientoFields()">
                                    <label for="tipo_devolucion">
                                        <i class="bi bi-arrow-return-left"></i>
                                        <span>Devolución de Botiquín</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor para Almacenes -->
                    <div class="almacenes-container" id="almacenes-container">
                        <!-- Almacén de Origen (solo visible para traslados) -->
                        <div class="almacen-section" id="origen-section">
                            <h4 class="section-subtitle">Almacén de Origen</h4>

                            <div class="form-group">
                                <label class="form-label">Tipo Almacén</label>
                                <div class="almacen-tipo-selector btn-group">
                                    <input type="hidden" id="origen_es_general" name="origen_es_general" value="0">
                                    <button type="button" id="origen_btn_planta" class="btn btn-secondary active"
                                            onclick="selectAlmacenTipo('origen', false)">PLANTA
                                    </button>
                                    <button type="button" id="origen_btn_general" class="btn btn-secondary"
                                            onclick="selectAlmacenTipo('origen', true)">GENERAL
                                    </button>
                                </div>
                            </div>

                            <div id="origen_selectors" class="selectors-container">
                                <div class="form-group">
                                    <label for="origen_hospital" class="form-label">Hospital</label>
                                    <select id="origen_hospital" name="origen_hospital" class="form-select"
                                            onchange="cargarPlantas('origen')">
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
                                    <select id="origen_planta" name="origen_planta" class="form-select"
                                            onchange="actualizarAlmacen('origen')">
                                        <option value="">Seleccione una planta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_origen" class="form-label">Almacén seleccionado</label>
                                    <input type="text" id="origen_almacen_nombre" class="form-input" readonly>
                                    <input type="hidden" id="id_origen" name="id_origen"
                                           value="<?= $movimiento['id_origen'] ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Botiquín de Origen (solo visible para devoluciones) -->
                        <div class="almacen-section" id="botiquin-section" style="display: none;">
                            <h4 class="section-subtitle">Botiquín de Origen</h4>

                            <div class="form-group">
                                <label for="botiquin_hospital" class="form-label">Hospital</label>
                                <select id="botiquin_hospital" name="botiquin_hospital" class="form-select" onchange="cargarPlantasPorHospital(this, document.getElementById('botiquin_planta'))">
                                    <option value="">Seleccione un hospital</option>
                                    <?php foreach ($hospitales as $hospital): ?>
                                        <option value="<?= $hospital->getId() ?>" <?= $movimiento['botiquin_hospital'] == $hospital->getId() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hospital->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="botiquin_planta" class="form-label">Planta</label>
                                <select id="botiquin_planta" name="botiquin_planta" class="form-select" onchange="cargarBotiquinesPorPlanta(this, document.getElementById('botiquin_origen'))">
                                    <option value="">Seleccione una planta</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="botiquin_origen" class="form-label">Botiquín</label>
                                <select id="botiquin_origen" name="botiquin_origen" class="form-select">
                                    <option value="">Seleccione un botiquín</option>
                                </select>
                            </div>
                        </div>

                        <!-- Almacén de Destino (visible para todos los tipos excepto devoluciones) -->
                        <div class="almacen-section" id="destino-section">
                            <h4 class="section-subtitle">Almacén de Destino</h4>

                            <div class="form-group">
                                <label class="form-label">Tipo Almacén</label>
                                <div class="almacen-tipo-selector btn-group">
                                    <input type="hidden" id="destino_es_general" name="destino_es_general" value="0">
                                    <button type="button" id="destino_btn_planta" class="btn btn-secondary active"
                                            onclick="selectAlmacenTipo('destino', false)">PLANTA
                                    </button>
                                    <button type="button" id="destino_btn_general" class="btn btn-secondary"
                                            onclick="selectAlmacenTipo('destino', true)">GENERAL
                                    </button>
                                </div>
                            </div>

                            <div id="destino_selectors" class="selectors-container">
                                <div class="form-group">
                                    <label for="destino_hospital" class="form-label">Hospital</label>
                                    <select id="destino_hospital" name="destino_hospital" class="form-select"
                                            onchange="cargarPlantas('destino')">
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
                                            onchange="actualizarAlmacen('destino')">
                                        <option value="">Seleccione una planta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_destino" class="form-label">Almacén seleccionado</label>
                                    <input type="text" id="destino_almacen_nombre" class="form-input" readonly>
                                    <input type="hidden" id="id_destino" name="id_destino"
                                           value="<?= $movimiento['id_destino'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Movimiento
                        </button>
                        <a href="<?= url('movimientos') ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Estructurar datos JSON para los selectores de manera más clara
    window.plantas = <?= json_encode(array_map(function ($p) {
        return [
            'id_planta' => $p->getId(),
            'nombre' => $p->getNombre(),
            'id_hospital' => $p->getIdHospital()
        ];
    }, $plantas)) ?>;

    window.almacenes = <?= json_encode(array_map(function ($a) {
        return [
            'id_almacen' => $a->getId(),
            'nombre' => $a->getNombre(),
            'tipo' => $a->getTipo(),
            'id_planta' => $a->getIdPlanta(),
            'id_hospital' => $a->getIdHospital()
        ];
    }, $almacenes)) ?>;

    window.botiquines = <?= json_encode(array_map(function ($b) {
        return [
            'id_botiquin' => $b->getId(),
            'nombre' => $b->getNombre(),
            'id_planta' => $b->getIdPlanta()
        ];
    }, $botiquines)) ?>;

    // Inicializar la página cuando se carga
    document.addEventListener('DOMContentLoaded', function () {
        // Aplicar la vista según el tipo de movimiento seleccionado
        toggleMovimientoFields();
        
        // Inicializar selectores si hay valores preseleccionados
        initBotiquinSelectors();
    });
    
    // Función para inicializar los selectores de botiquín si hay valores preseleccionados
    function initBotiquinSelectors() {
        const hospitalSelect = document.getElementById('botiquin_hospital');
        const plantaSelect = document.getElementById('botiquin_planta');
        const botiquinSelect = document.getElementById('botiquin_origen');

        if (hospitalSelect && hospitalSelect.value) {
            cargarPlantasPorHospital(hospitalSelect, plantaSelect);
        }

        if (plantaSelect && plantaSelect.value) {
            cargarBotiquinesPorPlanta(plantaSelect, botiquinSelect);
        }
    }
</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>