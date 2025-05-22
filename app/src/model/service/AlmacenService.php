<?php

namespace model\service;

use model\repository\AlmacenRepository;

class AlmacenService
{
    private AlmacenRepository $almacenRepository;

    public function __construct($almacenRepository)
    {
        $this->almacenRepository = $almacenRepository;
    }

    public function createAlmacen($tipo, $id_planta, $id_hospital): bool
    {
        return $this->almacenRepository->create($tipo, $id_planta, $id_hospital);
    }

    public function updateAlmacen($id_almacen, $tipo, $id_planta, $id_hospital): bool
    {
        return $this->almacenRepository->update($id_almacen, $tipo, $id_planta, $id_hospital);
    }

    public function deleteAlmacen($id_almacen): bool
    {
        return $this->almacenRepository->delete($id_almacen);
    }

    public function getAllAlmacenes()
    {
        return $this->almacenRepository->getAll();
    }

    public function getAlmacenById($id_almacen)
    {
        return $this->almacenRepository->getById($id_almacen);
    }



}