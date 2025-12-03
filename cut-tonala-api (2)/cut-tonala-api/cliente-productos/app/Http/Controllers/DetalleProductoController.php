<?php

namespace App\Http\Controllers;

use App\Services\ProductoService;
use App\Helpers\BladeHelper;

class DetalleProductoController extends Controller
{
    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function show($id)
    {
        $producto = null;
        $error = '';
        $cargando = false;

        try {
            $producto = $this->productoService->obtenerProductoPorId($id);
        } catch (\Exception $e) {
            $error = 'Producto no encontrado';
            if (strpos($e->getMessage(), 'Connection') !== false) {
                $error = 'Error de conexión. Verifica que la API esté corriendo.';
            }
        }

        BladeHelper::render('detalle-producto', [
            'producto' => $producto,
            'error' => $error,
            'cargando' => $cargando
        ]);
    }
}


