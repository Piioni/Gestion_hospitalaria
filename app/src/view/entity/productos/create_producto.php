<?php
use model\service\ProductoService;

$productoService = new ProductoService();

// Valores por defecto
$producto = [
    'codigo' => '',
    'nombre' => '',
    'descripcion' => '',
    'unidad_medida' => ''
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS);
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
    $unidad_medida = filter_input(INPUT_POST, 'unidad_medida', FILTER_SANITIZE_SPECIAL_CHARS);
    
    try {
        if ($productoService->create($codigo, $nombre, $descripcion, $unidad_medida)) {
            $success = true;
            // Reiniciar el formulario después de un envío exitoso
            $producto = [
                'codigo' => '',
                'nombre' => '',
                'descripcion' => '',
                'unidad_medida' => ''
            ];
        }
    } catch (\InvalidArgumentException $e) {
        $errors[] = $e->getMessage();
        // Mantener los valores ingresados en caso de error
        $producto = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'unidad_medida' => $unidad_medida
        ];
    } catch (Exception $e) {
        $errors[] = "Error al crear el producto: " . $e->getMessage();
        // Mantener los valores ingresados en caso de error
        $producto = [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'unidad_medida' => $unidad_medida
        ];
    }
}

$title = "Crear Producto";
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">Crear Producto</h1>
                <p class="page-description">
                    Complete el formulario para registrar un nuevo producto en el sistema.
                </p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Producto creado correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3>Información del Producto</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="codigo" class="form-label">Código</label>
                        <div class="form-field">
                            <input type="text" name="codigo" id="codigo" class="form-input"
                                value="<?= htmlspecialchars($producto['codigo']); ?>" 
                                placeholder="Ingrese el código del producto" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <div class="form-field">
                            <input type="text" name="nombre" id="nombre" class="form-input"
                                value="<?= htmlspecialchars($producto['nombre']); ?>" 
                                placeholder="Ingrese el nombre del producto" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <div class="form-field">
                            <textarea name="descripcion" id="descripcion" class="form-input"
                                rows="3" placeholder="Ingrese una descripción del producto"><?= htmlspecialchars($producto['descripcion']); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unidad_medida" class="form-label">Unidad de medida</label>
                        <div class="form-field">
                            <input type="text" name="unidad_medida" id="unidad_medida" class="form-input"
                                value="<?= htmlspecialchars($producto['unidad_medida']); ?>" 
                                placeholder="Ej: unidad, kg, litros">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear Producto</button>
                        <a href="/productos" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . "/../../layouts/_footer.php";
