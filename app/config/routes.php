<?php

return [
    "view_dir" => __DIR__ . '/../src/view',
    
    // Configuración centralizada de rutas de vistas
    "view_paths" => [
        'pages' => 'pages',
        'layouts' => 'layouts',
        'errors' => 'errors',
        'auth' => 'auth',
        'entity' => [
            'users' => 'entity/users',
            'hospitals' => 'entity/ubicaciones/hospitals',
            'plantas' => 'entity/ubicaciones/plantas',
            'botiquines' => 'entity/ubicaciones/botiquines',
            'almacenes' => 'entity/ubicaciones/almacenes',
            'productos' => 'entity/productos',
            'stocks' => 'entity/stocks',
            'reposiciones' => 'entity/reposiciones',
            'movimientos' => 'entity/movimientos',
            'lecturas' => 'entity/lecturas',
            'etiquetas' => 'entity/etiquetas',
        ]
    ],
    
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

        // Rutas de autenticación
        'logout' => [
            'path' => '/logout',
            'controller' => 'AuthController',
            'method' => 'logout'
        ],
        'register' => [
            'path' => '/register',
            'controller' => 'AuthController',
            'method' => 'register'
        ],
        'password.change' => [
            'path' => '/password/change',
            'controller' => 'AuthController',
            'method' => 'changePassword'
        ],

        // Rutas de usuario
        'users' => [
            'path' => '/users',
            'controller' => 'UserController',
            'method' => 'index'
        ],
        'users.create' => [
            'path' => '/users/create',
            'controller' => 'UserController',
            'method' => 'create'
        ],
        'users.edit' => [
            'path' => '/users/edit',
            'controller' => 'UserController',
            'method' => 'edit'
        ],
        'users.delete' => [
            'path' => '/users/delete',
            'controller' => 'UserController',
            'method' => 'delete'
        ],
        'users.locations' => [
            'path' => '/users/locations',
            'controller' => 'UserController',
            'method' => 'locations'
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
        'plantas.create' => [
            'path' => '/plantas/create',
            'controller' => 'PlantaController',
            'method' => 'create'
        ],
        'plantas.edit' => [
            'path' => '/plantas/edit',
            'controller' => 'PlantaController',
            'method' => 'edit'
        ],
        'plantas.delete' => [
            'path' => '/plantas/delete',
            'controller' => 'PlantaController',
            'method' => 'delete'
        ],

        // Rutas de almacenes
        'almacenes' => [
            'path' => '/almacenes',
            'controller' => 'AlmacenController',
            'method' => 'index'
        ],
        'almacenes.create' => [
            'path' => '/almacenes/create',
            'controller' => 'AlmacenController',
            'method' => 'create'
        ],
        'almacenes.edit' => [
            'path' => '/almacenes/edit',
            'controller' => 'AlmacenController',
            'method' => 'edit'
        ],
        'almacenes.delete' => [
            'path' => '/almacenes/delete',
            'controller' => 'AlmacenController',
            'method' => 'delete'
        ],

        // Rutas de botiquines
        'botiquines' => [
            'path' => '/botiquines',
            'controller' => 'BotiquinController',
            'method' => 'index'
        ],
        'botiquines.create' => [
            'path' => '/botiquines/create',
            'controller' => 'BotiquinController',
            'method' => 'create'
        ],
        'botiquines.edit' => [
            'path' => '/botiquines/edit',
            'controller' => 'BotiquinController',
            'method' => 'edit'
        ],
        'botiquines.delete' => [
            'path' => '/botiquines/delete',
            'controller' => 'BotiquinController',
            'method' => 'delete'
        ],

        // Rutas de productos
        'productos' => [
            'path' => '/productos',
            'controller' => 'ProductController',
            'method' => 'index'
        ],
        'productos.create' => [
            'path' => '/productos/create',
            'controller' => 'ProductController',
            'method' => 'create'
        ],
        'productos.edit' => [
            'path' => '/productos/edit',
            'controller' => 'ProductController',
            'method' => 'edit'
        ],
        'productos.delete' => [
            'path' => '/productos/delete',
            'controller' => 'ProductController',
            'method' => 'delete'
        ],

        // Rutas de stocks
        'stocks' => [
            'path' => '/stocks',
            'controller' => 'StockController',
            'method' => 'index'
        ],
        'stocks.botiquines' => [
            'path' => '/stocks/botiquines',
            'controller' => 'StockController',
            'method' => 'indexBotiquin'
        ],
        'stocks.almacenes' => [
            'path' => '/stocks/almacenes',
            'controller' => 'StockController',
            'method' => 'indexAlmacen'
        ],
        'stocks.create' => [
            'path' => '/stocks/create',
            'controller' => 'StockController',
            'method' => 'create'
        ],
        'stocks.edit' => [
            'path' => '/stocks/edit',
            'controller' => 'StockController',
            'method' => 'edit'
        ],
        'stocks.delete' => [
            'path' => '/stocks/delete',
            'controller' => 'StockController',
            'method' => 'delete'
        ],

        // Rutas de reposiciones
        'reposiciones' => [
            'path' => '/reposiciones',
            'controller' => 'ReposicionController',
            'method' => 'index'
        ],

        // Rutas de movimientos
        'movimientos' => [
            'path' => '/movimientos',
            'controller' => 'MovimientoController',
            'method' => 'index'
        ],
        'movimientos.list' => [
            'path' => '/movimientos/list',
            'controller' => 'MovimientoController',
            'method' => 'list'
        ],
        'movimientos.create' => [
            'path' => '/movimientos/create',
            'controller' => 'MovimientoController',
            'method' => 'create'
        ],
        'movimientos.complete' => [
            'path' => '/movimientos/complete',
            'controller' => 'MovimientoController',
            'method' => 'complete'
        ],
        'movimientos.cancel' => [
            'path' => '/movimientos/cancel',
            'controller' => 'MovimientoController',
            'method' => 'cancel'
        ],

        // Rutas de lecturas
        'lecturas' => [
            'path' => '/lecturas',
            'controller' => 'LecturaController',
            'method' => 'index'
        ],
        'lecturas.create' => [
            'path' => '/lecturas/create',
            'controller' => 'LecturaController',
            'method' => 'create'
        ],

        // Rutas de etiquetas
        'etiquetas' => [
            'path' => '/etiquetas',
            'controller' => 'EtiquetaController',
            'method' => 'index'
        ],
    ]
];
