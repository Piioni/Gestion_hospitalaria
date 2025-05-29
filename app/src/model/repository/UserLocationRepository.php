<?php

namespace model\repository;

use model\Database;
use PDO;
use PDOException;

class UserLocationRepository {
    private PDO $pdo;
    private HospitalRepository $hospitalRepository;
    private PlantaRepository $plantaRepository;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
        $this->hospitalRepository = new HospitalRepository();
        $this->plantaRepository = new PlantaRepository();
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
     * Obtiene los hospitales asociados a un usuario específico
     * @param $userId
     * @return array Lista de objetos Hospital
     */
    public function getHospitalsByUserId($userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT h.*
                FROM hospitales h
                JOIN user_hospitales uh ON h.id_hospital = uh.id_hospital
                WHERE uh.id_usuario = ? AND h.activo = 1
            ");
            $stmt->execute([$userId]);
            $hospitalesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this->hospitalRepository, 'createHospitalFromData'], $hospitalesData);

        } catch (PDOException $e) {
            error_log("Error en getHospitalsByUserId: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene todas las plantas asignadas a un usuario
     * @param $userId
     * @return array Lista de objetos Planta
     */
    public function getPlantasByUserId($userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*
                FROM plantas p
                JOIN user_plantas up ON p.id_planta = up.id_planta
                WHERE up.id_usuario = ? AND p.activo = 1
            ");
            $stmt->execute([$userId]);
            $plantasData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this->plantaRepository, 'createPlantaFromData'], $plantasData);

        } catch (PDOException $e) {
            error_log("Error en getPlantasByUserId: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtiene todos los botiquines asignados a un usuario
     */
    public function getBotiquinesByUserId($userId): array
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

}
