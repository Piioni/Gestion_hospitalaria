<?php
include __DIR__ . "/../../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Botiquín</h1>
                <p class="page-description">
                    Complete el formulario para registrar un nuevo botiquín en el sistema.
                </p>
            </div>
        </div>

        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h3>Información del Botiquín</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="form" id="createBotiquinForm">
                        <div class="form-group">
                            <label for="id_planta" class="form-label field-required">Planta</label>
                            <div class="form-field">
                                <select name="id_planta" id="id_planta" class="form-select" required>
                                    <option value="">Seleccione una planta</option>
                                    <?php foreach ($plantas as $planta): ?>
                                        <option value="<?= htmlspecialchars($planta->getId()); ?>"
                                            <?= $botiquin['id_planta'] == $planta->getId() ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($planta->getNombre()); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombre" class="form-label field-required">Nombre del Botiquín</label>
                            <div class="form-field">
                                <input type="text" name="nombre" id="nombre" class="form-input"
                                    value="<?= htmlspecialchars($botiquin['nombre']); ?>" 
                                    placeholder="Ingrese el nombre del botiquín" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="capacidad" class="form-label field-required">Capacidad</label>
                            <div class="form-field">
                                <input type="number" name="capacidad" id="capacidad" class="form-input"
                                    value="<?= htmlspecialchars($botiquin['capacidad']); ?>" 
                                    placeholder="Ingrese la capacidad del botiquín" required>
                                <div class="field-help">
                                    <i class="fas fa-info-circle"></i> Número máximo de productos distintos que puede almacenar
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Crear Botiquín
                            </button>
                            <a href="<?= url('botiquines') ?>" class="btn btn-secondary">
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
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($errors)): ?>
            // Mostrar errores en un toast de tipo danger
            ToastSystem.danger(
                'Error al crear el botiquín',
                `<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>`,
                null,
                { autoClose: false }
            );
        <?php endif; ?>

        <?php if ($success): ?>
            // Mostrar mensaje de éxito
            ToastSystem.success(
                'Botiquín creado',
                'El botiquín se ha creado correctamente.',
                null,
                { autoClose: true, closeDelay: 5000 }
            );
        <?php endif; ?>

        // Validación del formulario
        document.getElementById('createBotiquinForm').addEventListener('submit', function(event) {
            const planta = document.getElementById('id_planta').value;
            const nombre = document.getElementById('nombre').value;
            const capacidad = document.getElementById('capacidad').value;
            
            let isValid = true;
            let errorMessages = [];

            if (!planta) {
                errorMessages.push('Debe seleccionar una planta');
                isValid = false;
            }
            
            if (!nombre.trim()) {
                errorMessages.push('El nombre del botiquín es obligatorio');
                isValid = false;
            }
            
            if (!capacidad || capacidad <= 0) {
                errorMessages.push('La capacidad debe ser un número mayor que cero');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
                ToastSystem.warning(
                    'Formulario incompleto',
                    errorMessages.join('<br>'),
                    null,
                    { autoClose: true, closeDelay: 7000 }
                );
            }
        });
    });
</script>

<?php
include __DIR__ . "/../../../layouts/_footer.php";
