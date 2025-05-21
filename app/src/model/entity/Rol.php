<?php

namespace model\entity;
class Rol
{
    private int $id_rol;
    private string $nombre;

    public function __construct(int $id, string $nombre)
    {
        $this->id_rol = $id;
        $this->nombre = $nombre;
    }

    public function getIdRol(): int
    {
        return $this->id_rol;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

}