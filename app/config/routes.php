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
        '/users' => 'users/index.php',
        '/users/create' => 'users/create.php',
        '/users/edit' => 'users/edit.php',
        '/users/delete' => 'users/delete.php',
        '/users/list' => 'users/list.php',

        // Hospital pages
        '/hospitals' => 'hospitals/index.php',
        '/hospitals/create' => 'hospitals/create.php',
        '/hospitals/edit' => 'hospitals/edit.php',
        '/hospitals/delete' => 'hospitals/delete.php',
        '/hospitals/list' => 'hospitals/list.php',

        // Planta pages
        '/plantas' => 'plantas/index.php',
        '/plantas/create' => 'plantas/create.php',
        '/plantas/edit' => 'plantas/edit.php',
        '/plantas/delete' => 'plantas/delete.php',
        '/plantas/list' => 'plantas/list.php',
    ]
];
