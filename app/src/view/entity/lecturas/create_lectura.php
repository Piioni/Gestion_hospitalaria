<?php
include __DIR__ . "/../../layouts/_header.php";
?>
    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">Registrar Nueva Lectura</h1>
                    <p class="page-description">
                        Complete el formulario para registrar una nueva lectura de consumo de productos.
                    </p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Información de la Lectura</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                La lectura ha sido registrada correctamente.
                            </div>
                        <?php endif; ?>

                        <form action="<?= url('lecturas.create') ?>" method="POST" class="form">
                            <div class="field-group">
                                <div class="form-group">
                                    <label for="id_hospital" class="form-label field-required">Hospital:</label>
                                    <div class="form-field">
                                        <select id="id_hospital" name="id_hospital" class="form-select" required>
                                            <option value="">Seleccione un hospital</option>
                                            <?php foreach ($hospitales as $hospital): ?>
                                                <option value="<?= $hospital->getId() ?>" <?= $lectura['id_hospital'] == $hospital->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($hospital->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_planta" class="form-label field-required">Planta:</label>
                                    <div class="form-field">
                                        <select id="id_planta" name="id_planta" class="form-select" required>
                                            <option value="">Seleccione una planta</option>
                                            <!-- Se rellenará dinámicamente con JavaScript -->
                                        </select>
                                        <div class="field-help">
                                            <i class="fas fa-info-circle"></i> Primero seleccione un hospital
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="field-group">
                                <div class="form-group">
                                    <label for="id_botiquin" class="form-label field-required">Botiquín:</label>
                                    <div class="form-field">
                                        <select id="id_botiquin" name="id_botiquin" class="form-select" required>
                                            <option value="">Seleccione un botiquín</option>
                                            <!-- Se rellenará dinámicamente con JavaScript -->
                                        </select>
                                        <div class="field-help">
                                            <i class="fas fa-info-circle"></i> Primero seleccione una planta
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_producto" class="form-label field-required">Producto:</label>
                                    <div class="form-field">
                                        <select id="id_producto" name="id_producto" class="form-select" required>
                                            <option value="">Seleccione un producto</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?= $producto->getId() ?>" <?= $lectura['id_producto'] == $producto->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($producto->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cantidad" class="form-label field-required">Cantidad consumida:</label>
                                <div class="form-field">
                                    <input type="number" id="cantidad" name="cantidad" class="form-input" min="1"
                                           value="<?= htmlspecialchars($lectura['cantidad']) ?>" required>
                                    <div class="field-help">
                                        <i class="fas fa-info-circle"></i> Indique la cantidad exacta que se ha consumido
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Registrar lectura
                                </button>
                                <a href="<?= url('lecturas') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.allPlantas = <?= json_encode($plantas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        window.allBotiquines = <?= json_encode($botiquines, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        window.selectedPlantaId = "<?= htmlspecialchars($lectura['id_planta'] ?? '') ?>";
        window.selectedBotiquinId = "<?= htmlspecialchars($lectura['id_botiquin'] ?? '') ?>";

        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($errors)): ?>
            // Mostrar errores en un toast de tipo danger
            ToastSystem.danger(
                'Error al registrar la lectura',
                `<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>`,
                null,
                {autoClose: false}
            );
            <?php endif; ?>

            // Actualizar los mensajes de ayuda según el estado de selección
            const hospitalSelect = document.getElementById('id_hospital');
            const plantaSelect = document.getElementById('id_planta');
            const botiquinSelect = document.getElementById('id_botiquin');

            const plantaHelp = plantaSelect.parentElement.querySelector('.field-help');
            const botiquinHelp = botiquinSelect.parentElement.querySelector('.field-help');

            // Actualizar mensajes de ayuda cuando cambian las selecciones
            hospitalSelect.addEventListener('change', function () {
                if (this.value) {
                    plantaHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione una planta del hospital';
                } else {
                    plantaHelp.innerHTML = '<i class="fas fa-info-circle"></i> Primero seleccione un hospital';
                }
            });

            plantaSelect.addEventListener('change', function () {
                if (this.value) {
                    botiquinHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione un botiquín de la planta';
                } else {
                    botiquinHelp.innerHTML = '<i class="fas fa-info-circle"></i> Primero seleccione una planta';
                }
            });

            // Establecer mensajes iniciales según valores seleccionados
            if (hospitalSelect.value) {
                plantaHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione una planta del hospital';
            }

            if (plantaSelect.value) {
                botiquinHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione un botiquín de la planta';
            }
        });
    </script>

<?php
include __DIR__ . "/../../layouts/_footer.php";
