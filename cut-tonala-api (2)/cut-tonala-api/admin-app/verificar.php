<?php
/**
 * Script de verificación rápida
 * Ejecuta: php verificar.php
 */

echo "=== Verificación de la Aplicación Admin ===\n\n";

// 1. Verificar PHP
echo "1. Versión de PHP: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
    echo "   ✅ PHP 8.1+ detectado\n";
} else {
    echo "   ⚠️  Se requiere PHP 8.1+\n";
}

// 2. Verificar extensiones
echo "\n2. Extensiones PHP:\n";
$required = ['curl', 'json', 'mbstring', 'session'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext\n";
    } else {
        echo "   ❌ $ext (NO INSTALADA)\n";
    }
}

// 3. Verificar archivos importantes
echo "\n3. Archivos importantes:\n";
$files = [
    'public/index.php' => 'Punto de entrada',
    'app/Services/ApiService.php' => 'Servicio API',
    'app/Http/Controllers/AuthController.php' => 'Controlador Auth',
    'app/Http/Controllers/TallerController.php' => 'Controlador Talleres',
    'app/Http/Middleware/AuthMiddleware.php' => 'Middleware',
    'routes/web.php' => 'Rutas',
    'config/api.php' => 'Configuración API',
];

foreach ($files as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "   ✅ $file - $desc\n";
    } else {
        echo "   ❌ $file - $desc (NO ENCONTRADO)\n";
    }
}

// 4. Verificar vendor (composer)
echo "\n4. Dependencias:\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✅ Composer dependencies instaladas\n";
    
    // Verificar Guzzle
    if (file_exists(__DIR__ . '/vendor/guzzlehttp/guzzle/src/Client.php')) {
        echo "   ✅ Guzzle HTTP instalado\n";
    } else {
        echo "   ⚠️  Guzzle HTTP no encontrado\n";
    }
} else {
    echo "   ⚠️  Composer dependencies no instaladas\n";
    echo "      Ejecuta: composer install\n";
}

// 5. Verificar configuración
echo "\n5. Configuración:\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "   ✅ Archivo .env existe\n";
    
    $env = file_get_contents($envFile);
    if (strpos($env, 'API_BASE_URL') !== false) {
        echo "   ✅ API_BASE_URL configurado\n";
    } else {
        echo "   ⚠️  API_BASE_URL no encontrado en .env\n";
    }
} else {
    echo "   ⚠️  Archivo .env no existe\n";
    echo "      Crea uno basado en .env.example\n";
}

// 6. Verificar vistas
echo "\n6. Vistas:\n";
$views = [
    'resources/views/layouts/app.blade.php',
    'resources/views/auth/login.blade.php',
    'resources/views/talleres/index.blade.php',
    'resources/views/talleres/show.blade.php',
];

foreach ($views as $view) {
    $path = __DIR__ . '/' . $view;
    if (file_exists($path)) {
        echo "   ✅ $view\n";
    } else {
        echo "   ❌ $view (NO ENCONTRADO)\n";
    }
}

// 7. Test de conexión a API (opcional)
echo "\n7. Conexión a API:\n";
$apiUrl = 'http://localhost:3000';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
$response = @curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 || $httpCode == 0) {
    echo "   ✅ API accesible en $apiUrl\n";
} else {
    echo "   ⚠️  API no accesible en $apiUrl\n";
    echo "      Asegúrate de que la API esté corriendo: npm start\n";
}

echo "\n=== Verificación completada ===\n";
echo "\nPara iniciar el servidor:\n";
echo "  cd admin-app/public\n";
echo "  php -S localhost:8000\n";
echo "\nLuego abre: http://localhost:8000\n";

