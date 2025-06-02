<?php

namespace model\entity;

// Representa una lectura de productos en un botiquín
class Lectura
{
    private int $id_lectura;
    private int $id_botiquin;
    private int $id_producto;
    private int $cantidad;
    private string $fecha_lectura;
    private string $id_usuario;

    public function __construct(
        int $id_lectura,
        int $id_botiquin,
        int $id_producto,
        int $cantidad,
        string $fecha_lectura,
        string $id_usuario
    ) {
        $this->id_lectura = $id_lectura;
        $this->id_botiquin = $id_botiquin;
        $this->id_producto = $id_producto;
        $this->cantidad = $cantidad;
        $this->fecha_lectura = $fecha_lectura;
        $this->id_usuario = $id_usuario;
    }

    public function getId(): int
    {
        return $this->id_lectura;
    }

    public function setId(int $id_lectura): void
    {
        $this->id_lectura = $id_lectura;
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

    public function getFechaLectura(): string
    {
        return $this->fecha_lectura;
    }

    public function setFechaLectura(string $fecha_lectura): void
    {
        $this->fecha_lectura = $fecha_lectura;
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