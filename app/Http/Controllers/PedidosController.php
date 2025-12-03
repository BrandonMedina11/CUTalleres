<?php

namespace App\Http\Controllers;

use App\Services\AuthApiService;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    protected $authService;

    public function __construct(AuthApiService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Muestra los pedidos del usuario autenticado
     */
    public function misPedidos()
    {
        if (!$this->authService->estaAutenticado()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos');
        }

        $pedidos = $this->authService->obtenerMisPedidos();

        return view('mis-pedidos', [
            'pedidos' => $pedidos,
            'error' => empty($pedidos) ? 'No se pudieron cargar los pedidos o no tienes pedidos registrados' : null
        ]);
    }
}


