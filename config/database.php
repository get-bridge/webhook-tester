<?php

return [

    'default' => env('DB_CONNECTION', 'sqlite'),

    'redis' => [

        // Redis connection used by the token and request stores
        'connection' => env('REDIS_CACHE_CONNECTION', 'default'),

        'client' => env('REDIS_CLIENT', 'predis'),

        // No key prefix: laravel-echo-server subscribes to the bare channel names.
        'options' => [],

        'default' => [
            'scheme' => env('SCHEME', 'tcp'),
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'unix_socket' => [
            'scheme' => 'unix',
            'path' => env('REDIS_UNIX_SOCKET', '/var/run/redis/redis.sock'),
        ],

    ],

];
