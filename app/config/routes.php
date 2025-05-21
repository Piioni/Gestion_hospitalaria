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
        '/users' => 'users/indexUser.php',
        '/users/create' => 'users/createUser.php',
        '/users/edit' => 'users/editUser.php',
        '/users/delete' => 'users/deleteUser.php',
        '/users/list' => 'users/listUser.php',

        // Hospital pages
        '/hospitals' => 'hospitals/indexHospital.php',
        '/hospitals/create' => 'hospitals/createHospital.php',
        '/hospitals/edit' => 'hospitals/editHospital.php',
        '/hospitals/delete' => 'hospitals/deleteHospital.php',
        '/hospitals/list' => 'hospitals/listHospital.php',

        // Planta pages
        '/plantas' => 'plantas/indexPlanta.php',
        '/plantas/create' => 'plantas/createPlanta.php',
        '/plantas/edit' => 'plantas/editPlanta.php',
        '/plantas/delete' => 'plantas/deletePlanta.php',
        '/plantas/list' => 'plantas/listPlanta.php',
    ]
];
