<?php

namespace model\entity;

class Almacen
{
    private int $id_almacen;

    private string $nombre;
    private string $tipo;
    private int $id_hospital;
    private ?int $id_planta;

    /**
     * @param int $id_almacen
     * @param string $nombre
     * @param string $tipo
     * @param int $id_hospital
     * @param int|null $id_planta
     */
    public function __construct(int $id_almacen, string $nombre, string $tipo, int $id_hospital, int $id_planta = null)
    {
        $this->id_almacen = $id_almacen;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->id_hospital = $id_hospital;
        $this->id_planta = $id_planta;
    }

    public function getId(): int
    {
        return $this->id_almacen;
    }

    public function setId(int $id_almacen): void
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

    public function getIdHospital(): int
    {
        return $this->id_hospital;
    }

    public function setIdHospital(int $id_hospital): void
    {
        $this->id_hospital = $id_hospital;
    }

    public function getIdPlanta(): ?int
    {
        return $this->id_planta;
    }

    public function setIdPlanta(?int $id_planta): void
    {
        $this->id_planta = $id_planta;
    }


}