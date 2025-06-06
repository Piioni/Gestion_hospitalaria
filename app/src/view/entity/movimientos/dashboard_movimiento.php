<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="movimientos-section">
            <div class="overview-section">
                <h1 class="page-title">Gestión de Movimientos</h1>
                <p class="lead-text">
                    Control de movimientos de productos entre almacenes y registro de nuevas entradas.
                </p>
            </div>

            <div class="cards-container movement-cards">
                <div class="card dashboard-card movement-card">
                    <div class="card-body">
                        <h3 class="card-title"><i class="bi bi-arrow-left-right"></i> Traslado entre Almacenes</h3>
                        <p class="card-text">
                            Realice movimientos de productos entre diferentes almacenes del sistema.
                        </p>
                        <div class="card-actions">
                            <a href="<?= url('movimientos.create') ?>?tipo=TRASLADO" class="btn btn-primary">
                                <i class="bi bi-box-arrow-right"></i> Solicitar Traslado
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card movement-card">
                    <div class="card-body">
                        <h3 class="card-title"><i class="bi bi-box-arrow-in-down"></i> Entrada de Productos</h3>
                        <p class="card-text">
                            Registre la entrada de nuevos productos a cualquier almacén del sistema.
                        </p>
                        <div class="card-actions">
                            <a href="<?= url('movimientos.create') ?>?tipo=ENTRADA" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Registrar Entrada
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card movement-card">
                    <div class="card-body">
                        <h3 class="card-title"><i class="bi bi-clock-history"></i> Historial de Movimientos</h3>
                        <p class="card-text">
                            Consulte el historial completo de movimientos con filtros avanzados.
                        </p>
                        <div class="card-actions">
                            <a href="<?= url('movimientos.list') ?>" class="btn btn-info">
                                <i class="bi bi-search"></i> Ver Historial
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card dashboard-card movement-card">
                    <div class="card-body">
                        <h3 class="card-title"><i class="bi bi-arrow-return-left"></i> Devolución de Botiquín</h3>
                        <p class="card-text">
                            Devuelva todos los productos de un botiquín a un almacén del sistema.
                        </p>
                        <div class="card-actions">
                            <a href="<?= url('movimientos.create') ?>?tipo=DEVOLUCION" class="btn btn-primary">
                                <i class="bi bi-box-arrow-left"></i> Registrar Devolución
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-header mt-5">
                <h2 class="section-title">Movimientos Pendientes</h2>
                <p class="section-description">
                    Listado de movimientos que requieren confirmación o pueden ser cancelados.
                </p>
            </div>

            <?php if (empty($pendientes)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--light-text);"></i>
                    <div>
                        <h3>No hay movimientos pendientes</h3>
                        <p>Actualmente no existen movimientos que requieran confirmación.</p>
                    </div>
                    <div class="action-buttons">
                        <a href="<?= url('movimientos.create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nuevo Movimiento
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
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pendientes as $movimiento): ?>
                                    <tr>
                                        <td>
                                            <span class="badge <?= $movimiento['tipo_movimiento'] === 'TRASLADO' ? 'bajo-stock' : 'stock-ok' ?>">
                                                <?= $movimiento['tipo_movimiento'] ?>
                                            </span>
                                        </td>
                                        <td><?= $movimiento['cantidad'] . ' ' . htmlspecialchars($movimiento['nombre_producto']) ?></td>
                                        <td><?= $movimiento['origen_nombre'] ? htmlspecialchars($movimiento['origen_nombre']) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($movimiento['destino_nombre']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])) ?></td>
                                        <td>
                                            <div class="acciones-stock">
                                                <button type="button"
                                                        class="btn btn-outline btn-icon btn-confirmar-mov"
                                                        data-id="<?= $movimiento['id_movimiento'] ?>"
                                                        title="Completar movimiento">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline btn-icon btn-cancelar-mov"
                                                        data-id="<?= $movimiento['id_movimiento'] ?>"
                                                        title="Cancelar movimiento">
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
                    <a href="<?= url('movimientos.list') ?>" class="btn btn-info">
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
        // Mostrar toast por parámetros GET
        mostrarToastPorGet();
        inicializarEventosDashboardMovimientos();
    });

    // --- Toast por GET ---
    function mostrarToastPorGet() {
        <?php if (isset($_GET['toast']) && isset($_GET['toastmsg'])): ?>
        const tipo = "<?= htmlspecialchars($_GET['toast']) ?>";
        const msg = "<?= htmlspecialchars($_GET['toastmsg']) ?>";
        
        switch (tipo) {
            case 'success':
                ToastSystem.success('Éxito', msg, null, {autoClose: true, closeDelay: 5000});
                break;
            case 'error':
                ToastSystem.danger('Error', msg, null, {autoClose: true, closeDelay: 5000});
                break;
            case 'info':
                ToastSystem.info('Información', msg, null, {autoClose: true, closeDelay: 5000});
                break;
            case 'warning':
                ToastSystem.warning('Advertencia', msg, null, {autoClose: true, closeDelay: 5000});
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

    // --- Inicialización de eventos ---
    function inicializarEventosDashboardMovimientos() {
        // Botones de confirmar
        document.querySelectorAll('.btn-confirmar-mov').forEach(function (btn) {
            btn.addEventListener('click', function () {
                mostrarToastConfirmacion('confirmar', this.dataset.id);
            });

        });
        // Botones de cancelar
        document.querySelectorAll('.btn-cancelar-mov').forEach(function (btn) {
            btn.addEventListener('click', function () {
                mostrarToastConfirmacion('cancelar', this.dataset.id);
            });

        });
        // Delegación para botones dentro del toast
        document.body.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-toast-accion')) {
                const action = e.target.dataset.action;
                const id = e.target.dataset.id;
                ejecutarAccionMovimiento(action, id);
            }
        });
    }

    // --- Toast de confirmación para acciones ---
    function mostrarToastConfirmacion(tipo, id) {
        if (tipo === 'confirmar') {
            ToastSystem.warning(
                'Confirmar movimiento',
                '¿Está seguro que desea confirmar este movimiento?' +
                'Se encuentran los productos en el almacén de destino?.',
                `<button class="btn btn-success btn-toast-accion" data-action="confirmar" data-id="${id}"><i class="bi bi-check-circle"></i> Confirmar</button>`,
                {autoClose: false}
            );
        } else if (tipo === 'cancelar') {
            ToastSystem.danger(
                'Cancelar movimiento',
                '¿Está seguro que desea cancelar este movimiento? Esta accion no se puede revertir.',
                `<button class="btn btn-danger btn-toast-accion" data-action="cancelar" data-id="${id}"><i class="bi bi-x-circle"></i> Cancelar</button>`,
                {autoClose: false}
            );
        }
    }

    // --- Ejecutar acción de movimiento ---
    function ejecutarAccionMovimiento(action, id) {
        const url = action === 'confirmar'
            ? '<?= url('movimientos.complete') ?>'
            : '<?= url('movimientos.cancel') ?>';
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
                    action === 'confirmar' ? 'Movimiento Confirmado' : 'Movimiento Cancelado',
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