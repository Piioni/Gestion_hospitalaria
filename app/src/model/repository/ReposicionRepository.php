<?php

namespace model\repository;

use model\Database;
use model\entity\Reposicion;
use PDO;

class ReposicionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function create($id_producto, $cantidad, $id_almacen, $id_botiquin, $estado, $id_responsable): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO reposiciones (
                id_producto, cantidad, id_almacen, id_botiquin, estado, id_responsable
            ) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $id_producto, $cantidad, $id_almacen, $id_botiquin, $estado, $id_responsable
            ]);
        } catch (\PDOException $e) {
            error_log("Error al crear reposición: " . $e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?Reposicion
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE id_reposicion = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) return null;
            return new Reposicion(
                $data['id_reposicion'],
                $data['id_producto'],
                $data['cantidad'],
                $data['id_almacen'],
                $data['id_botiquin'],
                $data['estado'],
                $data['id_responsable'],
                $data['fecha_reposicion'] ?? null
            );
        } catch (\PDOException $e) {
            error_log("Error al buscar reposición: " . $e->getMessage());
            return null;
        }
    }

    public function find($filtros = []): array
    {
        $where = [];
        $params = [];

        if (isset($filtros['estado'])) {
            $where[] = "estado = ?";
            $params[] = $filtros['estado'];
        }

        if (isset($filtros['id_botiquin'])) {
            if (is_array($filtros['id_botiquin'])) {
                $placeholders = implode(',', array_fill(0, count($filtros['id_botiquin']), '?'));
                $where[] = "id_botiquin IN ($placeholders)";
                $params = array_merge($params, $filtros['id_botiquin']);
            } else {
                $where[] = "id_botiquin = ?";
                $params[] = $filtros['id_botiquin'];
            }
        }

        $sql = "SELECT r.*, p.nombre as nombre_producto, a.nombre as nombre_almacen, b.nombre as nombre_botiquin
                FROM reposiciones r
                JOIN productos p ON r.id_producto = p.id_producto
                JOIN almacenes a ON r.id_almacen = a.id_almacen
                JOIN botiquines b ON r.id_botiquin = b.id_botiquin";
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY r.fecha_reposicion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function completar(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Obtener datos de la reposición
            $stmt = $this->pdo->prepare("SELECT * FROM reposiciones WHERE id_reposicion = ?");
            $stmt->execute([$id]);
            $repo = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$repo) {
                $this->pdo->rollBack();
                return false;
            }

            // Insertar stock en botiquín destino
            $stmtStock = $this->pdo->prepare("
                INSERT INTO stocks (id_producto, tipo_ubicacion, id_ubicacion, cantidad)
                VALUES (?, 'BOTIQUIN', ?, ?)
                ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)
            ");
            $okStock = $stmtStock->execute([
                $repo['id_producto'],
                $repo['id_botiquin'],
                $repo['cantidad']
            ]);
            if (!$okStock) {
                $this->pdo->rollBack();
                return false;
            }

            // Completar la reposición
            $stmtRepo = $this->pdo->prepare("UPDATE reposiciones SET estado = 'COMPLETADO' WHERE id_reposicion = ?");
            $okRepo = $stmtRepo->execute([$id]);
            if (!$okRepo) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            error_log("Error al completar reposición: " . $e->getMessage());
            $this->pdo->rollBack();
            return false;
        }
    }

    public function cancelar(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE reposiciones SET estado = 'CANCELADO' WHERE id_reposicion = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al cancelar reposición: " . $e->getMessage());
            return false;
        }
    }
}