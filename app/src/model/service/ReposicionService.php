<?php

namespace model\service;

use model\repository\ReposicionRepository;

class ReposicionService
{
    private ReposicionRepository $reposicionRepository;

    public function __construct()
    {
        $this->reposicionRepository = new ReposicionRepository();
    }

    public function crearReposicion($id_producto, $cantidad, $id_almacen, $id_botiquin, $estado, $id_responsable): bool
    {
        return $this->reposicionRepository->create($id_producto, $cantidad, $id_almacen, $id_botiquin, $estado, $id_responsable);
    }

    public function cancelarReposicion(int $id): bool
    {
        return $this->reposicionRepository->cancelar($id);
    }

    public function completarReposicion(int $id): bool
    {
        return $this->reposicionRepository->completar($id);
    }

    public function find($filtros = []): array
    {
        return $this->reposicionRepository->find($filtros);
    }
}
