<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar $title con un valor predeterminado
$title = $title ?? "Stock Hospitalario";
$scripts = $scripts ?? null;

// Variable para el título de la barra de navegación
$navTitle = $navTitle ?? "Pegasus Medical";

// Verificar si el usuario está autenticado
$isAuthenticated = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['user_role'] ?? null;

// Verificar si es administrador (rol 1)
$isAdmin = $userRole == 1;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/assets/js/nav.js" defer></script>
    <?php if (isset($scripts)): ?>
        <?php
        // Si $scripts es un string, convertirlo a array para procesarlo de forma uniforme
        $scriptsArray = is_array($scripts) ? $scripts : [$scripts];
        foreach ($scriptsArray as $script):
            ?>
            <script src="/assets/js/<?= htmlspecialchars($script, ENT_QUOTES) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<header class="header">
    <div class="container">
        <a href="<?= url('home') ?>" class="logo">
            <span class="logo-icon">
                <img src="/assets/img/pegasus_medical.png" alt="Logo Pegasus Medical" class="logo-image">
            </span>
            <span class="logo-text"><?= htmlspecialchars($navTitle) ?></span>
        </a>

        <button class="mobile-menu-toggle" aria-label="Abrir menú">
            <span class="hamburger"></span>
        </button>

        <nav class="main-nav">
            <ul class="nav-links">
                <?php if ($isAuthenticated): ?>
                    <!-- Categoría: Gestión de Infraestructura -->
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">Infraestructura</a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('hospitals') ?>">Hospitales</a></li>
                            <li><a href="<?= url('plantas') ?>">Plantas</a></li>
                            <li><a href="<?= url('almacenes') ?>">Almacenes</a></li>
                            <li><a href="<?= url('botiquines') ?>">Botiquines</a></li>
                        </ul>
                    </li>

                    <!-- Categoría: Gestión de Inventario -->
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">Inventario</a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('stocks') ?>">Stock</a></li>
                            <li><a href="<?= url('stocks.botiquines') ?>">Stock Botiquines</a></li>
                            <li><a href="<?= url('stocks.almacenes') ?>">Stock Almacenes</a></li>
                            <li><a href="<?= url('productos') ?>">Productos</a></li>
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

                    <?php if ($isAdmin): ?>
                        <!-- Section de gestión de Usuarios (solo para administradores) -->
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle">Usuarios</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= url('users') ?>">Dashboard</a></li>
                                <li><a href="<?= url('users.create') ?>">Crear Usuario</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Sección de usuario/autenticación -->
                <?php if ($isAuthenticated): ?>
                    <li class="dropdown user-dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($userName) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($isAdmin): ?>
                                <li><a href="<?= url('users') ?>">Gestionar usuarios</a></li>
                                <li class="dropdown-divider"></li>
                            <?php endif; ?>
                            <!-- TODO: Implementar logica para modificar perfil propio. -->
                            <!--                            <li><a href="-->
                            <?php //= url('profile') ?><!--">Mi perfil</a></li>-->
                            <li><a href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Cerrar
                                    sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="<?= url('login') ?>" class="nav-link"><i class="bi bi-box-arrow-in-right me-1">
                            </i>Iniciar sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="alerts-container">
    <?php include __DIR__ . '/_alerts.php'; ?>
</div>
