<?php

return [

    'default' => env('BROADCAST_DRIVER', 'redis'),

    'echo_host_mode' => env('ECHO_HOST_MODE', 'path'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_KEY'),
            'secret' => env('PUSHER_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_CLUSTER', 'eu'),
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_BROADCAST_CONNECTION', 'default'),
        ],

    ],

];
