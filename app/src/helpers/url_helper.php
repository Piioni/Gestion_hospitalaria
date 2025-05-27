<?php

/**
 * Genera una URL basada en el nombre de la ruta
 * 
 * @param string $routeName Nombre de la ruta
 * @param array $params Parámetros de la ruta (opcional)
 * @return string URL generada
 * @throws Exception Si la ruta no existe
 */
function url(string $routeName, array $params = []): string
{
    global $routeConfig;
    
    if (!isset($routeConfig['routes'][$routeName])) {
        throw new Exception("La ruta '{$routeName}' no existe");
    }
    
    $path = $routeConfig['routes'][$routeName]['path'];
    
    // Agregar parámetros como query string si existen
    if (!empty($params)) {
        $path .= '?' . http_build_query($params);
    }
    
    return $path;
}

/**
 * Determina si la ruta actual coincide con un nombre de ruta dado
 * 
 * @param string $routeName Nombre de la ruta
 * @return bool True si coincide, False en caso contrario
 */
function isCurrentRoute(string $routeName): bool
{
    global $routeConfig;
    
    if (!isset($routeConfig['routes'][$routeName])) {
        return false;
    }
    
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routePath = $routeConfig['routes'][$routeName]['path'];
    
    return rtrim($currentPath, '/') === rtrim($routePath, '/');
}
