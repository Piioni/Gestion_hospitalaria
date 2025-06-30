<?php

use model\service\AuthService;

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new AuthService();
$auth->logout();
// La redirección ya está incluida en el méto do logout del AuthService
