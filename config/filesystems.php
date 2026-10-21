<?php

return [

    'disks' => [

        // Laravel 11+ serves the local disk over storage/{path} by default; this app never exposed it.
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => false,
            'throw' => false,
            'report' => false,
        ],

    ],

];
