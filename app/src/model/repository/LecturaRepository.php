<?php

namespace model\repository;

use model\Database;
use model\entity\Lectura;
use PDO;

class LecturaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function createLecturaFromData(array $lecturaData): Lectura
    {
        return new Lectura(
            $lecturaData['id_lectura'],
            $lecturaData['id_botiquin'],
            $lecturaData['id_producto'],
            $lecturaData['cantidad'],
            $lecturaData['fecha_lectura'],
            $lecturaData['id_usuario']
        );
    }
    
    /**
     * Obtiene todas las lecturas con opción de filtrado
     * @param array $filters Filtros a aplicar (producto, botiquin, usuario)
     * @return array Lista de lecturas
     */
    public function getAll(array $filters = []): array
    {
        try {
            $whereClause = [];
            $params = [];

            if (isset($filters['id_producto'])) {
                $whereClause[] = 'id_producto = ?';
                $params[] = $filters['id_producto'];
            }
            if (isset($filters['id_botiquin'])) {
                $whereClause[] = 'id_botiquin = ?';
                $params[] = $filters['id_botiquin'];
            }
            if (isset($filters['id_usuario'])) {
                $whereClause[] = 'id_usuario = ?';
                $params[] = $filters['id_usuario'];
            }

            $whereSql = count($whereClause) > 0 ? 'WHERE ' . implode(' AND ', $whereClause) : '';
            $stmt = $this->pdo->prepare("
                SELECT *
                FROM lecturas
                $whereSql
                ORDER BY fecha_lectura DESC
            ");
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createLecturaFromData'], $result);


        } catch (\PDOException $e) {
            error_log("Error al obtener lecturas: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Crea una nueva lectura en la base de datos
     */
    public function create(int $id_botiquin, int $id_producto, int $cantidad, string $id_usuario): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO lecturas (id_botiquin, id_producto, cantidad, fecha_lectura, id_usuario)
                VALUES (?, ?, ?, NOW(), ?)
            ");
            
            return $stmt->execute([$id_botiquin, $id_producto, $cantidad, $id_usuario]);
        } catch (\PDOException $e) {
            error_log("Error al crear lectura: " . $e->getMessage());
            throw $e;
        }
    }


}
