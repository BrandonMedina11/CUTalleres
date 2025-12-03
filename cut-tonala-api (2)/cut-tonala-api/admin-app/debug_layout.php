<?php

require_once __DIR__ . '/app/Helpers/BladeHelper.php';
require_once __DIR__ . '/app/Helpers/functions.php';

use App\Helpers\BladeHelper;

// Cargar el layout
$layoutPath = __DIR__ . '/resources/views/layouts/app.blade.php';
$layoutContent = file_get_contents($layoutPath);

echo "=== COMPILANDO LAYOUT ===\n";
$compiled = BladeHelper::compile($layoutContent);

// Mostrar líneas alrededor de la 43
$lines = explode("\n", $compiled);
echo "\n=== LÍNEAS 40-50 ===\n";
for ($i = 39; $i < 50 && $i < count($lines); $i++) {
    echo ($i + 1) . ": " . $lines[$i] . "\n";
}

// Buscar problemas
echo "\n=== BUSCANDO PROBLEMAS DE SINTAXIS ===\n";
foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    // Buscar patrones problemáticos
    if (preg_match('/:\s*:/', $line)) {
        echo "Línea $lineNum: Doble dos puntos: " . trim($line) . "\n";
    }
    if (preg_match('/if\s*\([^)]*:\s*[^)]*\)/', $line)) {
        echo "Línea $lineNum: Dos puntos dentro de if: " . trim($line) . "\n";
    }
    if (preg_match('/foreach\s*\([^)]*:\s*[^)]*\)/', $line)) {
        echo "Línea $lineNum: Dos puntos dentro de foreach: " . trim($line) . "\n";
    }
}

// Intentar encontrar la línea 43 exacta
echo "\n=== LÍNEA 43 EXACTA ===\n";
if (isset($lines[42])) {
    echo $lines[42] . "\n";
    // Analizar caracteres
    $chars = str_split($lines[42]);
    foreach ($chars as $pos => $char) {
        if ($char === ':') {
            echo "Dos puntos encontrado en posición $pos\n";
        }
    }
}

