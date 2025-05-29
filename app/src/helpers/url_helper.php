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
 * Devuelve la ruta completa a un archivo de vista usando la configuración centralizada
 *
 * @param string $view Ruta relativa de la vista (puede usar notación con puntos)
 * @return string Ruta completa al archivo de vista
 * @throws Exception Si la ruta de vista no se encuentra configurada
 */
function viewPath(string $view): string
{
    global $routeConfig;
    
    // Si la vista ya es una ruta completa, devolverla directamente
    if ($view[0] === '/') {
        return $routeConfig['view_dir'] . $view;
    }
    
    // Comprobar si la vista usa notación de punto (ej: "entity.hospitals.dashboard")
    if (str_contains($view, '.')) {
        $parts = explode('.', $view);
        $viewFile = array_pop($parts); // Obtener el nombre del archivo
        $viewDirectory = implode('.', $parts); // Obtener la ruta de directorio
        
        // Navegar la estructura de view_paths para encontrar la ruta correcta
        $configPath = $routeConfig['view_paths'];
        foreach ($parts as $part) {
            if (isset($configPath[$part])) {
                $configPath = $configPath[$part];
            } else {
                throw new Exception("Ruta de vista no configurada: {$viewDirectory}");
            }
        }
        
        return $routeConfig['view_dir'] . '/' . (is_array($configPath) ? implode('/', $parts) : $configPath) . '/' . $viewFile . '.php';
    }
    
    // Si no usa notación de punto, buscar directamente en el primer nivel de view_paths
    if (isset($routeConfig['view_paths'][$view])) {
        $pathConfig = $routeConfig['view_paths'][$view];
        return $routeConfig['view_dir'] . '/' . (is_array($pathConfig) ? $view : $pathConfig) . '.php';
    }
    
    // Si no se encuentra configurada, asumir que es una ruta directa bajo view_dir
    return $routeConfig['view_dir'] . '/' . $view;
}
