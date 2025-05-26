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

        // Hospital pages
        '/hospitals' => 'entity/hospitals/dashboard_hospital.php',
        '/hospitals/create' => 'entity/hospitals/create_hospital.php',
        '/hospitals/edit' => 'entity/hospitals/edit_hospital.php',
        '/hospitals/delete' => 'entity/hospitals/delete_hospital.php',

        // Planta pages
        '/plantas' => 'entity/plantas/dashboard_planta.php',
        '/plantas/create' => 'entity/plantas/create_planta.php',
        '/plantas/edit' => 'entity/plantas/edit_planta.php',
        '/plantas/delete' => 'entity/plantas/delete_planta.php',

        // Botiquines pages
        '/botiquines' => 'entity/botiquines/dashboard_botiquin.php',
        '/botiquines/create' => 'entity/botiquines/create_botiquin.php',
        '/botiquines/edit' => 'entity/botiquines/edit_botiquin.php',
        '/botiquines/delete' => 'entity/botiquines/delete_botiquin.php',

        // Almacen pages
        '/almacenes' => 'entity/almacenes/dashboard_almacen.php',
        '/almacenes/create' => 'entity/almacenes/create_almacen.php',
        '/almacenes/edit' => 'entity/almacenes/edit_almacen.php',
        '/almacenes/delete' => 'entity/almacenes/delete_almacen.php',

        // Producto pages
        '/productos' => 'entity/productos/dashboard_producto.php',
        '/productos/create' => 'entity/productos/create_producto.php',
        '/productos/edit' => 'entity/productos/edit_producto.php',
        '/productos/delete' => 'entity/productos/delete_producto.php',
    ]
];
