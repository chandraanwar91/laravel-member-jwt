<?php

return [
    'fetch'       => PDO::FETCH_CLASS,
    'default'     => env('DB_CONNECTION', 'mysql'),
    'migrations'  => 'migrations',
    'connections' => [
        'mysql'   => [
            'read'      => [
                'host' => env('MYSQL_DB_SLAVE_HOST', env('MYSQL_DB_HOST', '127.0.0.1')),
            ],
            'write'     => [
                'host' => env('MYSQL_DB_HOST', '127.0.0.1'),

            ],
            'driver'    => 'mysql',
            'port'      => env('MYSQL_DB_PORT', 3306),
            'database'  => env('MYSQL_DB_DATABASE', 'forge'),
            'username'  => env('MYSQL_DB_USERNAME', 'forge'),
            'password'  => env('MYSQL_DB_PASSWORD', ''),
            'charset'   => env('MYSQL_DB_CHARSET', 'utf8'),
            'collation' => env('MYSQL_DB_COLLATION', 'utf8_unicode_ci'),
            'prefix'    => env('MYSQL_DB_PREFIX', ''),
            'timezone'  => env('MYSQL_DB_TIMEZONE', '+00:00'),
            'strict'    => env('MYSQL_DB_STRICT_MODE', false),
        ],
        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGO_DB_HOST', 'localhost'),
            'port'     => env('MONGO_DB_PORT', 27017),
            'database' => env('MONGO_DB_DATABASE', 'ma_local'),
            'username' => env('MONGO_DB_USERNAME', null),
            'password' => env('MONGO_DB_PASSWORD', null),
            'options'  => [
                'database' => env('MONGO_DB_AUTH_DB'),
            ],
        ],
    ],
    'redis'       => [
        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
            'password' => env('REDIS_PASSWORD', null),
        ],
    ],
];
