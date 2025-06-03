<?php

namespace model\service;

use model\entity\Almacen;
use model\repository\AlmacenRepository;

class AlmacenService
{
    private AlmacenRepository $almacenRepository;
    private UserLocationService $userLocationService;

    public function __construct()
    {
        $this->almacenRepository = new AlmacenRepository();
        $this->userLocationService = new UserLocationService();
    }

    public function createAlmacen($nombre, $tipo, $id_hospital, $id_planta): bool
    {
        // Validar los parámetros de entrada
        if (empty($tipo) || empty($id_hospital) || empty($nombre)) {
            throw new \InvalidArgumentException("Los campos tipo, id_hospital y nombre son obligatorios.");
        }
        // Verificar que no exista un almacen general en el hospital
        if ($tipo === "GENERAL" && $this->almacenRepository->getByHospitalId($id_hospital)) {
            throw new \InvalidArgumentException("Ya existe un almacen general en el hospital seleccionado.");
        }
        // Verificar que en la planta no exista ya un almacen
        if ($id_planta && $this->almacenRepository->getByPlantaId($id_planta)) {
            throw new \InvalidArgumentException("Ya existe un almacen en la planta seleccionada.");
        }
        return $this->almacenRepository->create($nombre, $tipo, $id_hospital, $id_planta);
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

        // Verificar que no exista un almacen general en el hospital (a menos que sea el mismo que estamos editando)
        if ($tipo === "GENERAL") {
            $existingAlmacen = $this->almacenRepository->getByHospitalId($id_hospital);
            if ($existingAlmacen && $existingAlmacen->getId() != $id_almacen) {
                throw new \InvalidArgumentException("Ya existe un almacen general en el hospital seleccionado.");
            }
        }

        return $this->almacenRepository->update($id_almacen, $nombre, $tipo, $id_hospital, $id_planta);
    }

    public function deleteAlmacen($id_almacen): bool
    {
        return $this->almacenRepository->delete($id_almacen);
    }

    public function getAllAlmacenes(): array
    {
        return $this->almacenRepository->getAll();
    }

    public function getAlmacenById($id_almacen): ?Almacen
    {
        return $this->almacenRepository->getById($id_almacen);
    }

    public function getGeneralByHospitalId($id_hospital): ?Almacen
    {
        return $this->almacenRepository->getByHospitalId($id_hospital);
    }

    public function getAlmacenByPlantaId($id_planta): ?Almacen
    {
        return $this->almacenRepository->getByPlantaId($id_planta);
    }

    public function getAlmacenesForUser($userId, $userRole, $filtroHospital = null, $filtroTipo = null): array
    {
        $almacenes = match ($userRole) {
            'ADMINISTRADOR', 'GESTOR_GENERAL' => $this->almacenRepository->getAll(),
            'GESTOR_HOSPITAL' => $this->userLocationService->getAssignedAlmacenesFromHospitals($userId),
            'GESTOR_PLANTA' => $this->userLocationService->getAssignedAlmacenesFromPlantas($userId),
            default => throw new \Exception("Rol de usuario no reconocido"),
        };

        // Aplicar filtros si se proporcionan
        if ($filtroHospital) {
            $almacenes = array_filter($almacenes, function($almacen) use ($filtroHospital) {
                return $almacen->getIdHospital() == $filtroHospital;
            });
        }
        if ($filtroTipo) {
            $almacenes = array_filter($almacenes, function($almacen) use ($filtroTipo) {
                return $almacen->getTipo() == $filtroTipo;
            });
        }
        // Reindexar el array para evitar índices faltantes
        return array_values($almacenes);
    }
}