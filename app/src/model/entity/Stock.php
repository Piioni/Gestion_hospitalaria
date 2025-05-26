<?php

namespace model\entity;

class Stock
{
    private int $id_stock;
    private int $id_producto;
    private string $tipo_ubicacion; // 'ALMACEN' o 'BOTIQUIN'
    private int $id_ubicacion;
    private int $cantidad;
    private int $cantidad_minima;
    private string $fecha_actualizacion;

    public function __construct(
        int $id_stock, 
        int $id_producto, 
        string $tipo_ubicacion, 
        int $id_ubicacion, 
        int $cantidad, 
        int $cantidad_minima, 
        string $fecha_actualizacion
    ) {
        $this->id_stock = $id_stock;
        $this->id_producto = $id_producto;
        $this->tipo_ubicacion = $tipo_ubicacion;
        $this->id_ubicacion = $id_ubicacion;
        $this->cantidad = $cantidad;
        $this->cantidad_minima = $cantidad_minima;
        $this->fecha_actualizacion = $fecha_actualizacion;
    }

    public function getId(): int
    {
        return $this->id_stock;
    }

    public function setId(int $id_stock): void
    {
        $this->id_stock = $id_stock;
    }

    public function getIdProducto(): int
    {
        return $this->id_producto;
    }

    public function setIdProducto(int $id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getTipoUbicacion(): string
    {
        return $this->tipo_ubicacion;
    }

    public function setTipoUbicacion(string $tipo_ubicacion): void
    {
        $this->tipo_ubicacion = $tipo_ubicacion;
    }

    public function getIdUbicacion(): int
    {
        return $this->id_ubicacion;
    }

    public function setIdUbicacion(int $id_ubicacion): void
    {
        $this->id_ubicacion = $id_ubicacion;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getStockMinimo(): int
    {
        return $this->cantidad_minima;
    }

    public function setStockMinimo(int $cantidad_minima): void
    {
        $this->cantidad_minima = $cantidad_minima;
    }

    public function getFechaActualizacion(): string
    {
        return $this->fecha_actualizacion;
    }

    public function setFechaActualizacion(string $fecha_actualizacion): void
    {
        $this->fecha_actualizacion = $fecha_actualizacion;
    }
}
