<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="reposiciones-section">
            <div class="overview-section">
                <h1 class="page-title">Gestión de Reposiciones</h1>
                <p class="lead-text">
                    Solicite reposiciones de stock desde almacén a botiquín y gestione su estado.
                </p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cards-container movement-cards">
                <div class="card dashboard-card movement-card">
                    <div class="card-body">
                        <h3 class="card-title"><i class="bi bi-arrow-repeat"></i> Nueva Reposición</h3>
                        <p class="card-text">
                            Solicite una nueva reposición de stock desde un almacén a un botiquín.
                        </p>
                        <div class="card-actions">
                            <a href="<?= url('reposiciones.create') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Solicitar Reposición
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card movement-card">
                    <div class="card-body">
                        <h3 class="card-title"><i class="bi bi-clock-history"></i> Historial de Reposiciones</h3>
                        <p class="card-text">
                            Consulte el historial completo de reposiciones con filtros avanzados.
                        </p>
                        <div class="card-actions">
                            <a href="<?= url('reposiciones.list') ?>" class="btn btn-info">
                                <i class="bi bi-search"></i> Ver Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-header mt-5">
                <h2 class="section-title">Reposiciones Pendientes</h2>
                <p class="section-description">
                    Listado de reposiciones que requieren confirmación o pueden ser canceladas.
                </p>
            </div>

            <?php if (empty($pendientes)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--light-text);"></i>
                    <div>
                        <h3>No hay reposiciones pendientes</h3>
                        <p>Actualmente no existen reposiciones que requieran confirmación.</p>
                    </div>
                    <div class="action-buttons">
                        <a href="<?= url('reposiciones.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nueva Reposición
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Almacén</th>
                                    <th>Botiquín</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pendientes as $repo): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($repo['nombre_producto']) ?></td>
                                        <td><?= $repo['cantidad'] ?></td>
                                        <td><?= htmlspecialchars($repo['nombre_almacen']) ?></td>
                                        <td><?= htmlspecialchars($repo['nombre_botiquin']) ?></td>
                                        <td><?= isset($repo['fecha_reposicion']) ? date('d/m/Y H:i', strtotime($repo['fecha_reposicion'])) : '-' ?></td>
                                        <td>
                                            <div class="acciones-stock">
                                                <button type="button"
                                                        class="btn btn-outline btn-icon btn-confirmar-repo"
                                                        data-id="<?= $repo['id_reposicion'] ?>"
                                                        title="Completar reposición">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline btn-icon btn-cancelar-repo"
                                                        data-id="<?= $repo['id_reposicion'] ?>"
                                                        title="Cancelar reposición">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="<?= url('reposiciones.list') ?>" class="btn btn-info">
                        <i class="bi bi-clock-history"></i> Ver Historial Completo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        mostrarToastPorGet();
        inicializarEventosDashboardReposiciones();
    });

    // --- Toast por GET ---
    function mostrarToastPorGet() {
        <?php if (isset($_GET['toast']) && isset($_GET['toastmsg'])): ?>
        const tipo = "<?= htmlspecialchars($_GET['toast']) ?>";
        const msg = "<?= htmlspecialchars($_GET['toastmsg']) ?>";
        switch (tipo) {
            case 'success':
                ToastSystem.success('Éxito', msg, null, {autoClose: true});
                break;
            case 'error':
                ToastSystem.danger('Error', msg, null, {autoClose: true});
                break;
            case 'info':
                ToastSystem.info('Información', msg, null, {autoClose: true});
                break;
            case 'warning':
                ToastSystem.warning('Advertencia', msg, null, {autoClose: true});
                break;
        }
        // Limpiar la URL para evitar mostrar el toast al recargar
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('toast');
            url.searchParams.delete('toastmsg');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
        <?php endif; ?>
    }

    function inicializarEventosDashboardReposiciones() {
        document.querySelectorAll('.btn-confirmar-repo').forEach(function (btn) {
            btn.addEventListener('click', function () {
                mostrarToastConfirmacionRepo('confirmar', this.dataset.id);
            });
        });
        document.querySelectorAll('.btn-cancelar-repo').forEach(function (btn) {
            btn.addEventListener('click', function () {
                mostrarToastConfirmacionRepo('cancelar', this.dataset.id);
            });
        });
        document.body.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-toast-accion-repo')) {
                const action = e.target.dataset.action;
                const id = e.target.dataset.id;
                ejecutarAccionRepo(action, id);
            }
        });
    }

    function mostrarToastConfirmacionRepo(tipo, id) {
        if (tipo === 'confirmar') {
            ToastSystem.warning(
                'Confirmar reposición',
                '¿Está seguro que desea confirmar esta reposición? Esta acción no se puede deshacer.',
                `<button class="btn btn-success btn-toast-accion-repo" data-action="confirmar" data-id="${id}"><i class="bi bi-check-circle"></i> Confirmar</button>`,
                {autoClose: false}
            );
        } else if (tipo === 'cancelar') {
            ToastSystem.danger(
                'Cancelar reposición',
                '¿Está seguro que desea cancelar esta reposición? Esta acción no se puede deshacer.',
                `<button class="btn btn-danger btn-toast-accion-repo" data-action="cancelar" data-id="${id}"><i class="bi bi-x-circle"></i> Cancelar</button>`,
                {autoClose: false}
            );
        }
    }

    function ejecutarAccionRepo(action, id) {
        const url = action === 'confirmar'
            ? '<?= url('reposiciones.complete') ?>'
            : '<?= url('reposiciones.cancel') ?>';
        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            ToastSystem.clearAll();
            if (data.success) {
                ToastSystem.success(
                    action === 'confirmar' ? 'Reposición Confirmada' : 'Reposición Cancelada',
                    data.msg,
                    null,
                    {autoClose: true}
                );
                setTimeout(() => location.reload(), 1200);
            } else {
                ToastSystem.danger('Error', data.msg, null, {autoClose: true});
            }
        });
    }
</script>
