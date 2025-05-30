<?php
include __DIR__ . "/../../layouts/_header.php";
?>

<div class="page-section">
    <div class="container">
        <div class="overview-section">
            <h1 class="page-title">Dashboard de Stock</h1>
            <p class="lead-text">
                Gestión y visualización del inventario en almacenes y botiquines.
            </p>
        </div>

        <div class="cards-container">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-building"></i> Stock en Almacenes</h3>
                    <p class="card-text">
                        Visualice y gestione el stock disponible en los almacenes del sistema.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('stocks.almacenes') ?>" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> Ver Stock de Almacenes
                        </a>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-briefcase-medical"></i> Stock en Botiquines</h3>
                    <p class="card-text">
                        Visualice y gestione el stock disponible en los botiquines de las plantas.
                    </p>
                    <div class="card-actions">
                        <a href="<?= url('stocks.botiquines') ?>" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> Ver Stock de Botiquines
                        </a>
                    </div>
                </div>
            </div>

            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="bi bi-exclamation-triangle"></i> Alertas de Stock</h3>
                    <p class="card-text">
                        Visualice productos con niveles de stock por debajo del mínimo establecido.
                    </p>
                    <div class="card-actions">
                        <a href="#" class="btn btn-warning">
                            <i class="bi bi-bell"></i> Ver Alertas de Stock
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="actions-section">
            <h2 class="section-title">Acciones Rápidas</h2>
            <div class="action-buttons">
                <a href="#" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Añadir Stock
                </a>
                <a href="#" class="btn btn-info">
                    <i class="bi bi-arrow-left-right"></i> Realizar Movimiento
                </a>
                <a href="#" class="btn btn-secondary">
                    <i class="bi bi-printer"></i> Imprimir Informe
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../layouts/_footer.php"; ?>
