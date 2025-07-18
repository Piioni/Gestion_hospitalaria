<?php

namespace model\entity;

abstract class Stock
{
    protected int $id_stock;
    protected int $id_ubicacion;
    protected int $id_producto;
    protected int $cantidad;

    public function __construct(
        int $id_stock, 
        int $id_producto, 
        int $id_ubicacion,
        int $cantidad, 
    ) {
        $this->id_stock = $id_stock;
        $this->id_producto = $id_producto;
        $this->id_ubicacion = $id_ubicacion;
        $this->cantidad = $cantidad;
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

    abstract public function getTipoUbicacion(): string;
}
