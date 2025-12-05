<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para consumir la API REST de Node.js
    |
    */

    'base_url' => env('API_BASE_URL', 'http://localhost:3000'),
    'timeout' => env('API_TIMEOUT', 30),
];


