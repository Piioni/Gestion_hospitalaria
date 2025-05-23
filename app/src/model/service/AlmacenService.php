<?php

namespace model\service;

use model\entity\Almacen;
use model\repository\AlmacenRepository;

class AlmacenService
{
    private AlmacenRepository $almacenRepository;

    public function __construct()
    {
        $this->almacenRepository = new AlmacenRepository();
    }

    public function createAlmacen($nombre, $tipo, $id_hospital, $id_planta): bool
    {
        // Validar los parámetros de entrada
        if (empty($tipo) || empty($id_hospital) || empty($nombre)) {
            throw new \InvalidArgumentException("Los campos tipo, id_hospital y nombre son obligatorios.");
        }
        // Verificar que en la planta no exista ya un almacen
        if ($id_planta && $this->almacenRepository->getByPlantaId($id_planta)) {
            throw new \InvalidArgumentException("Ya existe un almacen en la planta seleccionada.");
        }
        return $this->almacenRepository->create($nombre, $tipo, $id_hospital, $id_planta,);
    }

    public function updateAlmacen($id_almacen, $nombre, $tipo, $id_hospital, $id_planta): bool
    {
        // Validar los parámetros de entrada
        if (empty($tipo) || empty($id_hospital) || empty($nombre)) {
            throw new \InvalidArgumentException("Los campos tipo, id_hospital y nombre son obligatorios.");
        }

        // Verificar que en la planta no exista ya un almacen (a menos que sea el mismo que estamos editando)
        if ($id_planta) {
            $existingAlmacen = $this->almacenRepository->getByPlantaId($id_planta);
            if ($existingAlmacen && $existingAlmacen->getId() != $id_almacen) {
                throw new \InvalidArgumentException("Ya existe un almacen en la planta seleccionada.");
            }
        }

        return $this->almacenRepository->update($id_almacen, $nombre, $tipo, $id_hospital, $id_planta);
    }

    public function deleteAlmacen($id_almacen): bool
    {
        return $this->almacenRepository->delete($id_almacen);
    }

    public function getAllAlmacenes()
    {
        return $this->almacenRepository->getAll();
    }

    public function getAlmacenById($id_almacen) : ?Almacen
    {
        return $this->almacenRepository->getById($id_almacen);
    }

    public function getAlmacenByHospitalId($id_hospital): ?Almacen
    {
        return $this->almacenRepository->getByHospitalId($id_hospital);
    }

    public function getAlmacenByPlantaId($id_planta): ?Almacen
    {
        return $this->almacenRepository->getByPlantaId($id_planta);
    }



}