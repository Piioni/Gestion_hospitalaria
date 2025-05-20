<?php

return[
    "view_dirs" => [
        'pages' => __DIR__ . '/../src/view/pages',
        'layouts' => __DIR__ . '/../src/view/layouts',
        'errors' => __DIR__ . '/../src/view/errors',
    ],

    "routes" => [
        // Public pages
        '/' => '/homepage.php',
        '/homepage' => '/homepage.php',

        // Error pages
        '/404' => '/404.php',
    ]
];