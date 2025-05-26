<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar $title con un valor predeterminado
$title = $title ?? "Stock Hospitalario";
$scripts = $scripts ?? null;

// Variable para el título de la barra de navegación
$navTitle = $navTitle ?? "Pegasus Medical";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (isset($scripts)): ?>
        <script src="/assets/js/<?= htmlspecialchars($scripts, ENT_QUOTES) ?>"></script>
    <?php endif; ?>
    <script src="/assets/js/nav.js" defer></script>
</head>
<body>
<header class="header">
    <div class="container">
        <a href="/" class="logo">
            <span class="logo-icon">🏥</span>
            <span class="logo-text"><?= htmlspecialchars($navTitle) ?></span>
        </a>

        <button class="mobile-menu-toggle" aria-label="Abrir menú">
            <span class="hamburger"></span>
        </button>

        <nav class="main-nav">
            <ul class="nav-links">
                <!-- Categoría: Gestión de Infraestructura -->
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Infraestructura</a>
                    <ul class="dropdown-menu">
                        <li><a href="/ubicaciones/hospitals">Hospitales</a></li>
                        <li><a href="/ubicaciones/plantas">Plantas</a></li>
                        <li><a href="/ubicaciones/almacenes">Almacenes</a></li>
                        <li><a href="/ubicaciones/botiquines">Botiquines</a></li>
                    </ul>
                </li>

                <!-- Categoría: Gestión de Inventario -->
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Inventario</a>
                    <ul class="dropdown-menu">
                        <li><a href="/stocks">Stock</a></li>
                        <li><a href="/stocks/botiquines">Stock Botiquines</a></li>
                        <li><a href="/stocks/almacenes">Stock Almacenes</a></li>
                        <li><a href="/productos">Productos</a></li>
                    </ul>
                </li>

                <!-- Categoría: Logística -->
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Logística</a>
                    <ul class="dropdown-menu">
                        <li><a href="/reposiciones">Reposiciones</a></li>
                        <li><a href="/movimientos">Movimientos</a></li>
                        <li><a href="/etiquetas">Etiquetas</a></li>
                        <li><a href="/lecturas">Lecturas</a></li>
                    </ul>
                </li>
                
                <!-- Section de gestión de Usuarios -->
                <li class="dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Usuarios</a>
                    <ul class="dropdown-menu">
                        <li><a href="/users/dashboard">Dashboard</a></li>
                        <li><a href="/users/create">Crear Usuario</a></li>
                    </ul>
                </li>

                <!-- Sección de usuario -->
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="dropdown user-dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Usuario') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']): ?>
                                <li><a href="/users/list">Gestionar usuarios</a></li>
                                <li class="dropdown-divider"></li>
                                <li><a href="/users/edit?id=<?= $_SESSION['user']['id'] ?>">Mi perfil</a></li>
                            <?php endif; ?>
                            <li><a href="/logout">Cerrar sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/login" class="nav-link">Iniciar sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="alerts-container">
    <?php include __DIR__ . '/_alerts.php'; ?>
</div>
