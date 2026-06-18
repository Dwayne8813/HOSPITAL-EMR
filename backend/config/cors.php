<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This file determines the CORS configuration for your application.
    | Medical systems require strict CORS policies for HIPAA compliance.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CORS Origins
    |--------------------------------------------------------------------------
    |
    | The origins allowed to access your application. Set to '*' to allow
    | any origin, or specify specific origins for production.
    |
    */

    'allowed_origins' => env('CORS_ALLOWED_ORIGINS', [
        'http://localhost:3000',     // Local development (React/Vue)
        'http://localhost:5173',    // Vite dev server
        'http://localhost:8080',    // Alternative dev server
        'http://127.0.0.1:3000',   // Local IP variant
        'http://127.0.0.1:5173',   // Local IP variant
    ]),

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns for allowed origins (for dynamic subdomains).
    |
    */

    'allowed_origins_patterns' => [
        '/^https?:\/\/.*\.hospital\.com$/',
        '/^https?:\/\/.*\.emr\.hospital\.com$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Methods
    |--------------------------------------------------------------------------
    |
    | The HTTP methods allowed to be accessed from the specified origins.
    |
    */

    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Headers
    |--------------------------------------------------------------------------
    |
    | The headers that are allowed to be sent from the browser.
    |
    */

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'X-XSRF-TOKEN',
        'X-CSRF-TOKEN',
        'X-API-KEY',
        'X-Client-ID',
        'X-Hospital-ID',
        'X-Institution-Id',
        'Accept',
        'Origin',
        'Cache-Control',
        'Pragma',
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are accessible to the browser.
    |
    */

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
        'X-Total-Count',
        'X-Page-Count',
        'X-Current-Page',
        'X-Per-Page',
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Max Age
    |--------------------------------------------------------------------------
    |
    | Number of seconds that preflight requests can be cached.
    | Set to 0 to disable caching.
    |
    */

    'max_age' => env('CORS_MAX_AGE', 86400), // 24 hours

    /*
    |--------------------------------------------------------------------------
    | CORS Credentials
    |--------------------------------------------------------------------------
    |
    | Indicates that the response to the request can be exposed when the
    | credentials flag is true. This is required for JWT tokens.
    |
    */

    'supports_credentials' => true,

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Paths to include in CORS configuration. Use regex patterns.
    |
    */

    'paths' => [
        'api/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Hosts
    |--------------------------------------------------------------------------
    |
    | Allowed hosts for CORS. Set to specific hosts in production.
    |
    */

    'allowed_hosts' => env('CORS_ALLOWED_HOSTS', [
        'localhost',
        '127.0.0.1',
    ]),

];
