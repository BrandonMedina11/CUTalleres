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
            
            public function validate($rules, $messages = []) {
                // Validación simplificada
                $errors = [];
                foreach ($rules as $field => $rule) {
                    $rulesArray = explode('|', $rule);
                    $value = $this->input($field);
                    
                    foreach ($rulesArray as $r) {
                        if ($r === 'required' && empty($value)) {
                            $errors[$field] = $messages[$field . '.required'] ?? "El campo {$field} es obligatorio";
                        } elseif ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = $messages[$field . '.email'] ?? "El campo {$field} debe ser un email válido";
                        } elseif (strpos($r, 'min:') === 0) {
                            $min = (int)substr($r, 4);
                            if (strlen($value) < $min) {
                                $errors[$field] = $messages[$field . '.min'] ?? "El campo {$field} debe tener al menos {$min} caracteres";
                            }
                        }
                    }
                }
                
                if (!empty($errors)) {
                    $_SESSION['errors'] = $errors;
                    $_SESSION['_old_input'] = $_POST;
                    return false;
                }
                
                return true;
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
        
        // Verificar middleware
        if (isset($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareClass = "App\\Http\\Middleware\\{$middleware}";
                if (class_exists($middlewareClass)) {
                    $middlewareInstance = new $middlewareClass();
                    $nextCalled = false;
                    $next = function() use (&$nextCalled) {
                        $nextCalled = true;
                        return true;
                    };
                    $middlewareInstance->handle(request(), $next);
                    if (!$nextCalled) {
                        return; // Middleware bloqueó la petición
                    }
                }
            }
        }
        
        // Ejecutar el controlador
        if (isset($route['controller'])) {
            list($controllerClass, $method) = explode('@', $route['controller']);
            $controllerClass = "App\\Http\\Controllers\\{$controllerClass}";
            
            if (class_exists($controllerClass)) {
                // Crear instancia de ApiService para inyectar en controladores
                $apiService = new \App\Services\ApiService();
                
                // Instanciar controlador con ApiService
                $controller = new $controllerClass($apiService);
                
                if (method_exists($controller, $method)) {
                    // Pasar request y parámetros
                    $args = [request()];
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
    http_response_code(404);
    echo "Página no encontrada";
}
