<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas de la aplicación web.
|
*/

return [
    // Rutas públicas
    [
        'path' => '/login',
        'method' => 'GET',
        'controller' => 'AuthController@showLoginForm',
        'name' => 'login',
    ],
    [
        'path' => '/login',
        'method' => 'POST',
        'controller' => 'AuthController@login',
    ],
    [
        'path' => '/logout',
        'method' => 'POST',
        'controller' => 'AuthController@logout',
        'name' => 'logout',
    ],
    
    // Rutas protegidas (requieren autenticación)
    [
        'path' => '/',
        'method' => 'GET',
        'controller' => 'TallerController@index',
        'middleware' => ['AuthMiddleware'],
    ],
    [
        'path' => '/talleres',
        'method' => 'GET',
        'controller' => 'TallerController@index',
        'middleware' => ['AuthMiddleware'],
        'name' => 'talleres.index',
    ],
    [
        'path' => '/talleres/{id}',
        'method' => 'GET',
        'controller' => 'TallerController@show',
        'middleware' => ['AuthMiddleware'],
        'name' => 'talleres.show',
    ],
];

