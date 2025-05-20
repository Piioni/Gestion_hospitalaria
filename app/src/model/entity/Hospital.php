<?php

namespace model\entity;

class Hospital
{
    private int $id_hospital;
    private string $name;

    public function getIdHospital(): int
    {
        return $this->id_hospital;
    }

    public function setIdHospital(int $id_hospital): void
    {
        $this->id_hospital = $id_hospital;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }



}