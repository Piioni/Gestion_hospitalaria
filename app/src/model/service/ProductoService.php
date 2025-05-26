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

    public function getAll(): array
    {
        return $this->productoRepository->getAll();
    }

    public function getProductoById($id_producto): ?Producto
    {
        return $this->productoRepository->getById($id_producto);
    }

    public function getProductosByCodigoAndAlmacen($codigo, $almacen): array
    {
        return $this->productoRepository->getByCodigoAndAlmacen($codigo, $almacen);
    }

    public function getProductosByCodigoAndBotiquin(string $codigo, $botiquin) : array
    {
        return $this->productoRepository->getByCodigoAndBotiquin($codigo, $botiquin);
    }

    public function getProductosByCodigo(string $codigo) : array
    {
        return $this->productoRepository->getByCodigo($codigo);

    }

    public function getProductosByAlmacen(string $almacen): array
    {
        return $this->productoRepository->getByAlmacen($almacen);
    }

    public function getProductosByBotiquin(string $botiquin): array
    {
        return $this->productoRepository->getByBotiquin($botiquin);
    }

    /**
     * Valida los campos obligatorios del producto
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
