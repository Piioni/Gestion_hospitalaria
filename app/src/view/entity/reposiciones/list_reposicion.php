<?php
include __DIR__ . "/../../layouts/_header.php";
?>

    <div class="page-section">
        <div class="container">
            <div class="reposiciones-section">
                <div class="container-title">
                    <h1 class="page-title">Historial de Reposiciones</h1>
                    <div class="action-buttons">
                        <a href="?<?= isset($_GET['filtrar']) ? '' : 'filtrar=1' ?>" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> <?= isset($_GET['filtrar']) ? 'Ocultar filtros' : 'Filtrar' ?>
                        </a>
                        <a href="<?= url('reposiciones.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear reposición
                        </a>
                        <a href="<?= url('reposiciones') ?>" class="btn btn-outline">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['filtrar'])): ?>
                    <div class="filter-section card mb-4">
                        <div class="card-body">
                            <h3 class="filter-title">Filtrar reposiciones</h3>
                            <form action="<?= url('reposiciones.list') ?>" method="GET" class="filter-form">
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
                                    <a href="<?= url('reposiciones.list') ?>" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <?php if (empty($reposiciones)): ?>
                        <div class="empty-stock">
                            <p>No se encontraron reposiciones con los criterios seleccionados.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Almacén</th>
                                <th>Botiquín</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reposiciones as $reposicion): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reposicion['nombre_almacen'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($reposicion['nombre_botiquin'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($reposicion['nombre_producto'] ?? '-') ?></td>
                                    <td class="table-numeric"><?= $reposicion['cantidad'] ?></td>
                                    <td>
                                        <span class="badge <?php
                                        if ($reposicion['estado'] === 'PENDIENTE') echo 'bajo-stock';
                                        elseif ($reposicion['estado'] === 'COMPLETADO') echo 'stock-ok';
                                        else echo 'badge-cancelado';
                                        ?>">
                                            <?= $reposicion['estado'] ?>
                                        </span>
                                    </td>
                                    <td><?= isset($reposicion['fecha_reposicion']) ? date('d/m/Y H:i', strtotime($reposicion['fecha_reposicion'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if ($paginas > 1): ?>
                            <div class="pagination-container text-center mt-4">
                                <nav aria-label="Navegación de páginas">
                                    <ul class="pagination">
                                        <?php if ($pagina_actual > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                   href="<?= url('reposiciones.list') . '?' . http_build_query(array_merge($filtros, ['page' => $pagina_actual - 1] + (isset($_GET['filtrar']) ? ['filtrar' => 1] : []))) ?>"
                                                   aria-label="Anterior">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $pagina_actual - 2); $i <= min($paginas, $pagina_actual + 2); $i++): ?>
                                            <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                                <a class="page-link"
                                                   href="<?= url('reposiciones.list') . '?' . http_build_query(array_merge($filtros, ['page' => $i] + (isset($_GET['filtrar']) ? ['filtrar' => 1] : []))) ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($pagina_actual < $paginas): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                   href="<?= url('reposiciones.list') . '?' . http_build_query(array_merge($filtros, ['page' => $pagina_actual + 1] + (isset($_GET['filtrar']) ? ['filtrar' => 1] : []))) ?>"
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

<?php
include __DIR__ . "/../../layouts/_footer.php";
