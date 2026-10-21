<?php

return [

    'default' => env('QUEUE_DRIVER', 'redis'),

    'connections' => [

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'default'),
            'queue' => 'default',
            'retry_after' => 60,
            'block_for' => null,
        ],

    ],

    // The failed_jobs table predates the uuid column, so the plain database driver is used.
    'failed' => [
        'driver' => 'database',
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
