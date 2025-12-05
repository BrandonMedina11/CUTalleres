<?php

namespace App\Http\Controllers;

use App\Services\ProductoService;
use App\Helpers\BladeHelper;

class CatalogoController extends Controller
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function index()
    {
        $productos = [];
        $cargando = false;
        $error = '';

        try {
            $productosData = $this->productoService->obtenerProductos();
            // Filtrar solo productos activos
            $productos = array_filter($productosData, function($producto) {
                return isset($producto['estado']) && $producto['estado'] === true;
            });
            $productos = array_values($productos); // Reindexar array
        } catch (\Exception $e) {
            $error = 'No se pudo cargar el catálogo';
            if (strpos($e->getMessage(), 'Connection') !== false) {
                $error = 'Error de conexión. Verifica que la API esté corriendo.';
            }
        }

        BladeHelper::render('catalogo', [
            'productos' => $productos,
            'cargando' => $cargando,
            'error' => $error
        ]);
    }
}


