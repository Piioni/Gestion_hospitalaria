<?php

return[
    "view_dirs" => [
        'pages' => __DIR__ . '/../src/view/pages',
        'errors' => __DIR__ . '/../src/view/errors',
        'auth' => __DIR__ . '/../src/view/auth',
        'users' => __DIR__ . '/../src/view/users',
        'hospitals' => __DIR__ . '/../src/view/hospitals',
        'plantas' => __DIR__ . '/../src/view/plantas',
    ],

    "routes" => [
        // Public pages
        '/' => '/homepage.php',
        '/homepage' => '/homepage.php',

        // Error pages
        '/404' => '/404.php',

        // Auth pages
        '/login' => '/login.php',
        '/register' => '/register.php',
        '/logout' => '/logout.php',

        // User pages
        '/users' => '/index.php',
        '/users/create' => '/create.php',
        '/users/edit' => '/edit.php',
        '/users/delete' => '/delete.php',
        '/users/list' => '/list.php',

        // Hospital pages
        '/hospitals' => '/index.php',
        '/hospitals/create' => '/create.php',
        '/hospitals/edit' => '/edit.php',
        '/hospitals/delete' => '/delete.php',
        '/hospitals/list' => '/list.php',


    ]
];