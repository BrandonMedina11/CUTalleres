<?php

if (!function_exists('route')) {
    /**
     * Generar URL para una ruta nombrada
     */
    function route($name, $params = [])
    {
        static $routes = null;
        
        if ($routes === null) {
            $routes = require __DIR__ . '/../../routes/web.php';
        }
        
        foreach ($routes as $route) {
            if (isset($route['name']) && $route['name'] === $name) {
                $url = $route['path'];
                
                // Reemplazar parámetros en la ruta
                foreach ($params as $key => $value) {
                    $url = str_replace('{' . $key . '}', $value, $url);
                }
                
                return $url;
            }
        }
        
        return '#';
    }
}

if (!function_exists('session')) {
    /**
     * Obtener o establecer valores de sesión
     */
    function session($key = null, $default = null)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if ($key === null) {
            return $_SESSION;
        }
        
        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generar token CSRF
     */
    function csrf_token()
    {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}

if (!function_exists('old')) {
    /**
     * Obtener valor anterior del formulario
     */
    function old($key, $default = null)
    {
        if (isset($_SESSION['_old_input']) && is_array($_SESSION['_old_input'])) {
            return $_SESSION['_old_input'][$key] ?? $default;
        }
        return $default;
    }
}

if (!function_exists('view')) {
    /**
     * Renderizar una vista
     */
    function view($view, $data = [])
    {
        extract($data);
        
        // Incluir helpers
        require_once __DIR__ . '/functions.php';
        
        // Renderizar la vista
        $viewPath = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $view) . '.blade.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: {$view}");
        }
        
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirigir a una URL
     */
    function redirect($url, $status = 302)
    {
        header("Location: {$url}", true, $status);
        exit;
    }
}

if (!function_exists('config')) {
    /**
     * Obtener valor de configuración
     */
    function config($key, $default = null)
    {
        static $config = [];
        
        $keys = explode('.', $key);
        $file = array_shift($keys);
        
        if (!isset($config[$file])) {
            $configPath = __DIR__ . '/../../config/' . $file . '.php';
            if (file_exists($configPath)) {
                $config[$file] = require $configPath;
            } else {
                return $default;
            }
        }
        
        $value = $config[$file];
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
}

