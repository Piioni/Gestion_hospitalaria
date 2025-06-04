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

    /**
     * Busca movimientos según filtros y almacenes
     * @param array|null $filtros
     * @param array|null $almacenIds
     * @return array
     */
    public function find(array $filtros = null, array $almacenIds = null): array
    {
        return $this->movimientoRepository->find($filtros, $almacenIds);
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