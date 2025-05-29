<?php

namespace controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        // Renderizar la vista de inicio
        $data = [
            'title' => 'Bienvenido al Sistema de Gestión Hospitalaria',
            'navTitle' => 'Inicio'
        ];

        // Usando la notación de punto para referenciar la vista
        $this->render('pages.homepage', $data);
    }
}
