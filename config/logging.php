<?php

return [

    // APP_LOG is the Laravel 5 setting the deployments still pass (single, daily,
    // syslog or errorlog); each value matches a default Laravel log channel.
    'default' => env('LOG_CHANNEL', env('APP_LOG', 'single')),

];
