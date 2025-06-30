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
        // Verificar que no sean los mismos datos
        $productoExistente = $this->productoRepository->getById($id_producto);
        if (!$productoExistente) {
            throw new \InvalidArgumentException("Producto no encontrado");
        }
        if ($productoExistente->getCodigo() === $codigo &&
            $productoExistente->getNombre() === $nombre &&
            $productoExistente->getDescripcion() === $descripcion &&
            $productoExistente->getUnidadMedida() === $unidad_medida) {
            throw new \InvalidArgumentException("No se han realizado cambios en el producto");
        }
        
        return $this->productoRepository->update($id_producto, $codigo, $nombre, $descripcion, $unidad_medida);
    }

    public function delete($id_producto): bool
    {
        return $this->productoRepository->delete($id_producto);
    }

    public function getAllProducts(): array
    {
        return $this->productoRepository->getAll();
    }

    public function getProductoById($id_producto): ?Producto
    {
        return $this->productoRepository->getById($id_producto);
    }

    public function filtrarProductos(array $filtros = []): array
    {
        return $this->productoRepository->filtrarProductos($filtros);
    }

    /**
     * Válida los campos obligatorios del producto
     *
     * @param string $codigo
     * @param string $nombre
     * @param string $descripcion
     * @throws \InvalidArgumentException
     */
    private function validarCampos(string $codigo, string $nombre, string $descripcion): void
    {
        if (empty($codigo)) {
            throw new \InvalidArgumentException("El código del producto es obligatorio");
        }

        if (empty($nombre)) {
            throw new \InvalidArgumentException("El nombre del producto es obligatorio");
        }

        if (strlen($codigo) > 15) {
            throw new \InvalidArgumentException("El código no puede exceder los 15 caracteres");
        }

        if (strlen($nombre) > 30) {
            throw new \InvalidArgumentException("El nombre no puede exceder los 30 caracteres");
        }

        if (strlen($descripcion) > 255) {
            throw new \InvalidArgumentException("La descripción no puede exceder los 255 caracteres");
        }
    }
}
