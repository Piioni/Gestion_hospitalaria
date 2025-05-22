<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar $title con un valor predeterminado
$title = $title ?? "Stock Hospitalario";
$scripts = $scripts ?? "main.js";

// Variable para el título de la barra de navegación
$navTitle = "Stock Hospitalario";

// Sí estamos en el panel de administración, cambiar el título
//if (str_contains($_SERVER['REQUEST_URI'], 'admin_dashboard') || isset($isAdminPage)) {
//    $navTitle = "Panel de Administración";
//}
//
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <script src="/assets/js/<?= htmlspecialchars($scripts, ENT_QUOTES) ?>"></script>
</head>
<body>
<header class="header">
    <div class="container">
        <a href="/" class="logo">
            <span class="logo-icon">🏥</span>
            <span class="logo-text"><?= htmlspecialchars($navTitle) ?></span>
        </a>
        <nav class="main-nav">
            <ul class="nav-links">
                <li><a href="/" class="nav-link">Inicio</a></li>
                <li><a href="/hospitals/list" class="nav-link">Hospitales</a></li>
                <li><a href="/plants/list" class="nav-link">Plantas</a></li>
                <li><a href="/stock/list" class="nav-link">Stock</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="alerts-container">
    <?php include __DIR__ . '/_alerts.php'; ?>
</div>
