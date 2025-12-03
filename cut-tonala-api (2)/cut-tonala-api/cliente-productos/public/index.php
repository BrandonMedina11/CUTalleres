<?php

/*
|--------------------------------------------------------------------------
| Punto de entrada de la aplicación
|--------------------------------------------------------------------------
|
| Este archivo maneja todas las peticiones HTTP entrantes
|
*/

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Autoloader para clases App
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Cargar variables de entorno PRIMERO
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// Función helper para obtener variables de entorno (debe estar antes de cargar config)
if (!function_exists('env')) {
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

// Cargar helpers (si no se cargaron con autoload)
if (!function_exists('route')) {
    require_once __DIR__ . '/../app/Helpers/functions.php';
}

// Helper para obtener el request
if (!function_exists('request')) {
    function request() {
        return new class {
            public function input($key = null, $default = null) {
                if ($key === null) {
                    return array_merge($_GET, $_POST);
                }
                return $_POST[$key] ?? $_GET[$key] ?? $default;
            }
            
            public function only($keys) {
                $result = [];
                foreach ((array)$keys as $key) {
                    $result[$key] = $this->input($key);
                }
                return $result;
            }
        };
    }
}

// Cargar rutas
$routes = require __DIR__ . '/../routes/web.php';

// Obtener la ruta actual
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Buscar la ruta que coincida
$matched = false;
foreach ($routes as $route) {
    // Verificar método HTTP
    if (isset($route['method']) && $route['method'] !== $requestMethod) {
        continue;
    }
    
    $pattern = $route['path'];
    
    // Extraer nombres de parámetros
    preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
    $paramNames = $paramNames[1];
    
    // Convertir patrón a regex
    $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $requestUri, $matches)) {
        // Extraer parámetros
        array_shift($matches);
        $params = [];
        foreach ($paramNames as $index => $name) {
            if (isset($matches[$index])) {
                $params[$name] = $matches[$index];
            }
        }
        
        // Ejecutar el controlador
        if (isset($route['controller'])) {
            list($controllerClass, $method) = explode('@', $route['controller']);
            $controllerClass = "App\\Http\\Controllers\\{$controllerClass}";
            
            if (class_exists($controllerClass)) {
                // Instanciar controlador (algunos pueden necesitar servicios específicos)
                $reflection = new ReflectionClass($controllerClass);
                $constructor = $reflection->getConstructor();
                
                if ($constructor && $constructor->getNumberOfParameters() > 0) {
                    // Verificar qué servicio necesita el controlador
                    $params = $constructor->getParameters();
                    $services = [];
                    
                    foreach ($params as $param) {
                        $type = $param->getType();
                        if ($type && !$type->isBuiltin()) {
                            $typeName = $type->getName();
                            if ($typeName === 'App\\Services\\ProductoService') {
                                $services[] = new \App\Services\ProductoService();
                            } elseif ($typeName === 'App\\Services\\ApiService') {
                                $services[] = new \App\Services\ApiService();
                            }
                        }
                    }
                    
                    $controller = $reflection->newInstanceArgs($services);
                } else {
                    $controller = new $controllerClass();
                }
                
                if (method_exists($controller, $method)) {
                    // Pasar request y parámetros
                    $args = [];
                    foreach ($params as $key => $value) {
                        $args[] = $value;
                    }
                    call_user_func_array([$controller, $method], $args);
                    $matched = true;
                    break;
                }
            }
        }
    }
}

if (!$matched) {
    // Si no se encontró ninguna ruta, mostrar 404
    http_response_code(404);
    $notFoundController = new \App\Http\Controllers\NotFoundController();
    $notFoundController->index();
}


