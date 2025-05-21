<?php

namespace model\entity;

class Planta
{
    private int $id_planta;
    private int $id_hospital;
    private string $nombre;

    public function __construct(int $id_planta, int $id_hospital, string $nombre)
    {
        $this->id_planta = $id_planta;
        $this->id_hospital = $id_hospital;
        $this->nombre = $nombre;
    }

    public function getId(): int
    {
        return $this->id_planta;
    }

    public function getIdPlanta(): int
    {
        return $this->id_planta;
    }

    public function setIdPlanta(int $id_planta): void
    {
        $this->id_planta = $id_planta;
    }

    public function getIdHospital(): int
    {
        return $this->id_hospital;
    }

    public function setIdHospital(int $id_hospital): void
    {
        $this->id_hospital = $id_hospital;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
}