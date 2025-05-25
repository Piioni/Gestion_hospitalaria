<?php

namespace model\entity;

class Botiquin
{
    private int $id_botiquin;
    private string $id_planta;
    private string $nombre;
    private int $capacidad;

    /**
     * @param int $id_botiquin
     * @param string $id_planta
     * @param string $nombre
     * @param int $capacidad
     */
    public function __construct(int $id_botiquin, string $id_planta, string $nombre, int $capacidad)
    {
        $this->id_botiquin = $id_botiquin;
        $this->id_planta = $id_planta;
        $this->nombre = $nombre;
        $this->capacidad = $capacidad;
    }

    public function getId(): int
    {
        return $this->id_botiquin;
    }

    public function setId(int $id_botiquin): void
    {
        $this->id_botiquin = $id_botiquin;
    }

    public function getIdPlanta(): string
    {
        return $this->id_planta;
    }

    public function setIdPlanta(string $id_planta): void
    {
        $this->id_planta = $id_planta;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getCapacidad(): int
    {
        return $this->capacidad;
    }

    public function setCapacidad(int $capacidad): void
    {
        $this->capacidad = $capacidad;
    }
}