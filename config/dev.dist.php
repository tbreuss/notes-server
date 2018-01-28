<?php

$config = [
    'database' => [
        'database_type' => 'mysql',
        'server' => 'localhost',
        'database_name' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8'
    ]
];

return array_replace_recursive(require('prod.env.php'), $config);
