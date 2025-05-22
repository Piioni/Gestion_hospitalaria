<?php

namespace model\entity;

class Hospital
{
    private int $id_hospital;
    private string $nombre;
    private string $ubicacion;

    public function __construct(int $id_hospital, string $nombre, string $ubicacion)
    {
        $this->id_hospital = $id_hospital;
        $this->nombre = $nombre;
        $this->ubicacion = $ubicacion;
    }

    public function getId(): int
    {
        return $this->id_hospital;
    }

    public function setId(int $id_hospital): void
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

    public function getUbicacion(): string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(string $ubicacion): void
    {
        $this->ubicacion = $ubicacion;
    }
}
