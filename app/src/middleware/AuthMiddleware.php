<?php

namespace middleware;

use model\Database;
use PDO;

class AuthMiddleware
{
    private static $session_started = false;

    public static function requireAuth(): void
    {
        self::ensureSession();

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

        // Administradores y gestores generales pueden acceder a todo
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return;
        }

        // Gestores de hospital solo pueden acceder a su hospital
        if ($userRole === 'GESTOR_HOSPITAL') {
            $userHospitals = self::getUserHospitals($userId);
            if (!in_array($hospitalId, $userHospitals)) {
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

        // Administradores y gestores generales pueden acceder a todo
        if (in_array($userRole, ['ADMINISTRADOR', 'GESTOR_GENERAL'])) {
            return;
        }

        // Gestores de hospital pueden acceder a plantas de sus hospitales
        if ($userRole === 'GESTOR_HOSPITAL') {
            $plantaHospital = self::getPlantaHospital($plantaId);
            $userHospitals = self::getUserHospitals($userId);

            if (!in_array($plantaHospital, $userHospitals)) {
                http_response_code(403);
                header('Location: /403');
                exit;
            }
        }

        // Gestores de planta solo pueden acceder a sus plantas
        if ($userRole === 'GESTOR_PLANTA') {
            $userPlantas = self::getUserPlantas($userId);
            if (!in_array($plantaId, $userPlantas)) {
                http_response_code(403);
                header('Location: /403');
                exit;
            }
        }
    }

    private static function ensureSession(): void
    {
        if (!self::$session_started) {
            session_start();
            self::$session_started = true;
        }
    }

    private static function getUserHospitals(int $userId): array
    {
        // Implementar consulta a user_hospital
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT id_hospital FROM user_hospital WHERE id_usuario = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private static function getUserPlantas(int $userId): array
    {
        // Implementar consulta a user_planta
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT id_planta FROM user_planta WHERE id_usuario = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private static function getPlantaHospital(int $plantaId): int
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT id_hospital FROM plantas WHERE id_planta = ?");
        $stmt->execute([$plantaId]);
        return $stmt->fetchColumn();
    }

}