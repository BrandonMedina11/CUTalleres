<?php

namespace App\Http\Controllers;

use App\Services\CarritoService;
use App\Services\ProductoApiService;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    protected $carritoService;
    protected $productoService;

    public function __construct(CarritoService $carritoService, ProductoApiService $productoService)
    {
        $this->carritoService = $carritoService;
        $this->productoService = $productoService;
    }

    /**
     * Muestra el carrito de inscripciones
     */
    public function index()
    {
        $talleres = $this->carritoService->obtener();
        
        return view('carrito', [
            'talleres' => $talleres,
            'total' => count($talleres)
        ]);
    }

    /**
     * Agrega un taller al carrito
     */
    public function agregar(Request $request)
    {
        $request->validate([
            'taller_id' => 'required|integer',
            'nombre' => 'required|string',
        ]);

        // Intentar obtener información completa del taller desde la API
        $taller = $this->productoService->obtenerProductoPorId($request->taller_id);

        // Si la API no responde, usar los datos básicos del formulario
        if (!$taller) {
            // Obtener todos los talleres para buscar el que necesitamos
            $talleres = $this->productoService->obtenerProductos();
            $tallerEncontrado = null;
            
            foreach ($talleres as $t) {
                if (isset($t['id']) && $t['id'] == $request->taller_id) {
                    $tallerEncontrado = $t;
                    break;
                }
            }
            
            // Si lo encontramos en la lista, usarlo
            if ($tallerEncontrado) {
                $taller = $tallerEncontrado;
            } else {
                // Si no lo encontramos, crear un objeto básico con los datos del formulario
                // Esto permite agregar al carrito incluso si la API falla
                $taller = [
                    'id' => $request->taller_id,
                    'nombre' => $request->nombre,
                    'descripcion' => '',
                    'categoria_nombre' => '',
                    'profesor_nombre' => '',
                    'foto' => null,
                    'foto_url' => null,
                ];
            }
        }

        // Agregar al carrito
        $this->carritoService->agregar($taller);

        return redirect()->back()->with('success', 'Taller agregado al carrito de inscripciones.');
    }

    /**
     * Elimina un taller del carrito
     */
    public function eliminar($id)
    {
        $this->carritoService->eliminar($id);
        
        return redirect()->route('carrito')->with('success', 'Taller eliminado del carrito.');
    }

    /**
     * Limpia todo el carrito
     */
    public function limpiar()
    {
        $this->carritoService->limpiar();
        
        return redirect()->route('carrito')->with('success', 'Carrito limpiado.');
    }
}

