<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="page-header">
                <div class="page-header-content">
                    <h1 class="page-title">Editar Almacén</h1>
                    <p class="page-description">
                        Modifique los datos del almacén según sea necesario.
                    </p>
                </div>
            </div>

            <div class="form-container">
                <div class="card almacen-card">
                    <div class="card-header">
                        <h3>Información del Almacén</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="form almacen-form" id="editAlmacenForm">
                            <!-- Campo oculto para el ID -->
                            <input type="hidden" name="id" value="<?= htmlspecialchars($almacen['id']) ?>">

                            <div class="form-group">
                                <label for="nombre" class="form-label field-required">Nombre del Almacén</label>
                                <div class="form-field">
                                    <input type="text" name="nombre" id="nombre" class="form-input"
                                           value="<?= htmlspecialchars($almacen['nombre']) ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tipo" class="form-label field-required">Tipo de Almacén</label>
                                <div class="form-field">
                                    <select name="tipo" id="tipo" class="form-select" required>
                                        <option value="">Seleccione un tipo de almacén</option>
                                        <option value="GENERAL" <?= $almacen['tipo'] === 'GENERAL' ? 'selected' : '' ?>>
                                            GENERAL
                                        </option>
                                        <option value="PLANTA" <?= $almacen['tipo'] === 'PLANTA' ? 'selected' : '' ?>>
                                            PLANTA
                                        </option>
                                    </select>
                                    <div class="tipo-info">
                                        <i class="fas fa-info-circle"></i>
                                        <span>El tipo define el nivel de acceso y las funcionalidades disponibles.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="field-group">
                                <div class="form-group">
                                    <label for="id_hospital" class="form-label field-required">Hospital</label>
                                    <div class="form-field">
                                        <select name="id_hospital" id="id_hospital" class="form-select" required>
                                            <option value="">Seleccione un hospital</option>
                                            <?php foreach ($hospitals as $hospital): ?>
                                                <option value="<?= $hospital->getId() ?>" <?= $almacen['id_hospital'] == $hospital->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($hospital->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_planta" class="form-label">Planta <span class="field-optional">(Opcional)</span></label>
                                    <div class="form-field">
                                        <select name="id_planta" id="id_planta" class="form-select">
                                            <option value="">Seleccione una planta</option>
                                            <!-- Se rellenará dinámicamente con JavaScript -->
                                        </select>
                                        <div class="field-help">
                                            <i class="fas fa-info-circle"></i> Solo necesario para almacenes de tipo
                                            PLANTA
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                                <a href="<?= url('almacenes') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <a href="<?= url('almacenes.delete', ['id_almacen' => $almacen['id']]) ?>"
                                   class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Eliminar Almacén
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pasar los datos al JavaScript -->
    <script>
        // Inicializar variables globales necesarias para el script
        window.allPlantas = <?= json_encode($plantas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        // Para preseleccionar la planta en edición
        window.selectedPlantaId = '<?= $almacen['id_planta'] ?? '' ?>';

        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($errors)): ?>
            // Mostrar errores en un toast de tipo danger
            ToastSystem.danger(
                'Error al actualizar el almacén',
                `<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>`,
                null,
                {autoClose: false}
            );
            <?php endif; ?>
        });
    </script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
