<?php

namespace App\Http\Controllers;

use App\Services\AuthApiService;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    protected $authService;

    public function __construct(AuthApiService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Muestra el perfil del usuario
     */
    public function index()
    {
        if (!$this->authService->estaAutenticado()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tu perfil');
        }

        $usuario = $this->authService->obtenerPerfil();
        
        if (!$usuario) {
            return view('perfil', [
                'usuario' => null,
                'error' => 'No se pudo cargar el perfil'
            ]);
        }

        return view('perfil', [
            'usuario' => $usuario,
            'error' => null
        ]);
    }
}


