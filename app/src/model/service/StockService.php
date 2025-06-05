<?php

namespace model\service;

use model\repository\StockAlmacenRepository;
use model\repository\StockBotiquinRepository;

class StockService
{
    private StockAlmacenService $stockAlmacenService;
    private StockBotiquinService $stockBotiquinService;
    
    public function __construct()
    {
        $this->stockAlmacenService = new StockAlmacenService();
        $this->stockBotiquinService = new StockBotiquinService();
    }

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

    public function getStockStats(): array
    {
        $stats = [
            'total_productos_almacen' => 0,
            'total_productos_botiquin' => 0,
        ];

        $stockBajoAlmacen = $this->stockAlmacenService->getProductosStockBajo();
        $stockBajoBotiquin = $this->stockBotiquinService->getProductosStockBajo();

        $stats['productos_bajo_stock_almacen'] = count($stockBajoAlmacen);
        $stats['productos_bajo_stock_botiquin'] = count($stockBajoBotiquin);

        // Obtener estadísticas totales (esto sería mejor implementarlo en los repositorios para evitar cargar todos los datos)
        $almacenes = (new AlmacenService())->getAllAlmacenes();
        $botiquines = (new BotiquinService())->getAllBotiquines();

        foreach ($almacenes as $almacen) {
            $stocks = $this->stockAlmacenService->getStockByAlmacenId($almacen->getId());
            $stats['total_productos_almacen'] += count($stocks);
        }

        foreach ($botiquines as $botiquin) {
            $stocks = $this->stockBotiquinService->getStockByBotiquinId($botiquin->getId());
            $stats['total_productos_botiquin'] += count($stocks);
        }

        return $stats;
    }
}
