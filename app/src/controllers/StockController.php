<?php

namespace controllers;

use middleware\AuthMiddleware;
use model\service\AlmacenService;
use model\service\BotiquinService;
use model\service\HospitalService;
use model\service\PlantaService;
use model\service\StockService;

class StockController extends BaseController
{
    private StockService $stockService;
    private HospitalService $hospitalService;
    private PlantaService $plantaService;
    private AlmacenService $almacenService;
    private BotiquinService $botiquinService;

    public function __construct()
    {
        $this->stockService = new StockService();
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
        $this->almacenService = new AlmacenService();
        $this->botiquinService = new BotiquinService();
    }

    public function index(): void
    {

    }

    public function indexBotiquin(): void
    {
        AuthMiddleware::requireRole(['ADMINISTRADOR', 'GESTOR_GENERAL', 'GESTOR_HOSPITAL', 'GESTOR_PLANTA', 'GESTOR_BOTIQUIN']);

    }

    public function indexAlmacen(): void
    {

    }

    public function create(): void
    {

    }


}