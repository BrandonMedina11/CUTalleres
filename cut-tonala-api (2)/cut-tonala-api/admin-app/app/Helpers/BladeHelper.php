<?php

namespace App\Helpers;

class BladeHelper
{
    /**
     * Encontrar el paréntesis de cierre correcto para una expresión
     */
    private static function findMatchingParen($str, $start = 0)
    {
        $depth = 0;
        $inString = false;
        $stringChar = null;
        
        for ($i = $start; $i < strlen($str); $i++) {
            $char = $str[$i];
            
            // Manejar strings
            if (($char === '"' || $char === "'") && ($i === 0 || $str[$i-1] !== '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                    $stringChar = null;
                }
                continue;
            }
            
            if (!$inString) {
                if ($char === '(') $depth++;
                elseif ($char === ')') {
                    $depth--;
                    if ($depth === 0) {
                        return $i;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Compilar directivas Blade básicas
     */
    public static function compile($content)
    {
        // Primero, eliminar @extends (se maneja en render)
        $content = preg_replace('/@extends\s*\([\'"](.+?)[\'"]\)/', '', $content);
        
        // Procesar @if con paréntesis anidados PRIMERO (antes de otras directivas)
        while (preg_match('/@if\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1];
            $start = $pos + strlen($matches[0][0]);
            $end = self::findMatchingParen($content, $start - 1);
            
            if ($end !== false) {
                $expr = substr($content, $start, $end - $start);
                $fullMatch = substr($content, $pos, $end + 1 - $pos);
                $replacement = '<?php if (' . $expr . '): ?>';
                $content = substr_replace($content, $replacement, $pos, strlen($fullMatch));
            } else {
                break; // No se encontró el cierre, salir del bucle
            }
        }
        
        // Procesar @elseif con paréntesis anidados
        while (preg_match('/@elseif\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1];
            $start = $pos + strlen($matches[0][0]);
            $end = self::findMatchingParen($content, $start - 1);
            
            if ($end !== false) {
                $expr = substr($content, $start, $end - $start);
                $fullMatch = substr($content, $pos, $end + 1 - $pos);
                $replacement = '<?php elseif (' . $expr . '): ?>';
                $content = substr_replace($content, $replacement, $pos, strlen($fullMatch));
            } else {
                break;
            }
        }
        
        $content = str_replace('@else', '<?php else: ?>', $content);
        $content = str_replace('@endif', '<?php endif; ?>', $content);
        
        // @csrf
        $content = str_replace('@csrf', '<?php if (function_exists("csrf_token")) { echo \'<input type="hidden" name="_token" value="\' . csrf_token() . \'">\'; } ?>', $content);
        
        // {{ }} - Echo (escapar HTML)
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/s', '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>', $content);
        
        // {!! !!} - Raw echo
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/s', '<?php echo $1; ?>', $content);
        
        // @error y @enderror
        $content = preg_replace_callback('/@error\s*\([\'"]([^\'"]+)[\'"]\)/', function($m) {
            $field = $m[1];
            return '<?php if (isset($_SESSION["errors"]) && isset($_SESSION["errors"]["' . $field . '"])) { $message = is_array($_SESSION["errors"]["' . $field . '"]) ? implode(", ", $_SESSION["errors"]["' . $field . '"]) : $_SESSION["errors"]["' . $field . '"]; ?>';
        }, $content);
        $content = str_replace('@enderror', '<?php } ?>', $content);
        
        // @foreach
        while (preg_match('/@foreach\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1];
            $start = $pos + strlen($matches[0][0]);
            $end = self::findMatchingParen($content, $start - 1);
            
            if ($end !== false) {
                $expr = substr($content, $start, $end - $start);
                $fullMatch = substr($content, $pos, $end + 1 - $pos);
                $replacement = '<?php foreach (' . $expr . '): ?>';
                $content = substr_replace($content, $replacement, $pos, strlen($fullMatch));
            } else {
                break;
            }
        }
        $content = str_replace('@endforeach', '<?php endforeach; ?>', $content);
        
        // @for
        while (preg_match('/@for\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1];
            $start = $pos + strlen($matches[0][0]);
            $end = self::findMatchingParen($content, $start - 1);
            
            if ($end !== false) {
                $expr = substr($content, $start, $end - $start);
                $fullMatch = substr($content, $pos, $end + 1 - $pos);
                $replacement = '<?php for (' . $expr . '): ?>';
                $content = substr_replace($content, $replacement, $pos, strlen($fullMatch));
            } else {
                break;
            }
        }
        $content = str_replace('@endfor', '<?php endfor; ?>', $content);
        
        // @isset
        while (preg_match('/@isset\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1];
            $start = $pos + strlen($matches[0][0]);
            $end = self::findMatchingParen($content, $start - 1);
            
            if ($end !== false) {
                $expr = substr($content, $start, $end - $start);
                $fullMatch = substr($content, $pos, $end + 1 - $pos);
                $replacement = '<?php if (isset(' . $expr . ')): ?>';
                $content = substr_replace($content, $replacement, $pos, strlen($fullMatch));
            } else {
                break;
            }
        }
        $content = str_replace('@endisset', '<?php endif; ?>', $content);
        
        // @empty
        while (preg_match('/@empty\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1];
            $start = $pos + strlen($matches[0][0]);
            $end = self::findMatchingParen($content, $start - 1);
            
            if ($end !== false) {
                $expr = substr($content, $start, $end - $start);
                $fullMatch = substr($content, $pos, $end + 1 - $pos);
                $replacement = '<?php if (empty(' . $expr . ')): ?>';
                $content = substr_replace($content, $replacement, $pos, strlen($fullMatch));
            } else {
                break;
            }
        }
        $content = str_replace('@endempty', '<?php endif; ?>', $content);
        
        // @auth
        $content = str_replace('@auth', '<?php if (isset($_SESSION["api_token"])): ?>', $content);
        $content = str_replace('@endauth', '<?php endif; ?>', $content);
        
        // @php
        $content = preg_replace('/@php\s*(.+?)\s*@endphp/s', '<?php $1 ?>', $content);
        
        // Eliminar @section y @endsection (se manejan en render)
        $content = preg_replace('/@section\s*\([^)]+\)/', '', $content);
        $content = str_replace('@endsection', '', $content);
        $content = str_replace('@stop', '', $content);
        
        return $content;
    }
    
    /**
     * Renderizar vista con layout
     */
    public static function render($view, $data = [])
    {
        // Cargar helpers primero
        if (!function_exists('route')) {
            require_once __DIR__ . '/functions.php';
        }
        
        extract($data);
        
        $viewPath = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $view) . '.blade.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("Vista no encontrada: {$view}");
        }
        
        $content = file_get_contents($viewPath);
        
        // Buscar @extends
        if (preg_match('/@extends\s*\([\'"](.+?)[\'"]\)/', $content, $matches)) {
            $layout = $matches[1];
            $layoutPath = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $layout) . '.blade.php';
            
            if (file_exists($layoutPath)) {
                $layoutContent = file_get_contents($layoutPath);
                
                // Extraer secciones de la vista ANTES de compilar
                $sections = [];
                
                // Secciones con dos parámetros: @section('title', 'value')
                if (preg_match_all('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $sections[$match[1]] = $match[2];
                    }
                }
                
                // Secciones con contenido: @section('content') ... @endsection
                if (preg_match_all('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*(.*?)@endsection/s', $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $sections[$match[1]] = trim($match[2]);
                    }
                }
                
                // Compilar layout
                $layoutCompiled = self::compile($layoutContent);
                
                // Reemplazar @yield con contenido de secciones
                foreach ($sections as $sectionName => $sectionContent) {
                    $compiledSection = self::compile($sectionContent);
                    // Escapar para uso en regex
                    $escapedName = preg_quote($sectionName, '/');
                    $layoutCompiled = preg_replace('/@yield\s*\(\s*[\'"]' . $escapedName . '[\'"]\s*\)/', $compiledSection, $layoutCompiled);
                }
                
                // Limpiar cualquier @yield restante
                $layoutCompiled = preg_replace('/@yield\s*\([^)]+\)/', '', $layoutCompiled);
                
                // Ejecutar el código compilado
                eval('?>' . $layoutCompiled);
            } else {
                // Sin layout, compilar y ejecutar directamente
                $compiled = self::compile($content);
                eval('?>' . $compiled);
            }
        } else {
            // Sin @extends, compilar y ejecutar directamente
            $compiled = self::compile($content);
            eval('?>' . $compiled);
        }
    }
}
