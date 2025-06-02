<?php

namespace model\entity;

// Representa una reposición de productos en un botiquín desde un almacén
class Reposicion
{
    private int $id_reposicion;
    private int $id_almacen;
    private int $id_botiquin;
    private int $id_producto;
    private int $cantidad;
    private string $fecha_reposicion;
    private string $estado; // PENDIENTE, COMPLETADO, CANCELADO
    private string $id_usuario;

    /**
     * @param int $id_reposicion
     * @param int $id_almacen
     * @param int $id_botiquin
     * @param int $id_producto
     * @param string $estado
     * @param string $id_usuario
     * @param int $cantidad
     */
    public function __construct(int $id_reposicion, int $id_almacen, int $id_botiquin, int $id_producto, string $estado, string $id_usuario, int $cantidad)
    {
        $this->id_reposicion = $id_reposicion;
        $this->id_almacen = $id_almacen;
        $this->id_botiquin = $id_botiquin;
        $this->id_producto = $id_producto;
        $this->estado = $estado;
        $this->id_usuario = $id_usuario;
        $this->cantidad = $cantidad;
    }

    public function getId(): int
    {
        return $this->id_reposicion;
    }

    public function setId(int $id_reposicion): void
    {
        $this->id_reposicion = $id_reposicion;
    }

    public function getIdAlmacen(): int
    {
        return $this->id_almacen;
    }

    public function setIdAlmacen(int $id_almacen): void
    {
        $this->id_almacen = $id_almacen;
    }

    public function getIdBotiquin(): int
    {
        return $this->id_botiquin;
    }

    public function setIdBotiquin(int $id_botiquin): void
    {
        $this->id_botiquin = $id_botiquin;
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

    public function getFechaReposicion(): string
    {
        return $this->fecha_reposicion;
    }

    public function setFechaReposicion(string $fecha_reposicion): void
    {
        $this->fecha_reposicion = $fecha_reposicion;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getIdUsuario(): string
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(string $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }




}