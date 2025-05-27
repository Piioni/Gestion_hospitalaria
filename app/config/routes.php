<?php

return [
    "view_dir" => __DIR__ . '/../src/view',

    "routes" => [
        // Public pages
        'home' => [
            'path' => '/',
            'view' => 'pages/homepage.php'
        ],
        'homepage' => [
            'path' => '/homepage',
            'view' => 'pages/homepage.php'
        ],

        // Error pages
        '404' => [
            'path' => '/404',
            'view' => 'errors/404.php'
        ],

        // Auth pages
        'login' => [
            'path' => '/login',
            'view' => 'auth/login.php'
        ],
        'logout' => [
            'path' => '/logout',
            'view' => 'auth/logout.php'
        ],

        // User pages
        'users' => [
            'path' => '/users',
            'view' => 'entity/users/dashboard_user.php'
        ],
        'users.dashboard' => [
            'path' => '/users',
            'view' => 'entity/users/dashboard_user.php'
        ],
        'users.locations' => [
            'path' => '/users/locations',
            'view' => 'entity/users/user_locations.php'
        ],
        'users.create' => [
            'path' => '/users/create',
            'view' => 'entity/users/create_user.php'
        ],
        'users.edit' => [
            'path' => '/users/edit',
            'view' => 'entity/users/edit_user.php'
        ],
        'users.delete' => [
            'path' => '/users/delete',
            'view' => 'entity/users/deleteUser.php'
        ],

        // Hospital pages
        'hospitals' => [
            'path' => '/hospitals',
            'view' => 'entity/ubicaciones/hospitals/dashboard_hospital.php'
        ],
        'hospitals.dashboard' => [
            'path' => '/hospitals/dashboard',
            'view' => 'entity/ubicaciones/hospitals/dashboard_hospital.php'
        ],
        'hospitals.create' => [
            'path' => '/hospitals/create',
            'view' => 'entity/ubicaciones/hospitals/create_hospital.php'
        ],
        'hospitals.edit' => [
            'path' => '/hospitals/edit',
            'view' => 'entity/ubicaciones/hospitals/edit_hospital.php'
        ],
        'hospitals.delete' => [
            'path' => '/hospitals/delete',
            'view' => 'entity/ubicaciones/hospitals/delete_hospital.php'
        ],

        // Planta pages
        'plantas' => [
            'path' => '/plantas',
            'view' => 'entity/ubicaciones/plantas/dashboard_planta.php'
        ],
        'plantas.dashboard' => [
            'path' => '/plantas/dashboard',
            'view' => 'entity/ubicaciones/plantas/dashboard_planta.php'
        ],
        'plantas.create' => [
            'path' => '/plantas/create',
            'view' => 'entity/ubicaciones/plantas/create_planta.php'
        ],
        'plantas.edit' => [
            'path' => '/plantas/edit',
            'view' => 'entity/ubicaciones/plantas/edit_planta.php'
        ],
        'plantas.delete' => [
            'path' => '/plantas/delete',
            'view' => 'entity/ubicaciones/plantas/delete_planta.php'
        ],

        // Botiquines pages
        'botiquines' => [
            'path' => '/botiquines',
            'view' => 'entity/ubicaciones/botiquines/dashboard_botiquin.php'
        ],
        'botiquines.dashboard' => [
            'path' => '/botiquines/dashboard',
            'view' => 'entity/ubicaciones/botiquines/dashboard_botiquin.php'
        ],
        'botiquines.create' => [
            'path' => '/botiquines/create',
            'view' => 'entity/ubicaciones/botiquines/create_botiquin.php'
        ],
        'botiquines.edit' => [
            'path' => '/botiquines/edit',
            'view' => 'entity/ubicaciones/botiquines/edit_botiquin.php'
        ],
        'botiquines.delete' => [
            'path' => '/botiquines/delete',
            'view' => 'entity/ubicaciones/botiquines/delete_botiquin.php'
        ],

        // Almacen pages
        'almacenes' => [
            'path' => '/almacenes',
            'view' => 'entity/ubicaciones/almacenes/dashboard_almacen.php'
        ],
        'almacenes.dashboard' => [
            'path' => '/almacenes/dashboard',
            'view' => 'entity/ubicaciones/almacenes/dashboard_almacen.php'
        ],
        'almacenes.create' => [
            'path' => '/almacenes/create',
            'view' => 'entity/ubicaciones/almacenes/create_almacen.php'
        ],
        'almacenes.edit' => [
            'path' => '/almacenes/edit',
            'view' => 'entity/ubicaciones/almacenes/edit_almacen.php'
        ],
        'almacenes.delete' => [
            'path' => '/almacenes/delete',
            'view' => 'entity/ubicaciones/almacenes/delete_almacen.php'
        ],

        // Producto pages
        'productos' => [
            'path' => '/productos',
            'view' => 'entity/productos/dashboard_producto.php'
        ],
        'productos.dashboard' => [
            'path' => '/productos/dashboard',
            'view' => 'entity/productos/dashboard_producto.php'
        ],
        'productos.create' => [
            'path' => '/productos/create',
            'view' => 'entity/productos/create_producto.php'
        ],
        'productos.edit' => [
            'path' => '/productos/edit',
            'view' => 'entity/productos/edit_producto.php'
        ],
        'productos.delete' => [
            'path' => '/productos/delete',
            'view' => 'entity/productos/delete_producto.php'
        ],

        // Stock pages
        'stocks' => [
            'path' => '/stocks',
            'view' => 'entity/stocks/dashboard_stock.php'
        ],
        'stocks.dashboard' => [
            'path' => '/stocks/dashboard',
            'view' => 'entity/stocks/dashboard_stock.php'
        ],
        'stocks.botiquines' => [
            'path' => '/stocks/botiquines',
            'view' => 'entity/stocks/stock_botiquin.php'
        ],
        'stocks.almacenes' => [
            'path' => '/stocks/almacenes',
            'view' => 'entity/stocks/stock_almacen.php'
        ],
    ]
];
