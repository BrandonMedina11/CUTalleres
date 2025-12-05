<?php

namespace App\Http\Middleware;

use Closure;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verificar si el usuario tiene un token en la sesión
        if (!isset($_SESSION['api_token'])) {
            $_SESSION['errors'] = ['error' => 'Debes iniciar sesión para acceder a esta página.'];
            header('Location: ' . route('login'));
            exit;
        }

        return $next($request);
    }
}

