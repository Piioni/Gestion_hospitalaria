<?php

namespace model\service;

use model\entity\Movimiento;
use model\repository\MovimientoRepository;

class MovimientoService
{
    private MovimientoRepository $movimientoRepository;

    public function __construct()
    {
        $this->movimientoRepository = new MovimientoRepository();
    }

    public function getAllMovimientos(): array
    {
        return $this->movimientoRepository->findAll();
    }

    public function getMovimientoById(int $id): ?Movimiento
    {
        return $this->movimientoRepository->findById($id);
    }

    public function completarMovimiento(int $id_movimiento): bool
    {
        $movimiento = $this->movimientoRepository->findById($id_movimiento);
        if ($movimiento) {
            return $this->movimientoRepository->completar($id_movimiento);
        }
        return false;
    }

    public function cancelarMovimiento(int $id_movimiento): bool
    {
        $movimiento = $this->movimientoRepository->findById($id_movimiento);
        if ($movimiento) {
            return $this->movimientoRepository->cancelar($id_movimiento);
        }
        return false;
    }

    public function crearMovimiento($tipo_movimiento, $id_producto, $cantidad, $id_origen, $id_destino, $estado, $id_responsable): int
    {
        return $this->movimientoRepository->create($tipo_movimiento, $id_producto, $cantidad, $id_origen, $id_destino, $estado, $id_responsable);
    }

    public function getMovimientosPendientes(): array
    {
        return $this->movimientoRepository->findPendientes();
    }
    
    public function getMovimientosPendientesForUser(int $userId): array
    {
        return $this->movimientoRepository->findPendientesForUser($userId);
    }

    public function getMovimientosCompletados(): array
    {
        return $this->movimientoRepository->findCompletados();
    }

    public function getMovimientosCancelados(): array
    {
        return $this->movimientoRepository->findCancelados();
    }

    /**
     * Obtiene movimientos pendientes según los almacenes accesibles por el usuario
     * 
     * @param array $almacenIds IDs de almacenes a los que el usuario tiene acceso
     * @return array Movimientos pendientes filtrados
     */
    public function getMovimientosPendientesForAlmacenes(array $almacenIds): array
    {
        if (empty($almacenIds)) {
            return [];
        }
        
        return $this->movimientoRepository->findPendientesForUser($almacenIds);
    }

    /**
     * Extrae los IDs de una lista de objetos almacén
     * @param array $almacenes Lista de objetos almacén
     * @return array Lista de IDs de almacenes
     */
    public function extractAlmacenIds(array $almacenes): array
    {
        return array_map(function($almacen) {
            return $almacen->getId();
        }, $almacenes);
    }
}
