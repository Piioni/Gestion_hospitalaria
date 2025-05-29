<?php

namespace controllers;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use RuntimeException;

class BaseController
{
    /**
     * Renderiza una vista
     *
     * @param string $view Ruta de la vista (puede usar notación con puntos, ej: "entity.hospitals.dashboard")
     * @param array $data Datos a pasar a la vista
     * @throws RuntimeException|Exception Si la vista no existe
     */
    protected function render(string $view, array $data = []): void
    {
        // Extraer variables para la vista
        extract($data);

        // Obtener la ruta completa de la vista usando la función viewPath
        $viewPath = viewPath($view);
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new RuntimeException("Vista no encontrada: $viewPath");
        }
    }

    /**
     * Redirige a una URL específica con parámetros opcionales
     * @param string $url URL a la que redirigir
     * @param array $params Parámetros de consulta opcionales para agregar a la URL
     * @return void
     */
    #[NoReturn]
    protected function redirect(string $url, array $params = []): void
    {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        header("Location: $url");
        exit;
    }

    protected function getCurrentUserId(): int
    {
        return $_SESSION['user_id'];
    }

    protected function getCurrentUserRole(): string
    {
        return $_SESSION['user_role'];
    }
}