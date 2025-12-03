<?php

/*
|--------------------------------------------------------------------------
| Bootstrap de la aplicación
|--------------------------------------------------------------------------
|
| Este archivo inicializa la aplicación Laravel simplificada
|
*/

// Cargar autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración
$app = require_once __DIR__ . '/../config/app.php';

return $app;


