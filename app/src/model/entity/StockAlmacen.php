<?php

namespace model\entity;

class StockAlmacen extends Stock
{
    public function __construct(
        int $id_stock, 
        int $id_producto, 
        int $id_almacen,
        int $cantidad,
    ) {
        parent::__construct($id_stock, $id_producto, $id_almacen, $cantidad);
    }
    
    public function getIdAlmacen(): int
    {
        return $this->id_ubicacion;
    }
    
    public function setIdAlmacen(int $id_almacen): void
    {
        $this->id_ubicacion = $id_almacen;
    }
    
    public function getTipoUbicacion(): string
    {
        return 'ALMACEN';
    }
}
