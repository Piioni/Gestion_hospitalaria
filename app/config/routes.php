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
        '/register' => 'auth/register.php',
        '/logout' => 'auth/logout.php',

        // User pages
        '/users' => 'entity/users/index_user.php',
        '/users/create' => 'entity/users/create_user.php',
        '/users/edit' => 'entity/users/edit_user.php',
        '/users/delete' => 'entity/users/deleteUser.php',
        '/users/list' => 'entity/users/list_user.php',

        // Hospital pages
        '/hospitals' => 'entity/hospitals/index_hospital.php',
        '/hospitals/create' => 'entity/hospitals/create_hospital.php',
        '/hospitals/edit' => 'entity/hospitals/edit_hospital.php',
        '/hospitals/delete' => 'entity/hospitals/delete_hospital.php',
        '/hospitals/list' => 'entity/hospitals/list_hospital.php',

        // Planta pages
        '/plantas' => 'entity/plantas/index_planta.php',
        '/plantas/create' => 'entity/plantas/create_planta.php',
        '/plantas/edit' => 'entity/plantas/edit_planta.php',
        '/plantas/delete' => 'entity/plantas/delete_planta.php',
        '/plantas/list' => 'entity/plantas/list_planta.php',

        // Almacen pages
        '/almacenes' => 'entity/almacenes/indexAlmacen.php',
        '/almacenes/create' => 'entity/almacenes/create_almacen.php',
        '/almacenes/edit' => 'entity/almacenes/edit_almacen.php',
        '/almacenes/delete' => 'entity/almacenes/delete_almacen.php',
        '/almacenes/list' => 'entity/almacenes/list_almacen.php',

        // Producto pages
        '/productos' => 'entity/productos/indexProducto.php',
        '/productos/create' => 'entity/productos/create_producto.php',
        '/productos/edit' => 'entity/productos/edit_producto.php',
        '/productos/delete' => 'entity/productos/delete_producto.php',
        '/productos/list' => 'entity/productos/list_producto.php',
    ]
];
