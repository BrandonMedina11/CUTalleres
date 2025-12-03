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
    // Ruta home
    [
        'path' => '/',
        'method' => 'GET',
        'controller' => 'HomeController@index',
        'name' => 'home',
    ],
    
    // Rutas de autenticación
    [
        'path' => '/registro',
        'method' => 'GET',
        'controller' => 'AuthController@showRegisterForm',
        'name' => 'registro',
    ],
    [
        'path' => '/registro',
        'method' => 'POST',
        'controller' => 'AuthController@register',
    ],
    
    // Ruta catálogo
    [
        'path' => '/catalogo',
        'method' => 'GET',
        'controller' => 'CatalogoController@index',
        'name' => 'catalogo',
    ],
    
    // Ruta detalle de producto
    [
        'path' => '/producto/{id}',
        'method' => 'GET',
        'controller' => 'DetalleProductoController@show',
        'name' => 'producto.show',
    ],
    
    // Ruta 404 (debe ir al final)
    [
        'path' => '/404',
        'method' => 'GET',
        'controller' => 'NotFoundController@index',
        'name' => 'not-found',
    ],
];


