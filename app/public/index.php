<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers/url_helper.php';

$routeConfig = require __DIR__ . '/../config/routes.php';

// Iniciar sesión al principio para estar disponible en toda la aplicación
session_start();

// Obtener la ruta solicitada
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathInfo = rtrim($requestUri, '/') ?: '/';

function loadController(string $controllerName, string $method): void
{
    $controllerClass = (!str_contains($controllerName, '\\'))
        ? 'controllers\\' . $controllerName
        : $controllerName;
        
    // Verificar si existe la clase del controlador
    if (!class_exists($controllerClass)) {
        throw new RuntimeException("Controlador no encontrado: $controllerClass");
    }

    $controller = new $controllerClass();

    if (!method_exists($controller, $method)) {
        throw new RuntimeException("Método no encontrado: $controllerClass::$method");
    }

    $controller->$method();
}

try {
    $foundRoute = false;

    foreach ($routeConfig['routes'] as $route) {
        // Normalizar la ruta para evitar problemas con barras al final
        $routePath = rtrim($route['path'], '/') ?: '/';

        // Comparar la ruta solicitada con la ruta definida
        if ($routePath === $pathInfo) {
            if (isset($route['controller']) && isset($route['method'])) {
                // Llama al controlador y méto do especificados
                loadController($route['controller'], $route['method']);
            } else {
                // Arquitectura antigua (para migración gradual)
                loadView($route['view']);
            }
            $foundRoute = true;
            break;
        }
    }

    if (!$foundRoute) {
        http_response_code(404);
        include __DIR__ . '/../src/view/errors/404.php';
    }

} catch (RuntimeException $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    http_response_code(403);
    include __DIR__ . '/../src/view/errors/403.php';
}

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
