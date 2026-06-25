<?php

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => 'mysql_db',
            'name' => 'db',
            'user' => 'root',
            'pass' => 'myrootpassword',
            'port' => 3306,
            'charset' => 'utf8mb4'
        ]
    ],
    'version_order' => 'creation'
];
