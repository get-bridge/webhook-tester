<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Webhook Tester-specific Configuration
    |--------------------------------------------------------------------------
    */

    // How many requests each URL will log before returning a 410 Gone, and the user
    // will have to create a new URL.
    'max_requests' => env('WEBHOOK_MAX_REQUESTS', 500),

    // Requests and tokens will expire in 7 days (default)
    'expiry' => env('WEBHOOK_EXPIRY', 604800),

    'env' => env('APP_ENV', 'local'),

    'debug' => (bool) env('APP_DEBUG', true),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'UTC',

    'locale' => 'en',

    'fallback_locale' => 'en',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

];
