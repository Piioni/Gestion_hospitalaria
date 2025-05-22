<?php

namespace model\entity;

class Producto
{
    private int $id_producto;
    private string $codigo;
    private string $nombre;
    private string $descripcion;
    private string $unidad_medida;

    public function __construct(int $id_producto, string $codigo, string $nombre, string $descripcion, string $unidad_medida)
    {
        $this->id_producto = $id_producto;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->unidad_medida = $unidad_medida;
    }

    public function getId(): int
    {
        return $this->id_producto;
    }

    public function setId(int $id_producto): void
    {
        $this->id_producto = $id_producto;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getUnidadMedida(): string
    {
        return $this->unidad_medida;
    }

    public function setUnidadMedida(string $unidad_medida): void
    {
        $this->unidad_medida = $unidad_medida;
    }



}