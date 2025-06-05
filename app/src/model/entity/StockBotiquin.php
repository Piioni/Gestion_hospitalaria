<?php

namespace model\entity;

class StockBotiquin extends Stock
{
    public function __construct(
        int $id_stock, 
        int $id_producto, 
        int $id_botiquin,
        int $cantidad, 
    ) {
        parent::__construct($id_stock, $id_producto, $id_botiquin, $cantidad);
    }
    
    public function getIdBotiquin(): int
    {
        return $this->id_ubicacion;
    }
    
    public function setIdBotiquin(int $id_botiquin): void
    {
        $this->id_ubicacion = $id_botiquin;
    }
    
    public function getTipoUbicacion(): string
    {
        return 'BOTIQUIN';
    }
}
