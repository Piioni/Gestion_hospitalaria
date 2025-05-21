<?php
$pageTitle = "Error 404 - Página no encontrada";
include __DIR__ . '/../layouts/_header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1>¡Oops!</h1>
                <h2>Error 404 - Página no encontrada</h2>
                <div class="error-details my-4">
                    Lo sentimos, la página que estás buscando no existe o no está disponible.
                </div>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary btn-lg">
                        <i class="fas fa-home"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/_footer.php'; ?>
