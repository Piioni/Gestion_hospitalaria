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
                <li class="dropdown mega-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Infraestructura</a>
                    <div class="mega-dropdown-content">
                        <div class="mega-dropdown-section">
                            <h3>Hospitales</h3>
                            <ul>
                                <li><a href="/hospitals">Dashboard</a></li>
                            </ul>
                        </div>

                        <div class="mega-dropdown-section">
                            <h3>Plantas</h3>
                            <ul>
                                <li><a href="/plantas">Dashboard</a></li>
                            </ul>
                        </div>

                        <div class="mega-dropdown-section">
                            <h3>Botiquines</h3>
                            <ul>
                                <li><a href="/botiquines">Dashboard</a></li>
                                <li><a href="/botiquines/list">Listar todos</a></li>
                                <li><a href="/botiquines/create">Crear nuevo</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Categoría: Gestión de Inventario -->
                <li class="dropdown mega-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Inventario</a>
                    <div class="mega-dropdown-content">
                        <div class="mega-dropdown-section">
                            <h3>Almacenes</h3>
                            <ul>
                                <li><a href="/almacenes">Dashboard</a></li>
                                <li><a href="/almacenes/list">Listar todos</a></li>
                                <li><a href="/almacenes/create">Crear nuevo</a></li>
                            </ul>
                        </div>

                        <div class="mega-dropdown-section">
                            <h3>Productos</h3>
                            <ul>
                                <li><a href="/productos">Dashboard</a></li>
                                <li><a href="/productos/list">Listar todos</a></li>
                                <li><a href="/productos/create">Crear nuevo</a></li>
                            </ul>
                        </div>

                        <div class="mega-dropdown-section">
                            <h3>Pactos</h3>
                            <ul>
                                <li><a href="/pactos">Dashboard</a></li>
                                <li><a href="/pactos/list">Listar todos</a></li>
                                <li><a href="/pactos/create">Crear nuevo</a></li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Categoría: Logística -->
                <li class="dropdown mega-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Logística</a>
                    <div class="mega-dropdown-content">
                        <div class="mega-dropdown-section">
                            <h3>Reposiciones</h3>
                            <ul>
                                <li><a href="/reposiciones">Dashboard</a></li>
                                <li><a href="/reposiciones/list">Listar todas</a></li>
                                <li><a href="/reposiciones/create">Crear nueva</a></li>
                            </ul>
                        </div>

                        <div class="mega-dropdown-section">
                            <h3>Etiquetas</h3>
                            <ul>
                                <li><a href="/etiquetas">Dashboard</a></li>
                                <li><a href="/etiquetas/list">Listar todas</a></li>
                                <li><a href="/etiquetas/create">Crear nueva</a></li>
                            </ul>
                        </div>

                        <div class="mega-dropdown-section">
                            <h3>Lecturas</h3>
                            <ul>
                                <li><a href="/lecturas">Dashboard</a></li>
                                <li><a href="/lecturas/list">Listar todas</a></li>
                                <li><a href="/lecturas/create">Crear nueva</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
                <!-- Section de gestion de Usuarios -->
                <li class="dropdown mega-dropdown">
                    <a href="#" class="nav-link dropdown-toggle">Usuarios</a>
                    <div class="mega-dropdown-content">
                        <div class="mega-dropdown-section">
                            <h3>Gestión de Usuarios</h3>
                            <ul>
                                <li><a href="/users/dashboard">Dashboard</a></li>
                                <li><a href="/users/create">Crear nuevo</a></li>
                            </ul>
                        </div>
                    </div>
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
