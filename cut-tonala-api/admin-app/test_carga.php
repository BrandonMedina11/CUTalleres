<?php
/**
 * Script de prueba para verificar que todas las clases se carguen correctamente
 */

echo "=== Prueba de Carga de Clases ===\n\n";

// Cargar autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Autoloader para clases App
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
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
$envFile = __DIR__ . '/.env';
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

// Función helper para obtener variables de entorno
if (!function_exists('env')) {
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

// Cargar helpers
require_once __DIR__ . '/app/Helpers/functions.php';

// Probar carga de clases
$classes = [
    'App\\Services\\ApiService',
    'App\\Http\\Controllers\\AuthController',
    'App\\Http\\Controllers\\TallerController',
    'App\\Http\\Controllers\\Controller',
    'App\\Http\\Middleware\\AuthMiddleware',
    'App\\Helpers\\BladeHelper',
];

echo "Probando carga de clases:\n";
foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "  ✅ $class\n";
    } else {
        echo "  ❌ $class (NO ENCONTRADA)\n";
    }
}

// Función request para pruebas
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
                return true;
            }
        };
    }
}

// Probar funciones helper
echo "\nProbando funciones helper:\n";
$functions = ['route', 'session', 'csrf_token', 'old', 'config', 'request', 'env'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "  ✅ $func()\n";
    } else {
        echo "  ❌ $func() (NO ENCONTRADA)\n";
    }
}

// Probar instanciación
echo "\nProbando instanciación:\n";
try {
    $apiService = new \App\Services\ApiService();
    echo "  ✅ ApiService instanciado\n";
    
    $authController = new \App\Http\Controllers\AuthController($apiService);
    echo "  ✅ AuthController instanciado\n";
    
    $tallerController = new \App\Http\Controllers\TallerController($apiService);
    echo "  ✅ TallerController instanciado\n";
    
    $bladeHelper = \App\Helpers\BladeHelper::class;
    echo "  ✅ BladeHelper accesible\n";
    
} catch (\Exception $e) {
    echo "  ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Prueba completada ===\n";

