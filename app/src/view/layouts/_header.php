<?php

use model\service\AuthService;

// Inicializar servicios
$authService = new AuthService();

// Inicializar variables
$title = $title ?? "Pegasus Medical";
$scripts = $scripts ?? null;
$navTitle = $navTitle ?? "Pegasus Medical";

// Verificar autenticación usando AuthService
$isAuthenticated = $authService->isAuthenticated();
$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['user_role'] ?? null;

// Definir constantes de roles para mejor legibilidad
const ROLE_ADMIN = 'ADMINISTRADOR';
const ROLE_GESTOR_GENERAL = 'GESTOR_GENERAL';
const ROLE_GESTOR_HOSPITAL = 'GESTOR_HOSPITAL';
const ROLE_GESTOR_PLANTA = 'GESTOR_PLANTA';
const ROLE_BOTIQUIN = 'USUARIO_BOTIQUIN';

// Verificar roles específicos
$isAdmin = $userRole === ROLE_ADMIN;
$isGestorGeneral = $userRole === ROLE_GESTOR_GENERAL;
$isGestorHospital = $userRole === ROLE_GESTOR_HOSPITAL;
$isGestorPlanta = $userRole === ROLE_GESTOR_PLANTA;
$isBotiquinUser = $userRole === ROLE_BOTIQUIN;

// Determinar los permisos de gestión
$canManageHospitals = in_array($userRole, [ROLE_ADMIN, ROLE_GESTOR_GENERAL, ROLE_GESTOR_HOSPITAL]);
$canManagePlantas = in_array($userRole, [ROLE_ADMIN, ROLE_GESTOR_GENERAL, ROLE_GESTOR_HOSPITAL, ROLE_GESTOR_PLANTA]);
$canManageUsers = in_array($userRole, [ROLE_ADMIN, ROLE_GESTOR_GENERAL]);

// Se eliminó la variable hideNav
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
    <!-- Imports de iconos y tal    -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--  Imports de mis archivos css y js   -->
    <link rel="stylesheet" href="/assets/css/styles.css">
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

<!-- Se eliminó la condición para ocultar la navegación -->
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
                    <?php if ($canManageHospitals || $canManagePlantas): ?>
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle">Infraestructura</a>
                            <ul class="dropdown-menu">
                                <?php if ($canManageHospitals): ?>
                                    <li><a href="<?= url('hospitals') ?>">Hospitales</a></li>
                                <?php endif; ?>

                                <?php if ($canManagePlantas): ?>
                                    <li><a href="<?= url('plantas') ?>">Plantas</a></li>
                                <?php endif; ?>

                                <?php if ($isAdmin || $isGestorGeneral): ?>
                                    <li><a href="<?= url('almacenes') ?>">Almacenes</a></li>
                                <?php endif; ?>

                                <?php if ($canManagePlantas): ?>
                                    <li><a href="<?= url('botiquines') ?>">Botiquines</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Categoría: Gestión de Inventario - Todos los usuarios autenticados -->
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">Inventario</a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('stocks') ?>">Stock</a></li>

                            <?php if ($canManagePlantas || $isBotiquinUser): ?>
                                <li><a href="<?= url('stocks.botiquines') ?>">Stock Botiquines</a></li>
                            <?php endif; ?>

                            <?php if ($isAdmin || $isGestorGeneral || $isGestorHospital): ?>
                                <li><a href="<?= url('stocks.almacenes') ?>">Stock Almacenes</a></li>
                            <?php endif; ?>

                            <?php if ($isAdmin || $isGestorGeneral): ?>
                                <li><a href="<?= url('productos') ?>">Productos</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- Categoría: Logística -->
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">Logística</a>
                        <ul class="dropdown-menu">
                            <?php if ($canManagePlantas || $isBotiquinUser): ?>
                                <li><a href="<?= url('reposiciones') ?>">Reposiciones</a></li>
                            <?php endif; ?>

                            <li><a href="<?= url('movimientos') ?>">Movimientos</a></li>

                            <?php if ($isAdmin || $isGestorGeneral || $isGestorHospital): ?>
                                <li><a href="<?= url('etiquetas') ?>">Etiquetas</a></li>
                            <?php endif; ?>

                            <li><a href="<?= url('lecturas') ?>">Lecturas</a></li>
                        </ul>
                    </li>

                    <?php if ($canManageUsers): ?>
                        <!-- Sección de gestión de Usuarios (solo para administradores) -->
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle">Usuarios</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= url('users') ?>">Dashboard</a></li>
                                <li><a href="<?= url('users.create') ?>">Crear Usuario</a></li>
                                <li><a href="<?= url('users.locations') ?>">Asignar Ubicaciones</a></li>
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
                            <?php if ($canManageUsers): ?>
                                <li><a href="<?= url('users') ?>">Gestionar usuarios</a></li>
                                <li class="dropdown-divider"></li>
                            <?php endif; ?>

                            <li><a href="<?= url('password.change') ?>"><i class="bi bi-key me-1"></i>Cambiar
                                    contraseña</a></li>
                            <li><a href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Cerrar
                                    sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="<?= url('login') ?>" class="nav-link"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar
                            sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="alerts-container">
    <?php include __DIR__ . '/_alerts.php'; ?>
</div>
