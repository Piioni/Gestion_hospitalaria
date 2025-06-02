<?php

namespace model\service;

use model\repository\LecturaRepository;
use InvalidArgumentException;

class LecturaService
{
    private LecturaRepository $lecturaRepository;
    
    public function __construct()
    {
        $this->lecturaRepository = new LecturaRepository();
    }
    
    /**
     * Obtiene todas las lecturas con opciones de filtrado
     * @param array $filters Filtros a aplicar
     * @return array Lista de lecturas
     */
    public function getAllLecturas(array $filters = []): array
    {
        return $this->lecturaRepository->getAll($filters);
    }
    
    /**
     * Crea una nueva lectura
     * @param int $id_botiquin ID del botiquín
     * @param int $id_producto ID del producto
     * @param int $cantidad Cantidad registrada
     * @param string $id_usuario ID del usuario que registra
     * @return bool Resultado de la operación
     */
    public function createLectura(int $id_botiquin, int $id_producto, int $cantidad, string $id_usuario): bool
    {
        // Validación básica de datos
        if ($id_botiquin <= 0) {
            throw new InvalidArgumentException("El botiquín es obligatorio");
        }
        
        if ($id_producto <= 0) {
            throw new InvalidArgumentException("El producto es obligatorio");
        }
        
        if ($cantidad <= 0) {
            throw new InvalidArgumentException("La cantidad debe ser mayor que cero");
        }
        
        if (empty($id_usuario)) {
            throw new InvalidArgumentException("El usuario es obligatorio");
        }
        
        return $this->lecturaRepository->create($id_botiquin, $id_producto, $cantidad, $id_usuario);
    }
    

}
