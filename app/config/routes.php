<?php

return[
    "view_dirs" => [
        'pages' => __DIR__ . '/../src/view/pages',
        'layouts' => __DIR__ . '/../src/view/layouts',
        'errors' => __DIR__ . '/../src/view/errors',
        'auth' => __DIR__ . '/../src/view/auth',
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
    ]
];