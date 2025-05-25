<?php

namespace model\service;

use model\entity\Producto;
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
        // Validaciones de datos
        $this->validarCampos($codigo, $nombre, $descripcion);
        
        return $this->productoRepository->create($codigo, $nombre, $descripcion, $unidad_medida);
    }

    public function update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida = null): bool
    {
        // Validaciones de datos
        $this->validarCampos($codigo, $nombre, $descripcion);
        
        return $this->productoRepository->update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida);
    }

    public function delete($id_producto): bool
    {
        return $this->productoRepository->delete($id_producto);
    }

    /**
     * Obtiene todos los productos con filtros opcionales
     * 
     * @param int|null $almacenId Filtrar por almacén
     * @param int|null $botiquinId Filtrar por botiquín
     * @param string|null $nombre Filtrar por nombre
     * @return array Lista de productos que coinciden con los filtros
     */
    public function getAll(?int $almacenId = null, ?int $botiquinId = null, ?string $nombre = null): array
    {
        return $this->productoRepository->getAll($almacenId, $botiquinId, $nombre);
    }

    public function getById($id_producto): ?Producto
    {
        return $this->productoRepository->getById($id_producto);
    }

    public function getByCodigo($codigo): ?Producto
    {
        return $this->productoRepository->getByCodigo($codigo);
    }
    
    /**
     * Valida los campos obligatorios del producto
     * 
     * @param string $codigo
     * @param string $nombre
     * @param string $descripcion
     * @throws \InvalidArgumentException
     */
    private function validarCampos($codigo, $nombre, $descripcion): void
    {
        if (empty($codigo)) {
            throw new \InvalidArgumentException("El código del producto es obligatorio");
        }
        
        if (empty($nombre)) {
            throw new \InvalidArgumentException("El nombre del producto es obligatorio");
        }
        
        if (strlen($codigo) > 50) {
            throw new \InvalidArgumentException("El código no puede exceder los 50 caracteres");
        }
        
        if (strlen($nombre) > 100) {
            throw new \InvalidArgumentException("El nombre no puede exceder los 100 caracteres");
        }

        if (strlen($descripcion) > 255) {
            throw new \InvalidArgumentException("La descripción no puede exceder los 255 caracteres");
        }
    }
}
