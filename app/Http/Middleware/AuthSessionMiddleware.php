<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthApiService;
use Symfony\Component\HttpFoundation\Response;

class AuthSessionMiddleware
{
    protected $authService;

    public function __construct(AuthApiService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->authService->estaAutenticado()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página');
        }

        return $next($request);
    }
}


