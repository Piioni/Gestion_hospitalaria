<?php
include __DIR__ . "/../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="movimientos-section">
                <div class="container-title">
                    <h1 class="page-title">Historial de Movimientos</h1>
                    <div class="action-buttons">
                        <a href="?<?= isset($_GET['filtrar']) ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> <?= isset($_GET['filtrar']) ? 'Ocultar filtros' : 'Filtrar' ?>
                        </a>
                        <a href="<?= url('movimientos.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear movimiento
                        </a>
                        <a href="<?= url('movimientos') ?>" class="btn btn-outline">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['filtrar'])): ?>
                    <div class="filter-section card mb-4">
                        <div class="card-body">
                            <h3 class="filter-title">Filtrar movimientos</h3>
                            <form action="<?= url('movimientos.list') ?>" method="GET" class="filter-form">
                                <input type="hidden" name="filtrar" value="1">
                                <div class="filter-fields">
                                    <div class="filter-field">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select id="estado" name="estado" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="PENDIENTE" <?= $filtros['estado'] === 'PENDIENTE' ? 'selected' : '' ?>>
                                                Pendiente
                                            </option>
                                            <option value="COMPLETADO" <?= $filtros['estado'] === 'COMPLETADO' ? 'selected' : '' ?>>
                                                Completado
                                            </option>
                                            <option value="CANCELADO" <?= $filtros['estado'] === 'CANCELADO' ? 'selected' : '' ?>>
                                                Cancelado
                                            </option>
                                        </select>
                                    </div>
                                    <div class="filter-field">
                                        <label for="tipo_movimiento" class="form-label">Tipo</label>
                                        <select id="tipo_movimiento" name="tipo_movimiento" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="TRASLADO" <?= $filtros['tipo_movimiento'] === 'TRASLADO' ? 'selected' : '' ?>>
                                                Traslado
                                            </option>
                                            <option value="ENTRADA" <?= $filtros['tipo_movimiento'] === 'ENTRADA' ? 'selected' : '' ?>>
                                                Entrada
                                            </option>
                                        </select>
                                    </div>
                                    <div class="filter-field">
                                        <label for="producto" class="form-label">Producto</label>
                                        <select id="producto" name="producto" class="form-select">
                                            <option value="">Todos</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?= $producto->getId() ?>" <?= $filtros['producto'] == $producto->getId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($producto->getNombre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                    <a href="<?= url('movimientos.list') ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Resultados (<?= $total ?>)</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($movimientos)): ?>
                            <div class="empty-stock">
                                <p>No se encontraron movimientos con los criterios seleccionados.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($movimientos as $movimiento): ?>
                                        <tr>
                                            <td><?= isset($movimiento['id_movimiento']) ? $movimiento['id_movimiento'] : '-' ?></td>
                                            <td>
                                                <span class="estado-badge <?= $movimiento['tipo_movimiento'] === 'TRASLADO' ? 'bajo-stock' : 'stock-ok' ?>">
                                                    <?= $movimiento['tipo_movimiento'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($movimiento['nombre_producto'] ?? '-') ?>
                                            </td>
                                            <td><?= $movimiento['cantidad'] ?></td>
                                            <td><?= $movimiento['origen_nombre'] ? htmlspecialchars($movimiento['origen_nombre']) : 'N/A' ?></td>
                                            <td><?= htmlspecialchars($movimiento['destino_nombre'] ?? '-') ?></td>
                                            <td>
                                                <span class="estado-badge <?php
                                                if ($movimiento['estado'] === 'PENDIENTE') echo 'bajo-stock';
                                                elseif ($movimiento['estado'] === 'COMPLETADO') echo 'stock-ok';
                                                else echo 'badge-cancelado';
                                                ?>">
                                                    <?= $movimiento['estado'] ?>
                                                </span>
                                            </td>
                                            <td><?= isset($movimiento['fecha_movimiento']) ? date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($paginas > 1): ?>
                                <div class="pagination-container text-center mt-4">
                                    <nav aria-label="Navegación de páginas">
                                        <ul class="pagination">
                                            <?php if ($pagina_actual > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                       href="<?= url('movimientos.list') . '?' . http_build_query(array_merge($filtros, ['page' => $pagina_actual - 1] + (isset($_GET['filtrar']) ? ['filtrar' => 1] : []))) ?>"
                                                       aria-label="Anterior">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = max(1, $pagina_actual - 2); $i <= min($paginas, $pagina_actual + 2); $i++): ?>
                                                <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                                    <a class="page-link"
                                                       href="<?= url('movimientos.list') . '?' . http_build_query(array_merge($filtros, ['page' => $i] + (isset($_GET['filtrar']) ? ['filtrar' => 1] : []))) ?>">
                                                        <?= $i ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($pagina_actual < $paginas): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                       href="<?= url('movimientos.list') . '?' . http_build_query(array_merge($filtros, ['page' => $pagina_actual + 1] + (isset($_GET['filtrar']) ? ['filtrar' => 1] : []))) ?>"
                                                       aria-label="Siguiente">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include __DIR__ . "/../../layouts/_footer.php";

