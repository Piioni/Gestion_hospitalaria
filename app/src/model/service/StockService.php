<?php

namespace model\service;

use model\repository\StockAlmacenRepository;
use model\repository\StockBotiquinRepository;

/**
 * Servicio combinado para operaciones que pueden implicar ambos tipos de stock
 */
class StockService
{
    private StockAlmacenService $stockAlmacenService;
    private StockBotiquinService $stockBotiquinService;
    
    public function __construct()
    {
        $this->stockAlmacenService = new StockAlmacenService();
        $this->stockBotiquinService = new StockBotiquinService();
    }
    
    /**
     * Obtiene productos con stock bajo de ambos almacenes y botiquines
     */
    public function getAllProductosStockBajo(): array
    {
        $stockBajoAlmacen = $this->stockAlmacenService->getProductosStockBajo();
        $stockBajoBotiquin = $this->stockBotiquinService->getProductosStockBajo();
        
        // Agregamos un campo para distinguir el tipo
        foreach ($stockBajoAlmacen as &$item) {
            $item['tipo_ubicacion'] = 'ALMACEN';
        }
        
        foreach ($stockBajoBotiquin as &$item) {
            $item['tipo_ubicacion'] = 'BOTIQUIN';
        }
        
        // Combinamos los resultados
        $combined = array_merge($stockBajoAlmacen, $stockBajoBotiquin);
        
        // Ordenamos por cantidad ascendente
        usort($combined, function($a, $b) {
            return $a['cantidad'] <=> $b['cantidad'];
        });
        
        return $combined;
    }
}
