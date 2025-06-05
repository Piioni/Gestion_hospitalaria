<?php

namespace model\repository;

use model\Database;
use model\entity\Movimiento;
use PDO;

class MovimientoRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPDO();
    }

    public function createMovimientoFromData($data): Movimiento
    {
        return new Movimiento(
            $data['id_movimiento'],
            $data['tipo_movimiento'],
            $data['id_producto'],
            $data['cantidad'],
            $data['id_origen'] ?? null,
            $data['id_destino'],
            $data['estado'],
            $data['id_responsable']
        );
    }

    public function findById(int $id): ?Movimiento
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM movimientos WHERE id_movimiento = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? $this->createMovimientoFromData($data) : null;
        } catch (\PDOException $e) {
            error_log("Error al buscar movimiento por ID: " . $e->getMessage());
            return null;
        }
    }

    public function create($tipo_movimiento, $id_producto, $cantidad, $id_origen, $id_destino, $estado, $id_responsable): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO movimientos (
                         tipo_movimiento,
                         id_producto,
                         cantidad,
                         id_origen,
                         id_destino,
                         estado,
                         id_responsable) VALUES (?, ?, ?, ?, ?, ?, ?)");

            return $stmt->execute([
                $tipo_movimiento,
                $id_producto,
                $cantidad,
                $id_origen,
                $id_destino,
                $estado,
                $id_responsable
            ]);

        } catch (\PDOException $e) {
            error_log("Error al crear movimiento: " . $e->getMessage());
            return false;
        }
    }

    public function cancelar(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE movimientos SET estado = 'CANCELADO' WHERE id_movimiento = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al cancelar movimiento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Completa un movimiento y añade el stock al almacén destino en una transacción.
     * @param int $idMovimiento
     * @return bool
     */
    public function completar(int $idMovimiento): bool
    {
        // TODO: Si es un traslado debería verificar y eliminar el stock del almacén origen
        try {
            $this->pdo->beginTransaction();

            // Obtener datos del movimiento
            $stmt = $this->pdo->prepare("SELECT * FROM movimientos WHERE id_movimiento = ?");
            $stmt->execute([$idMovimiento]);
            $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movimiento) {
                $this->pdo->rollBack();
                return false;
            }

            // Insertar stock en el almacén destino
            $stmtStock = $this->pdo->prepare("
                INSERT INTO stocks (id_producto, tipo_ubicacion, id_ubicacion, cantidad)
                VALUES (?, 'ALMACEN', ?, ?)
                ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)
            ");
            $okStock = $stmtStock->execute([
                $movimiento['id_producto'],
                $movimiento['id_destino'],
                $movimiento['cantidad']
            ]);

            if (!$okStock) {
                $this->pdo->rollBack();
                return false;
            }

            // Completar el movimiento
            $stmtMov = $this->pdo->prepare("UPDATE movimientos SET estado = 'COMPLETADO' WHERE id_movimiento = ?");
            $okMov = $stmtMov->execute([$idMovimiento]);

            if (!$okMov) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            error_log("Error en completarYAgregarStock: " . $e->getMessage());
            $this->pdo->rollBack();
            return false;
        }
    }

    public function find($filtros = null, $almacenIds = null): array
    {
        try{
            $whereClauses = [];
            $params = [];

            if (!empty($filtros['tipo_movimiento'])) {
                $whereClauses[] = 'm.tipo_movimiento = ?';
                $params[] = $filtros['tipo_movimiento'];
            }

            if (!empty($filtros['producto'])) {
                $whereClauses[] = 'm.id_producto = ?';
                $params[] = $filtros['producto'];
            }

            if (!empty($filtros['estado'])) {
                $whereClauses[] = 'm.estado = ?';
                $params[] = $filtros['estado'];
            }
            if (isset($filtros['orden'])) {
                $orden = $filtros['orden'];
                if ($orden === 'fecha_desc') {
                    $orderBy = 'm.fecha_movimiento DESC';
                } elseif ($orden === 'fecha_asc') {
                    $orderBy = 'm.fecha_movimiento ASC';
                } else {
                    $orderBy = 'm.fecha_movimiento DESC'; // Valor por defecto
                }
            } else {
                $orderBy = 'm.fecha_movimiento DESC'; // Valor por defecto

            }

            // Filtrar por almacenes relacionados con el usuario
            if (!empty($almacenIds) && is_array($almacenIds)) {
                $placeholders = implode(',', array_fill(0, count($almacenIds), '?'));
                $whereClauses[] = "(m.id_origen IN ($placeholders) OR m.id_destino IN ($placeholders))";
                $params = array_merge($params, $almacenIds, $almacenIds);
            }

            $whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
            $sql = "SELECT 
                    m.id_movimiento,   
                    m.tipo_movimiento, 
                    p.nombre as nombre_producto,
                    m.cantidad,
                    o.nombre as origen_nombre,
                    d.nombre as destino_nombre,
                    m.fecha_movimiento,
                    m.estado,
                    m.id_responsable
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN almacenes o ON m.id_origen = o.id_almacen
                JOIN almacenes d ON m.id_destino = d.id_almacen
                $whereSql
                ORDER BY $orderBy";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al buscar movimientos por filtros: " . $e->getMessage());
            return [];
        }

    }

}