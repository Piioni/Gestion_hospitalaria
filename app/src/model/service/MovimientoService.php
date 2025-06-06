<?php

namespace model\service;

use model\entity\Movimiento;
use model\repository\MovimientoRepository;

class MovimientoService
{
    private MovimientoRepository $movimientoRepository;
    private StockAlmacenService $stockAlmacenService;

    public function __construct()
    {
        $this->movimientoRepository = new MovimientoRepository();
        $this->stockAlmacenService = new StockAlmacenService();
    }

    public function crearMovimiento($tipo_movimiento, $id_producto, $cantidad, $id_origen, $id_destino, $estado, $id_responsable): int
    {
        return $this->movimientoRepository->create($tipo_movimiento, $id_producto, $cantidad, $id_origen, $id_destino, $estado, $id_responsable);
    }

    public function getMovimientoById(int $id): ?Movimiento
    {
        return $this->movimientoRepository->findById($id);
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
     * Completa un movimiento y actualiza el stock en una transacción
     * @param int $id_movimiento
     * @return bool
     */
    public function completarMovimiento(int $id_movimiento): bool
    {
        // Obtener el movimiento
        $movimiento = $this->movimientoRepository->findById($id_movimiento);
        if (!$movimiento || $movimiento->getEstado() !== 'PENDIENTE') {
            return false;
        }

        try {
            // Iniciar transacción
            $this->movimientoRepository->beginTransaction();

            $idDestino = $movimiento->getIdDestino();
            $idProducto = $movimiento->getIdProducto();
            $cantidad = $movimiento->getCantidad();

            // Si es traslado, verificar y reducir stock del origen
            if ($movimiento->getTipoMovimiento() === 'TRASLADO' && $movimiento->getIdOrigen()) {
                $idOrigen = $movimiento->getIdOrigen();

                // Verificar stock suficiente
                if (!$this->stockAlmacenService->verificarStockSuficiente($idOrigen, $idProducto, $cantidad)) {
                    $this->movimientoRepository->rollBack();
                    return false;
                }

                // Reducir stock del origen
                if (!$this->stockAlmacenService->reducirStock($idOrigen, $idProducto, $cantidad)) {
                    $this->movimientoRepository->rollBack();
                    return false;
                }
            }

            // Incrementar stock en el destino
            if (!$this->stockAlmacenService->incrementarStock($idDestino, $idProducto, $cantidad)) {
                $this->movimientoRepository->rollBack();
                return false;
            }

            // Actualizar el estado del movimiento
            if (!$this->movimientoRepository->completarMovimiento($id_movimiento)) {
                $this->movimientoRepository->rollBack();
                return false;
            }

            // Confirmar transacción
            $this->movimientoRepository->commit();
            return true;

        } catch (\Exception $e) {
            $this->movimientoRepository->rollBack();
            error_log("Error al completar movimiento: " . $e->getMessage());
            return false;
        }
    }

    public function cancelarMovimiento(int $id_movimiento): bool
    {
        $movimiento = $this->movimientoRepository->findById($id_movimiento);
        if ($movimiento) {
            return $this->movimientoRepository->cancelar($id_movimiento);
        }
        return false;
    }

    /**
     * Extrae los IDs de una lista de objetos almacén
     * @param array $almacenes Lista de objetos almacén
     * @return array Lista de IDs de almacenes
     */
    public function extractAlmacenIds(array $almacenes): array
    {
        return array_map(function ($almacen) {
            return $almacen->getId();
        }, $almacenes);
    }
}