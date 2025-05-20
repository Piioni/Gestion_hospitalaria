<?php

namespace model\entity;

class User
{
    private int $id;
    private string $nombre;
    private string $email;
    private string $password;
    private string $rol;
    private string $hospital_id;
    private string $planta_id;
    private string $botiquin_id;

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function setRol(string $rol): void
    {
        $this->rol = $rol;
    }

    public function getHospitalId(): string
    {
        return $this->hospital_id;
    }

    public function setHospitalId(string $hospital_id): void
    {
        $this->hospital_id = $hospital_id;
    }

    public function getPlantaId(): string
    {
        return $this->planta_id;
    }

    public function setPlantaId(string $planta_id): void
    {
        $this->planta_id = $planta_id;
    }

    public function getBotiquinId(): string
    {
        return $this->botiquin_id;
    }

    public function setBotiquinId(string $botiquin_id): void
    {
        $this->botiquin_id = $botiquin_id;
    }




}