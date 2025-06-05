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


}
