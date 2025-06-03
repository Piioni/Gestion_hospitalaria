<?php
include __DIR__ . "/../../layouts/_header.php";

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

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                <div>
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card create-movimiento-card">
            <div class="card-header">
                <h3 class="mb-0">Registrar Movimiento</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('movimientos.create') ?>" method="POST" class="form" id="movimientoForm">
                    <!-- Tipo de Movimiento -->
                    <div class="movement-type-selector">
                        <div class="form-group">
                            <label class="form-label">Tipo de Movimiento</label>
                            <div class="movement-type-options">
                                <div class="movement-type-option">
                                    <input type="radio" id="tipo_traslado" name="tipo_movimiento" value="TRASLADO"
                                           <?= $movimiento['tipo_movimiento'] === 'TRASLADO' ? 'checked' : '' ?>
                                           onchange="toggleMovimientoFields()">
                                    <label for="tipo_traslado">
                                        <i class="bi bi-arrow-left-right"></i>
                                        <span>Traslado entre Almacenes</span>
                                    </label>
                                </div>
                                <div class="movement-type-option">
                                    <input type="radio" id="tipo_entrada" name="tipo_movimiento" value="ENTRADA"
                                           <?= $movimiento['tipo_movimiento'] === 'ENTRADA' ? 'checked' : '' ?>
                                           onchange="toggleMovimientoFields()">
                                    <label for="tipo_entrada">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                        <span>Entrada de Productos</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Producto -->
                    <div class="form-group">
                        <h4 class="section-subtitle">Información del Producto</h4>
                        <div class="product-info-container">
                            <div class="form-group">
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

                            <div class="form-group">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" id="cantidad" name="cantidad" min="1" value="<?= $movimiento['cantidad'] ?>" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor para Almacenes -->
                    <div class="almacenes-container" id="almacenes-container">
                        <!-- Almacén de Origen (solo visible para traslados) -->
                        <div class="almacen-section" id="origen-section">
                            <h4 class="section-subtitle">Almacén de Origen</h4>

                            <div class="form-group">
                                <div class="checkbox-toggle">
                                    <input type="checkbox" id="origen_es_general" name="origen_es_general" onchange="toggleAlmacenTipo('origen')">
                                    <label for="origen_es_general">Almacén general</label>
                                </div>
                            </div>

                            <div id="origen_selectors" class="selectors-container">
                                <div class="form-group">
                                    <label for="origen_hospital" class="form-label">Hospital</label>
                                    <select id="origen_hospital" name="origen_hospital" class="form-select" onchange="cargarPlantas('origen')">
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
                                    <select id="origen_planta" name="origen_planta" class="form-select" onchange="actualizarAlmacen('origen')">
                                        <option value="">Seleccione una planta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_origen" class="form-label">Almacén seleccionado</label>
                                    <input type="text" id="origen_almacen_nombre" class="form-input" readonly>
                                    <input type="hidden" id="id_origen" name="id_origen" value="<?= $movimiento['id_origen'] ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Almacén de Destino (visible para ambos tipos) -->
                        <div class="almacen-section" id="destino-section">
                            <h4 class="section-subtitle">Almacén de Destino</h4>

                            <div class="form-group">
                                <div class="checkbox-toggle">
                                    <input type="checkbox" id="destino_es_general" name="destino_es_general" onchange="toggleAlmacenTipo('destino')">
                                    <label for="destino_es_general">Almacén general</label>
                                </div>
                            </div>

                            <div id="destino_selectors" class="selectors-container">
                                <div class="form-group">
                                    <label for="destino_hospital" class="form-label">Hospital</label>
                                    <select id="destino_hospital" name="destino_hospital" class="form-select" onchange="cargarPlantas('destino')">
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
                                    <select id="destino_planta" name="destino_planta" class="form-select" onchange="actualizarAlmacen('destino')">
                                        <option value="">Seleccione una planta</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_destino" class="form-label">Almacén seleccionado</label>
                                    <input type="text" id="destino_almacen_nombre" class="form-input" readonly>
                                    <input type="hidden" id="id_destino" name="id_destino" value="<?= $movimiento['id_destino'] ?>">
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
// Datos para selectores dinámicos
const almacenes = <?= json_encode(array_map(function($a) {
        return [
            'id' => $a->getId(),
            'nombre' => $a->getNombre(),
            'tipo' => $a->getTipo(),
            'id_hospital' => $a->getIdHospital(),
            'id_planta' => $a->getIdPlanta()
        ];
    }, $almacenes)) ?>;

const plantas = <?= json_encode(array_map(function($p) {
        return [
            'id' => $p->getId(),
            'nombre' => $p->getNombre(),
            'id_hospital' => $p->getIdHospital()
        ];
    }, $plantas)) ?>;

</script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
