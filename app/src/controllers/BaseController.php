<?php

namespace controllers;

use http\Exception\RuntimeException;
use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        // Extraer variables para la vista
        extract($data);

        // Cargar la vista
        $viewPath = __DIR__ . '/../view/' . $view;
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new RuntimeException("Vista no encontrada: $viewPath");
        }
    }

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