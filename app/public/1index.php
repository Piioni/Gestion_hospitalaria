<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers/url_helper.php';

$routeConfig = require __DIR__ . '/../config/1routes.php';

function loadView(string $viewPath): void
{
    global $routeConfig;
    $fullPath = $routeConfig["view_dir"] . '/' . $viewPath;
    
    if (file_exists($fullPath)) {
        include($fullPath);
        return;
    }
    
    throw new RuntimeException("View not found: $fullPath");
}

// Obtener la ruta solicitada
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathInfo = rtrim($requestUri, '/') ?: '/';

try {
    // Buscar la ruta que coincida con el path solicitado
    $foundRoute = false;
    foreach ($routeConfig['routes'] as $route) {
        $routePath = rtrim($route['path'], '/') ?: '/';
        
        if ($routePath === $pathInfo) {
            loadView($route['view']);
            $foundRoute = true;
            break;
        }
    }
    
    if (!$foundRoute) {
        http_response_code(404);
        loadView($routeConfig['routes']['404']['view']);
    }
} catch (RuntimeException $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
