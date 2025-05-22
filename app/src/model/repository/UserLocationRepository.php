<?php

namespace model\repository;

use model\database\Database;
use PDO;

class UserLocationRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Elimina todas las ubicaciones asignadas a un usuario
     */
    public function deleteAllUserLocations($userId) {
        // Eliminar asignaciones de hospitales
        $stmt = $this->db->prepare("DELETE FROM user_hospitales WHERE user_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Eliminar asignaciones de plantas
        $stmt = $this->db->prepare("DELETE FROM user_plantas WHERE user_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Eliminar asignaciones de botiquines
        $stmt = $this->db->prepare("DELETE FROM user_botiquines WHERE user_id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    /**
     * Asigna un hospital a un usuario
     */
    public function addUserHospital($userId, $hospitalId) {
        $stmt = $this->db->prepare("INSERT INTO user_hospitales (user_id, hospital_id) VALUES (:userId, :hospitalId)");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':hospitalId', $hospitalId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Asigna una planta a un usuario
     */
    public function addUserPlanta($userId, $plantaId) {
        $stmt = $this->db->prepare("INSERT INTO user_plantas (user_id, planta_id) VALUES (:userId, :plantaId)");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':plantaId', $plantaId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Asigna un botiquín a un usuario
     */
    public function addUserBotiquin($userId, $botiquinId) {
        $stmt = $this->db->prepare("INSERT INTO user_botiquines (user_id, botiquin_id) VALUES (:userId, :botiquinId)");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':botiquinId', $botiquinId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Obtiene todos los hospitales asignados a un usuario
     */
    public function getUserHospitales($userId) {
        $stmt = $this->db->prepare("
            SELECT h.* FROM hospitales h
            JOIN user_hospitales uh ON h.id = uh.hospital_id
            WHERE uh.user_id = :userId
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene todas las plantas asignadas a un usuario
     */
    public function getUserPlantas($userId) {
        $stmt = $this->db->prepare("
            SELECT p.* FROM plantas p
            JOIN user_plantas up ON p.id = up.planta_id
            WHERE up.user_id = :userId
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene todos los botiquines asignados a un usuario
     */
    public function getUserBotiquines($userId) {
        $stmt = $this->db->prepare("
            SELECT b.* FROM botiquines b
            JOIN user_botiquines ub ON b.id = ub.botiquin_id
            WHERE ub.user_id = :userId
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
