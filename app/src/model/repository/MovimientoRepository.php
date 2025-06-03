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

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM movimientos");
            $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createMovimientoFromData'], $movimientos);
        } catch (\PDOException $e) {
            // Manejo de errores, por ejemplo, registrar el error
            error_log("Error al obtener todos los movimientos: " . $e->getMessage());
            return [];
        }
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

    public function completar(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE movimientos SET estado = 'COMPLETADO' WHERE id_movimiento = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error al completar movimiento: " . $e->getMessage());
            return false;
        }
    }

    public function findPendientes(): array
    {
        try {
            $sql = "SELECT m.*, 
                    p.nombre as nombre_producto,
                    o.nombre as origen_nombre,
                    d.nombre as destino_nombre
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN almacenes o ON m.id_origen = o.id_almacen
                JOIN almacenes d ON m.id_destino = d.id_almacen
                WHERE m.estado = 'PENDIENTE'";
                
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener movimientos pendientes: " . $e->getMessage());
            return [];
        }
    }

    public function findPendientesForUser(array $almacenIds): array
    {
        if (empty($almacenIds)) {
            return [];
        }

        try {
            // Convertir array a cadena de placeholders para SQL
            $placeholders = implode(',', array_fill(0, count($almacenIds), '?'));
            
            $sql = "SELECT m.*, 
                    p.nombre as nombre_producto,
                    o.nombre as origen_nombre,
                    d.nombre as destino_nombre
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                LEFT JOIN almacenes o ON m.id_origen = o.id_almacen
                JOIN almacenes d ON m.id_destino = d.id_almacen
                WHERE m.estado = 'PENDIENTE' 
                AND (m.id_origen IN ($placeholders) OR m.id_destino IN ($placeholders))";
            
            $stmt = $this->pdo->prepare($sql);
            
            // Unir los dos arrays de parámetros
            $params = array_merge($almacenIds, $almacenIds);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error al obtener movimientos pendientes para usuario: " . $e->getMessage());
            return [];
        }
    }

    public function findCompletados(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM movimientos WHERE estado = 'COMPLETADO'");
            $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createMovimientoFromData'], $movimientos);
        } catch (\PDOException $e) {
            error_log("Error al obtener movimientos completados: " . $e->getMessage());
            return [];
        }
    }

    public function findCancelados(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM movimientos WHERE estado = 'CANCELADO'");
            $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'createMovimientoFromData'], $movimientos);
        } catch (\PDOException $e) {
            error_log("Error al obtener movimientos cancelados: " . $e->getMessage());
            return [];
        }

    }

}
