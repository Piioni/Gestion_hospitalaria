<?php

namespace model\entity;

class Almacen
{
    private int $id_almacen;

    private string $nombre;
    private string $tipo;
    private string $id_planta;
    private string $id_hospital;

    /**
     * @param int $id_almacen
     * @param string $nombre
     * @param string $tipo
     * @param string $id_planta
     * @param string $id_hospital
     */
    public function __construct(int $id_almacen, string $nombre, string $tipo, string $id_planta, string $id_hospital)
    {
        $this->id_almacen = $id_almacen;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->id_planta = $id_planta;
        $this->id_hospital = $id_hospital;
    }

    public function getIdAlmacen(): int
    {
        return $this->id_almacen;
    }

    public function setIdAlmacen(int $id_almacen): void
    {
        $this->id_almacen = $id_almacen;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getIdPlanta(): string
    {
        return $this->id_planta;
    }

    public function setIdPlanta(string $id_planta): void
    {
        $this->id_planta = $id_planta;
    }

    public function getIdHospital(): string
    {
        return $this->id_hospital;
    }

    public function setIdHospital(string $id_hospital): void
    {
        $this->id_hospital = $id_hospital;
    }




}