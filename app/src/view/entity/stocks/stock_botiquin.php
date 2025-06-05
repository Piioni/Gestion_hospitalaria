<?php
include __DIR__ . "/../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="botiquines-section">
                <div class="container-title <?= !$filtrarActivo ? 'mt-3' : '' ?>">
                    <h2 class="section-title"> Stock de botiquines</h2>
                    <div class="action-buttons">
                        <a href="?<?= $filtrarActivo ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> <?= $filtrarActivo ? 'Ocultar filtros' : 'Filtrar' ?>
                        </a>
                        <a href="<?= url('botiquines') ?>" class="btn btn-secondary">Ver Botiquines</a>
                        <a href="<?= url('stocks') ?>" class="btn btn-secondary">Dashboard de Stock</a>
                    </div>
                </div>

                <?php if ($filtrarActivo): ?>
                    <div class="filter-section card mb-4">
                        <div class="card-body">
                            <h3 class="filter-title">Filtrar botiquines</h3>
                            <form action="" method="GET" class="filter-form">
                                <input type="hidden" name="filtrar" value="1">
                                <div class="filter-fields">
                                    <div class="filter-field">
                                        <label for="planta" class="form-label">Planta:</label>
                                        <div class="form-field">
                                            <select name="planta" id="planta" class="form-select">
                                                <option value="">Todas las plantas</option>
                                                <?php foreach ($plantas as $planta): ?>
                                                    <option value="<?= $planta->getId() ?>" <?= $filtro_plantas == $planta->getId() ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($planta->getNombre()) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="filter-field">
                                        <label for="nombre" class="form-label">Nombre:</label>
                                        <div class="form-field">
                                            <input type="text" name="nombre" id="nombre" class="form-input"
                                                   placeholder="Filtrar por nombre de botiquín"
                                                   value="<?= isset($filtro_nombre) ? htmlspecialchars($filtro_nombre) : '' ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrar
                                    </button>
                                    <?php if ($filtro_plantas || $filtro_botiquin || $filtro_nombre): ?>
                                        <a href="<?= url('stocks.botiquines') ?>" class="btn btn-secondary"><i
                                                    class="bi bi-x-circle"></i> Limpiar filtros</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if (empty($botiquines)): ?>
                    <div class="empty-state">
                        <?php if ($filtro_plantas || $filtro_nombre): ?>
                            No hay botiquines que coincidan con los criterios de filtrado.
                        <?php elseif ($filtro_botiquin): ?>
                            No se encontró el botiquín especificado.
                        <?php else: ?>
                            No hay botiquines registrados o asignados en el sistema.
                        <?php endif; ?>
                        <?php if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA'])): ?>
                            <a href="<?= url('botiquines.create') ?>" class="btn btn-primary"><i
                                        class="bi bi-plus-circle"></i> Crear un botiquín</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="botiquines-list">
                        <?php foreach ($botiquines as $botiquin):
                            // Obtener la planta asociada
                            try {
                                $planta = $plantaService->getPlantaById($botiquin->getIdPlanta());
                                $plantaNombre = $planta->getNombre();
                            } catch (Exception $e) {
                                $plantaNombre = "Error al cargar la planta";
                            }

                            // Determinar si este botiquín es el seleccionado para mantenerlo expandido
                            $isSelected = $filtro_botiquin && $filtro_botiquin == $botiquin->getId();
                            ?>
                            <div class="botiquin-card card">
                                <div class="collapsible-header planta-header <?= $isSelected ? 'active' : '' ?>"
                                     onclick="toggleCollapsible(this, 'botiquin-<?= $botiquin->getId() ?>')">
                                    <h3 class="planta-name"><?= htmlspecialchars($botiquin->getNombre()) ?></h3>
                                    <span class="collapsible-icon">▼</span>
                                </div>

                                <div id="botiquin-<?= $botiquin->getId() ?>"
                                     class="collapsible-content <?= $isSelected ? 'active' : '' ?>">
                                    <div class="card-body">
                                        <div class="botiquin-details">
                                            <div class="botiquin-info">
                                                <?php
                                                // Obtener el nombre del hospital asociado al botiquín
                                                $idHospital = $plantaService->getPlantaById($botiquin->getIdPlanta())->getIdHospital();
                                                $nombreHospital = $hospitalService->getHospitalById($idHospital)->getNombre();
                                                ?>
                                                <p><strong>Hospital: </strong> <?= htmlspecialchars($nombreHospital) ?>
                                                </p>
                                                <p><strong>Ubicación:</strong> <?= htmlspecialchars($plantaNombre) ?>
                                                </p>
                                                <p><strong>Capacidad:</strong> <?= $botiquin->getCapacidad() ?>
                                                    medicamentos
                                                </p>
                                            </div>
                                        </div>

                                        <hr class="divider">

                                        <div class="botiquines-section">
                                            <h4 class="subsection-title">Productos en Stock</h4>
                                            <?php
                                            // Obtenemos los productos en stock para este botiquín usando el servicio
                                            $productosEnStock = $stockService->getStockByBotiquinId($botiquin->getId());

                                            if (empty($productosEnStock)): ?>
                                                <div class="empty-plants">
                                                    <p>No hay productos en stock para este botiquín.</p>
                                                    <?php if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA'])): ?>
                                                        <a href="<?= url('reposiciones.create', ['id_botiquin' => $botiquin->getId()]) ?>"
                                                           class="btn btn-primary">
                                                            <i class="bi bi-plus circle"></i> Reponer productos
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Stock Actual</th>
                                                            <th>Cantidad Pactada</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($productosEnStock as $stock):
                                                            $producto = $productoService->getProductoById($stock->getIdProducto());
                                                            ?>
                                                            <tr>
                                                                <td><?= $producto ? htmlspecialchars($producto->getNombre()) : 'Producto no encontrado' ?></td>
                                                                <td class="<?= $estadoClase ?>"><?= $stock->getCantidad() ?></td>
                                                                <!--TODO: Mostrar la cantidad pactada si existe-->
                                                                <td class="actions-column">
                                                                    <!--TODO: Implementar la ruta a lecuturas correcta-->
                                                                    <div class="btn-container">
                                                                        <a href="<?= url('lecturas.create', ['id_stock' => $stock->getId()]) ?>"
                                                                           class="btn btn-sm btn-warning"
                                                                           title="Consumir">
                                                                            <i class="bi bi-dash-circle"></i> Consumir
                                                                        </a>
                                                                        <a href="<?= url('reposiciones.create', ['id_botiquin' => $botiquin->getId(), 'id_producto' => $stock->getIdProducto()]) ?>"
                                                                           class="btn btn-sm btn-success"
                                                                           title="Reponer">
                                                                            <i class="bi bi-plus-circle"></i> Reponer
                                                                        </a>
                                                                        <!--TODO: Boton para editar cantidad pactada -->
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleCollapsible(header, contentId) {
            const content = document.getElementById(contentId);
            content.classList.toggle('active');
            header.classList.toggle('active');
        }

        // Actualizar automáticamente el formulario cuando cambia el select
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('planta').addEventListener('change', function () {
                this.form.submit();
            });

        });
    </script>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>