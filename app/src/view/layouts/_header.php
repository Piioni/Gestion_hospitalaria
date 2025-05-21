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
    <script src="/assets/js/<?= htmlspecialchars($scripts, ENT_QUOTES) ?>"></script>
</head>
<body>
<header>
</header>
</body>

<?php include __DIR__ . '/../partials/_alerts.php'; ?>
