<?php

namespace model\entity;

// Representa movimientos de productos entre almacenes o entradas de productos
class Movimiento
{
    private int $id_movimiento;
    private string $tipo_movimiento; // "TRASLADO" , "ENTRADA" , "DEVOLUCION"
    private int $id_producto;
    private int $cantidad;
    private ?int $id_origen; // Él, id de origen puede ser nulo si es una entrada
    private ?int $id_botiquin_origen = null; // Si es un traslado entre botiquines
    private int $id_destino;
    private string $fecha_movimiento;
    private string $estado; // "PENDIENTE", "COMPLETADO", "CANCELADO"
    private string $id_responsable;

    /**
     * @param int $id_movimiento
     * @param string $tipo_movimiento
     * @param int $id_producto
     * @param int $cantidad
     * @param int|null $id_origen
     * @param int |null $id_botiquin_origen
     * @param int $id_destino
     * @param string $estado
     * @param string $id_responsable
     */
    public function __construct(int $id_movimiento, string $tipo_movimiento, int $id_producto, int $cantidad, ?int $id_origen, ?int $id_botiquin_origen, int $id_destino, string $estado, string $id_responsable)
    {
        $this->id_movimiento = $id_movimiento;
        $this->tipo_movimiento = $tipo_movimiento;
        $this->id_producto = $id_producto;
        $this->cantidad = $cantidad;
        $this->id_origen = $id_origen;
        $this->id_botiquin_origen = $id_botiquin_origen;
        $this->id_destino = $id_destino;
        $this->estado = $estado;
        $this->id_responsable = $id_responsable;
    }

    public function getId(): int
    {
        return $this->id_movimiento;
    }

    public function setId(int $id_movimiento): void
    {
        $this->id_movimiento = $id_movimiento;
    }

    public function getTipoMovimiento(): string
    {
        return $this->tipo_movimiento;
    }

    public function setTipoMovimiento(string $tipo_movimiento): void
    {
        $this->tipo_movimiento = $tipo_movimiento;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto(int $id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getIdOrigen(): ?int
    {
        return $this->id_origen;
    }

    public function setIdOrigen(?int $id_origen): void
    {
        $this->id_origen = $id_origen;
    }

    public function getIdBotiquinOrigen(): ?int
    {
        return $this->id_botiquin_origen;
    }

    public function setIdBotiquinOrigen(?int $id_botiquin_origen): void
    {
        $this->id_botiquin_origen = $id_botiquin_origen;
    }

    public function getIdDestino(): int
    {
        return $this->id_destino;
    }

    public function setIdDestino(int $id_destino): void
    {
        $this->id_destino = $id_destino;
    }

    public function getFechaMovimiento(): string
    {
        return $this->fecha_movimiento;
    }

    public function setFechaMovimiento(string $fecha_movimiento): void
    {
        $this->fecha_movimiento = $fecha_movimiento;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getIdResponsable(): string
    {
        return $this->id_responsable;
    }

    public function setIdResponsable(string $id_responsable): void
    {
        $this->id_responsable = $id_responsable;
    }




}