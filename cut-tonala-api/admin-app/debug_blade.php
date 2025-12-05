<?php

require_once __DIR__ . '/app/Helpers/BladeHelper.php';
require_once __DIR__ . '/app/Helpers/functions.php';

use App\Helpers\BladeHelper;

// Cargar la vista de login
$viewPath = __DIR__ . '/resources/views/auth/login.blade.php';
$content = file_get_contents($viewPath);

echo "=== CONTENIDO ORIGINAL ===\n";
echo substr($content, 0, 500) . "\n\n";

// Extraer secciones
$sections = [];
if (preg_match_all('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $content, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $sections[$match[1]] = $match[2];
        echo "Sección encontrada: {$match[1]} = {$match[2]}\n";
    }
}

if (preg_match_all('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*(.*?)@endsection/s', $content, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $sections[$match[1]] = trim($match[2]);
        echo "Sección con contenido: {$match[1]}\n";
        echo "Contenido (primeros 200 chars): " . substr($match[2], 0, 200) . "\n";
    }
}

// Compilar una sección
if (isset($sections['content'])) {
    echo "\n=== COMPILANDO SECCIÓN CONTENT ===\n";
    $compiled = BladeHelper::compile($sections['content']);
    echo "Compilado (primeros 500 chars):\n";
    echo substr($compiled, 0, 500) . "\n";
    
    // Buscar líneas problemáticas
    $lines = explode("\n", $compiled);
    echo "\n=== LÍNEA 43 (si existe) ===\n";
    if (isset($lines[42])) {
        echo "Línea 43: " . $lines[42] . "\n";
    }
    
    // Buscar problemas de sintaxis
    echo "\n=== BUSCANDO PROBLEMAS ===\n";
    foreach ($lines as $i => $line) {
        if (preg_match('/:\s*:/', $line) || preg_match('/if\s*\([^)]*:\s*[^)]*\)/', $line)) {
            echo "Línea " . ($i + 1) . " podría ser problemática: " . trim($line) . "\n";
        }
    }
}

