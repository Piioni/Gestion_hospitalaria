<?php

namespace model\service;

use model\repository\BotiquinRepository;

class BotiquinService
{
    private BotiquinRepository $botiquinRepository;

    public function __construct()
    {
        $this->botiquinRepository = new BotiquinRepository();
    }

    public function getAllBotiquines(): array
    {
        return $this->botiquinRepository->getAll();
    }

    public function getBotiquinesByPlantaId($plantaId): array
    {
        return $this->botiquinRepository->getByPlantaId($plantaId);
    }

    public function getBotiquinById($id): array
    {
        return $this->botiquinRepository->getBotiquinById($id);
    }

}