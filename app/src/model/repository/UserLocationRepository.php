<?php

namespace model\repository;

use model\Database;
use PDO;
use PDOException;

class UserLocationRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }
    
    /**
     * Elimina todas las ubicaciones asignadas a un usuario
     */
    public function deleteAllUserLocations($userId): void
    {
        try {
            // Iniciamos una transacción para garantizar que todas las eliminaciones se ejecuten o ninguna
            $this->pdo->beginTransaction();
            
            // Eliminar asignaciones de hospitales
            $stmt = $this->pdo->prepare("DELETE FROM user_hospitales WHERE id_usuario = ?");
            $stmt->execute([$userId]);
            
            // Eliminar asignaciones de plantas
            $stmt = $this->pdo->prepare("DELETE FROM user_plantas WHERE id_usuario = ?");
            $stmt->execute([$userId]);
            
            // Eliminar asignaciones de botiquines
            $stmt = $this->pdo->prepare("DELETE FROM user_botiquines WHERE id_usuario = ?");
            $stmt->execute([$userId]);
            
            // Confirmamos la transacción
            $this->pdo->commit();
            
        } catch (PDOException $e) {
            // Si algo falla, revertimos la transacción
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error en deleteAllUserLocations: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Asigna un hospital a un usuario
     */
    public function addUserHospital($userId, $hospitalId): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO user_hospitales (id_usuario, id_hospital) VALUES (?, ?)");
            return $stmt->execute([$userId, $hospitalId]);
        } catch (PDOException $e) {
            error_log("Error en addUserHospital: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Asigna una planta a un usuario
     */
    public function addUserPlanta($userId, $plantaId): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO user_plantas (id_usuario, id_planta) VALUES (?, ?)");
            return $stmt->execute([$userId, $plantaId]);
        } catch (PDOException $e) {
            error_log("Error en addUserPlanta: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Asigna un botiquín a un usuario
     */
    public function addUserBotiquin($userId, $botiquinId): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO user_botiquines (id_usuario, id_botiquin) VALUES (?, ?)");
            $result = $stmt->execute([$userId, $botiquinId]);
            if (!$result) {
                error_log("Error al insertar botiquín: " . print_r($stmt->errorInfo(), true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error en addUserBotiquin: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtiene todos los hospitales asignados a un usuario
     */
    public function getUserHospitales($userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT h.id_hospital, h.nombre FROM hospitales h
                JOIN user_hospitales uh ON h.id_hospital = uh.id_hospital
                WHERE uh.id_usuario = ?
            ");
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getUserHospitales: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtiene todas las plantas asignadas a un usuario
     */
    public function getUserPlantas($userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.id_planta, p.nombre FROM plantas p
                JOIN user_plantas up ON p.id_planta = up.id_planta
                WHERE up.id_usuario = ?
            ");
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getUserPlantas: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtiene todos los botiquines asignados a un usuario
     */
    public function getUserBotiquines($userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT b.id_botiquin, b.nombre FROM botiquines b
                JOIN user_botiquines ub ON b.id_botiquin = ub.id_botiquin
                WHERE ub.id_usuario = ?
            ");
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getUserBotiquines: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verifica si el usuario tiene acceso a un hospital específico
     * 
     * @param int $userId ID del usuario
     * @param int $hospitalId ID del hospital
     * @return bool True si tiene acceso, false en caso contrario 
     */
    public function userHasAccessToHospital(int $userId, int $hospitalId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM user_hospitales 
                WHERE id_usuario = ? AND id_hospital = ?
            ");
            $stmt->execute([$userId, $hospitalId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en userHasAccessToHospital: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verifica si el usuario tiene acceso a una planta específica
     * 
     * @param int $userId ID del usuario
     * @param int $plantaId ID de la planta
     * @return bool True si tiene acceso, false en caso contrario
     */
    public function userHasAccessToPlanta(int $userId, int $plantaId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM user_plantas 
                WHERE id_usuario = ? AND id_planta = ?
            ");
            $stmt->execute([$userId, $plantaId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en userHasAccessToPlanta: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verifica si el usuario tiene acceso a un botiquín específico
     * 
     * @param int $userId ID del usuario
     * @param int $botiquinId ID del botiquín
     * @return bool True si tiene acceso, false en caso contrario
     */
    public function userHasAccessToBotiquin(int $userId, int $botiquinId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM user_botiquines 
                WHERE id_usuario = ? AND id_botiquin = ?
            ");
            $stmt->execute([$userId, $botiquinId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en userHasAccessToBotiquin: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Añade una ubicación a un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $locationId ID de la ubicación
     * @param string $locationType Tipo de ubicación ('hospital', 'planta', 'botiquin')
     * @return bool True si se añadió correctamente
     */
    public function addUserLocation($userId, $locationId, $locationType): bool
    {
        try {
            $tableName = $this->getTableNameByLocationType($locationType);
            $columnName = $this->getColumnNameByLocationType($locationType);
            
            $sql = "INSERT INTO $tableName (id_usuario, $columnName) VALUES (?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$userId, $locationId]);
        } catch (PDOException $e) {
            error_log("Error al añadir ubicación al usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una ubicación de un usuario
     * 
     * @param int $userId ID del usuario
     * @param int $locationId ID de la ubicación
     * @param string $locationType Tipo de ubicación ('hospital', 'planta', 'botiquin')
     * @return bool True si se eliminó correctamente
     */
    public function removeUserLocation($userId, $locationId, $locationType): bool
    {
        try {
            $tableName = $this->getTableNameByLocationType($locationType);
            $columnName = $this->getColumnNameByLocationType($locationType);
            
            $sql = "DELETE FROM $tableName WHERE id_usuario = ? AND $columnName = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$userId, $locationId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar ubicación del usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el nombre de la tabla según el tipo de ubicación
     * 
     * @param string $locationType Tipo de ubicación
     * @return string Nombre de la tabla
     */
    private function getTableNameByLocationType(string $locationType): string
    {
        switch (strtolower($locationType)) {
            case 'hospital':
                return 'user_hospitales';
            case 'planta':
                return 'user_plantas';
            case 'botiquin':
                return 'user_botiquines';
            default:
                throw new \InvalidArgumentException("Tipo de ubicación no válido: $locationType");
        }
    }
    
    /**
     * Obtiene el nombre de la columna según el tipo de ubicación
     * 
     * @param string $locationType Tipo de ubicación
     * @return string Nombre de la columna
     */
    private function getColumnNameByLocationType(string $locationType): string
    {
        switch (strtolower($locationType)) {
            case 'hospital':
                return 'id_hospital';
            case 'planta':
                return 'id_planta';
            case 'botiquin':
                return 'id_botiquin';
            default:
                throw new \InvalidArgumentException("Tipo de ubicación no válido: $locationType");
        }
    }
}
