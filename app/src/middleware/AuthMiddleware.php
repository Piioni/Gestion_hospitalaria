<?php

namespace middleware;

use model\service\UserLocationService;
use model\service\PlantaService;

class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public static function requireRole(array $allowedRoles): void
    {
        self::requireAuth();

        $userRole = $_SESSION['user_role'] ?? null;

        if (!in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            header('Location: /403');
            exit;
        }
    }

    public static function requireHospitalAccess(int $hospitalId): void
    {
        self::requireAuth();

        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        // Administradores y gestores generales pueden acceder a to do
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return;
        }

        // Gestores de hospital solo pueden acceder a su hospital
        if ($userRole === 'GESTOR_HOSPITAL') {
            $userLocationService = new UserLocationService();
            if (!$userLocationService->userHasHospitalAccess($userId, $hospitalId)) {
                http_response_code(403);
                header('Location: /403');
                exit;
            }
        } else {
            // Otros roles no pueden acceder a gestión de hospitales
            http_response_code(403);
            header('Location: /403');
            exit;
        }
    }

    public static function requirePlantaAccess(int $plantaId): void
    {
        self::requireAuth();

        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        // Administradores y gestores generales pueden acceder a to do
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return;
        }

        $userLocationService = new UserLocationService();
        $plantaService = new PlantaService();

        // Gestores de hospital pueden acceder a plantas de sus hospitales
        if ($userRole === 'GESTOR_HOSPITAL') {
            $planta = $plantaService->getPlantaById($plantaId);

            if (!$planta) {
                http_response_code(404);
                header('Location: /404');
                exit;
            }

            if (!$userLocationService->userHasHospitalAccess($userId, $planta->getIdHospital())) {
                http_response_code(403);
                header('Location: /403');
                exit;
            }
        } // Gestores de planta solo pueden acceder a sus plantas
        else if ($userRole === 'GESTOR_PLANTA') {
            if (!$userLocationService->userHasPlantaAccess($userId, $plantaId)) {
                http_response_code(403);
                header('Location: /403');
                exit;
            }
        } else {
            // Otros roles no tienen acceso
            http_response_code(403);
            header('Location: /403');
            exit;
        }
    }
}
