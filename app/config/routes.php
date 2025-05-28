<?php
return [
    "view_dir" => __DIR__ . '/../src/view',
    "routes" => [
        // Rutas públicas
        'home' => [
            'path' => '/',
            'controller' => 'HomeController',
            'method' => 'index'
        ],
        'login' => [
            'path' => '/login',
            'controller' => 'AuthController',
            'method' => 'login'
        ],

        // Rutas de hospitales
        'hospitals' => [
            'path' => '/hospitals',
            'controller' => 'HospitalController',
            'method' => 'index'
        ],
        'hospitals.create' => [
            'path' => '/hospitals/create',
            'controller' => 'HospitalController',
            'method' => 'create'
        ],
        'hospitals.edit' => [
            'path' => '/hospitals/edit',
            'controller' => 'HospitalController',
            'method' => 'edit'
        ],
        'hospitals.delete' => [
            'path' => '/hospitals/delete',
            'controller' => 'HospitalController',
            'method' => 'delete'
        ],

        // Rutas de plantas
        'plantas' => [
            'path' => '/plantas',
            'controller' => 'PlantaController',
            'method' => 'index'
        ],
        'plantas.edit' => [
            'path' => '/plantas/edit',
            'controller' => 'PlantaController',
            'method' => 'edit'
        ],
        // ... más rutas
    ]
];