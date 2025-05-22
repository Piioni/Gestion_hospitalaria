<?php

namespace model\service;

use model\repository\ProductoRepository;

class ProductoService
{
    private ProductoRepository $productoRepository;

    public function __construct()
    {
        $this->productoRepository = new ProductoRepository();
    }

    public function create($codigo, $nombre, $descripcion, $unidad_medida = null): bool
    {
        return $this->productoRepository->create($codigo, $nombre, $descripcion, $unidad_medida);
    }

    public function update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida = null): bool
    {
        return $this->productoRepository->update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida);
    }

    public function delete($id_producto): bool
    {
        return $this->productoRepository->delete($id_producto);
    }

    public function getAll()
    {
        return $this->productoRepository->getAll();
    }

    public function getById($id_producto)
    {
        return $this->productoRepository->getById($id_producto);
    }

    public function getByCodigo($codigo)
    {
        return $this->productoRepository->getByCodigo($codigo);
    }

}