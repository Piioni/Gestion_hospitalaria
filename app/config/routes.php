<?php

return[
    "view_dir" => __DIR__ . '/../src/view',

    "routes" => [
        // Public pages
        '/' => 'pages/homepage.php',
        '/homepage' => 'pages/homepage.php',

        // Error pages
        '/404' => 'errors/404.php',

        // Auth pages
        '/login' => 'auth/login.php',
        '/logout' => 'auth/logout.php',

        // User pages
        '/users' => 'entity/users/index_user.php',
        '/users/dashboard' => 'entity/users/dashboard_user.php',
        '/users/locations' => 'entity/users/user_locations.php',
        '/users/create' => 'entity/users/create_user.php',
        '/users/edit' => 'entity/users/edit_user.php',
        '/users/delete' => 'entity/users/deleteUser.php',

        // Ubicaciones pages
        // Hospital pages
        '/ubicaciones/hospitals' => 'entity/ubicaciones/hospitals/dashboard_hospital.php',
        '/ubicaciones/hospitals/create' => 'entity/ubicaciones/hospitals/create_hospital.php',
        '/ubicaciones/hospitals/edit' => 'entity/ubicaciones/hospitals/edit_hospital.php',
        '/ubicaciones/hospitals/delete' => 'entity/ubicaciones/hospitals/delete_hospital.php',

        // Planta pages
        '/ubicaciones/plantas' => 'entity/ubicaciones/plantas/dashboard_planta.php',
        '/ubicaciones/plantas/create' => 'entity/ubicaciones/plantas/create_planta.php',
        '/ubicaciones/plantas/edit' => 'entity/ubicaciones/plantas/edit_planta.php',
        '/ubicaciones/plantas/delete' => 'entity/ubicaciones/plantas/delete_planta.php',

        // Botiquines pages
        '/ubicaciones/botiquines' => 'entity/ubicaciones/botiquines/dashboard_botiquin.php',
        '/ubicaciones/botiquines/create' => 'entity/ubicaciones/botiquines/create_botiquin.php',
        '/ubicaciones/botiquines/edit' => 'entity/ubicaciones/botiquines/edit_botiquin.php',
        '/ubicaciones/botiquines/delete' => 'entity/ubicaciones/botiquines/delete_botiquin.php',

        // Almacen pages
        '/almacenes' => 'entity/ubicaciones/almacenes/dashboard_almacen.php',
        '/almacenes/create' => 'entity/ubicaciones/almacenes/create_almacen.php',
        '/almacenes/edit' => 'entity/ubicaciones/almacenes/edit_almacen.php',
        '/almacenes/delete' => 'entity/ubicaciones/almacenes/delete_almacen.php',

        // Producto pages
        '/productos' => 'entity/productos/dashboard_producto.php',
        '/productos/create' => 'entity/productos/create_producto.php',
        '/productos/edit' => 'entity/productos/edit_producto.php',
        '/productos/delete' => 'entity/productos/delete_producto.php',

        // Stock pages
        '/stock' => 'entity/stock/dashboard_stock.php',
    ]
];
