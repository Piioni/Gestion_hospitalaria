<?php

namespace model\service;

use model\repository\AlmacenRepository;

class AlmacenService
{
    private AlmacenRepository $almacenRepository;

    public function __construct()
    {
        $this->almacenRepository = new AlmacenRepository();
    }

    public function createAlmacen($tipo, $id_hospital, $id_planta): bool
    {
        // Validar los parámetros de entrada
        if (empty($tipo) || empty($id_hospital)) {
            throw new \InvalidArgumentException("El tipo e id_hospital son obligatorios!.");
        }
        // Verificar que en la planta no exista ya un almacen
        if ($id_planta && $this->almacenRepository->getByPlantaId($id_planta)) {
            throw new \InvalidArgumentException("Ya existe un almacen en la planta seleccionada.");
        }
        return $this->almacenRepository->create($tipo, $id_hospital, $id_planta,);
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